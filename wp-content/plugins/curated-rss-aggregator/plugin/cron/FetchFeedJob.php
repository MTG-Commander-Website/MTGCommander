<?php

namespace com\cminds\rssaggregator\plugin\cron;

use com\cminds\rssaggregator\App;
use com\cminds\rssaggregator\plugin\taxonomies\LinkTaxonomy;
use com\cminds\rssaggregator\plugin\taxonomies\CategoryTaxonomy;
use com\cminds\rssaggregator\plugin\misc\Misc;
use com\cminds\rssaggregator\plugin\options\Options;
use com\cminds\rssaggregator\plugin\helpers\ConditionalEchoHelper as Dbg;

class FetchFeedJob {

    private $termId;
    private $feedUrls;
    private $deleteAfter;
    private $startTime;
    private $config;
    private $userAgent;

    public function __construct( $term_id ) {
        Dbg::sprintf( 'PHP version: %s', phpversion() );
        Dbg::sprintf( 'Starting fetching feed job for category: %d', $term_id );
        $this->termId = $term_id;
        $this->config = array();
        if ( !$this->init() ) {
            Dbg::sprintf( 'Initialization failed, fetching stopped' );
            return;
        }
        if ( $this->isRefreshForced() ) {
            Dbg::sprintf( 'Refresh all RSS links detected, fetching stopped' );
            return;
        }
        foreach ( $this->feedUrls as $url ) {
            $this->config[ 'feed_url' ] = $url;
            $res                        = parse_url( $url );
            if ( $res === FALSE ) {
                Dbg::sprintf( 'Invalid feed url: %s', $url );
                continue;
            }
            if ( empty( $res[ 'scheme' ] ) || !$res[ 'scheme' ] ) {
                $res[ 'scheme' ] = 'http';
            }
            $this->config[ 'feed_url_domain' ] = sprintf( '%s://%s', $res[ 'scheme' ], $res[ 'host' ] );
            $this->fetch( $url );
        }
        Dbg::sprintf( 'Fetching feed job finished' );
    }

    public function wp_feed_cache_transient_lifetime( $arg ) {
        return 30;
    }

    public function https_ssl_verify( $arg ) {
        return FALSE;
    }

    private function init() {
        $this->startTime = time();

        $this->feedUrls = preg_split( '/[\n]+/', get_term_meta( $this->termId, sprintf( '%s_feed_url', App::PREFIX ), TRUE ) );
        if ( count( $this->feedUrls ) == 0 ) {
            Dbg::sprintf( 'No feeds urls found' );
            return FALSE;
        }

        if ( !class_exists( 'SimplePie', false ) ) {
            Dbg::sprintf( 'Loading SimplePie class' );
            require_once( ABSPATH . WPINC . '/class-simplepie.php' );
        }
        $this->config[ 'subtitle_namespace' ] = get_term_meta( $this->termId, sprintf( '%s_advanced_subtitle_namespace', App::PREFIX ), TRUE );
        if ( defined( $this->config[ 'subtitle_namespace' ] ) ) {
            $this->config[ 'subtitle_namespace' ] = constant( $this->config[ 'subtitle_namespace' ] );
        }
        $this->config[ 'subtitle_tag' ]          = get_term_meta( $this->termId, sprintf( '%s_advanced_subtitle_tag', App::PREFIX ), TRUE );
        $this->config[ 'is_custom_subtitle' ]    = strlen( $this->config[ 'subtitle_tag' ] ) > 0;
        $this->config[ 'is_fix_relative_paths' ] = get_term_meta( $this->termId, sprintf( '%s_fix_relative_paths', App::PREFIX ), TRUE );

        $this->deleteAfter = intval( get_term_meta( $this->termId, sprintf( '%s_delete_after', App::PREFIX ), TRUE ) );


        $this->userAgent = get_term_meta( $this->termId, sprintf( '%s_user_agent', App::PREFIX ), TRUE );
        if ( empty( $this->userAgent ) ) {
            $this->userAgent = Options::getOption( 'user_agent' );
        }
        if ( $this->userAgent == 'WP' ) {
            $this->userAgent = NULL;
        }
        if ( !empty( $this->userAgent ) ) {
            Dbg::sprintf( 'User Agent: %s', $this->userAgent );
        }

        if(get_term_meta( $this->termId, sprintf( '%s_always_refresh_links', App::PREFIX ), TRUE )){
            CategoryTaxonomy::RefreshAllLinksForCategory($this->termId, false, true);
        }

        return TRUE;
    }

    private function fetch( $url ) {
        Dbg::sprintf( 'Starting fetching: %s', $url );
        add_filter( 'wp_feed_cache_transient_lifetime', [$this, 'wp_feed_cache_transient_lifetime' ] );
        add_filter( 'https_ssl_verify', [$this, 'https_ssl_verify' ] );
        add_filter( 'wp_feed_options', function($feed, $url) {
            if ( !empty( $this->userAgent ) ) {
                $feed->set_useragent( $this->userAgent );
            }
        }, 10, 2 );
        $feed = fetch_feed( $url );
        remove_filter( 'wp_feed_cache_transient_lifetime', [$this, 'wp_feed_cache_transient_lifetime' ] );
        remove_filter( 'https_ssl_verify', [$this, 'https_ssl_verify' ] );
        if ( is_wp_error( $feed ) ) {
            Dbg::sprintf( sprintf( "%s at line %s: %s", __FILE__, __LINE__, $feed->get_error_message() ) );
            error_log( sprintf( "%s at line %s: %s", __FILE__, __LINE__, $feed->get_error_message() ) );
            add_filter( 'https_ssl_verify', [$this, 'https_ssl_verify' ] );
            $response = wp_remote_get( $url, ['timeout' => 15 ] );
            remove_filter( 'https_ssl_verify', [$this, 'https_ssl_verify' ] );
            if ( is_array( $response ) ) {
                Dbg::sprintf( 'Feed content: %s', htmlspecialchars( substr( $response[ 'body' ], 0, 1000 ) ) );
            }
            return;
        }
        Dbg::sprintf( 'Feed fetched' );
        foreach ( $feed->get_items() as $item ) {
            Dbg::sprintf( 'Starting processing feed item: %s', $item->get_id() );
            if ( $this->isOutdated( $item ) ) {
                Dbg::sprintf( 'Feed item rejected (outdated)' );
                continue;
            }
            
            $term = LinkTaxonomy::fromSimplePieItem( $item, $this->termId, $this->config );
            if ( is_wp_error( $term ) ) {
                Dbg::sprintf( sprintf( "%s at line %s: %s", __FILE__, __LINE__, $term->get_error_message() ) );
                error_log( sprintf( "%s at line %s: %s", __FILE__, __LINE__, $term->get_error_message() ) );
                continue;
            }
            if ( $this->isRefreshForced() ) {
                Dbg::sprintf( 'Refresh all RSS links detected, RSS link abandoned' );
                update_term_meta( $term->term_id, sprintf( '%s_category', App::PREFIX ), '-1' );
                return;
            }

        }
    }

    private function isRefreshForced() {
        $time = intval( get_term_meta( $this->termId, sprintf( '%s_refresh', App::PREFIX ), TRUE ) );
        if ( !$time ) {
            return FALSE;
        }
        return $time >= $this->startTime;
    }

    private function isOutdated( $item ) {
        if ( !$this->deleteAfter ) {
            return FALSE;
        }
        $time = $item->get_date( 'U' ) ? : time();
        if ( $time < time() - $this->deleteAfter ) {
            return TRUE;
        }
        return FALSE;
    }

}
