<?php

namespace com\cminds\rssaggregator\plugin\shortcodes;

use com\cminds\rssaggregator\App;
use com\cminds\rssaggregator\plugin\helpers\ViewHelper;
use com\cminds\rssaggregator\plugin\taxonomies\ListTaxonomy;
use com\cminds\rssaggregator\plugin\taxonomies\CategoryTaxonomy;
use com\cminds\rssaggregator\plugin\options\Options;

class Shortcode {

    const SHORTCODE = 'cm_rss_aggregator';

    public function __construct() {
        add_action( 'init', array( $this, 'actionInit' ) );
        add_action( 'init', array( $this, 'actionAddShortcode' ) );
        add_action( 'wp_head', array( $this, 'preRenderStyles' ), 1 );
    }

    public function actionInit() {
        wp_register_style( 'opentip', plugin_dir_url( App::PLUGIN_FILE ) . 'assets/frontend/css/opentip.css', array(), App::VERSION );
        wp_register_style( 'cmra-frontend', plugin_dir_url( App::PLUGIN_FILE ) . 'assets/frontend/css/frontend.css', array( 'opentip' ), App::VERSION );
        wp_register_script( 'masonry', plugin_dir_url( App::PLUGIN_FILE ) . 'assets/frontend/js/masonry.pkgd.min.js', array( 'jquery' ), App::VERSION );
        wp_register_script( 'opentip', plugin_dir_url( App::PLUGIN_FILE ) . 'assets/frontend/js/opentip-jquery.min.js', array( 'jquery' ), App::VERSION );
        wp_register_script( 'highlight', plugin_dir_url( App::PLUGIN_FILE ) . 'assets/frontend/js/jquery.highlight.js', array( 'jquery' ), App::VERSION );
        wp_register_script( 'cmra-frontend', plugin_dir_url( App::PLUGIN_FILE ) . 'assets/frontend/js/frontend.js', array( 'jquery', 'masonry', 'opentip', 'highlight' ), App::VERSION );
        wp_localize_script( 'cmra-frontend', 'cmraOptions', array(
            'columnsCount'            => Options::getOption( 'columns_count' ),
            //'showCheckboxes' => Options::getOption('show_checkboxes'),
            'tooltipBackgroundColor'  => Options::getOption( 'tooltip_background_color' ),
            'tooltipBorderColor'      => Options::getOption( 'tooltip_border_color' ),
            'user_display_categories' => Options::getOption( 'cmra_user_display_categories' )
        ) );
    }

    public function preRenderStyles() {
        $currentPost = get_post();

        $seo = doing_action( 'wpseo_head' );
        if ( $seo ) {
            return;
        }

	    wp_add_inline_style( 'cmra-frontend', ViewHelper::load( 'views/frontend/shortcodes/inline_css.php' ) );

        if ( has_shortcode( $currentPost->post_content, static::SHORTCODE ) ) {
            wp_enqueue_style( 'cmra-frontend' );
            wp_add_inline_style( 'cmra-frontend', ViewHelper::load( 'views/frontend/shortcodes/inline_css.php' ) );
        }
    }

    public function actionAddShortcode() {
        // if ( $GLOBALS[ sprintf( '%s_isLicenseOk', App::SLUG ) ] || filter_input( INPUT_COOKIE, 'FOR_DEVELOPMENT_USE_ONLY_CMRA_PRO' ) ) {
            add_shortcode( static::SHORTCODE, array( $this, 'shortcode' ) );
        // }
    }

    public function shortcode( $atts ) {
        $atts = shortcode_atts( array(
            'list'              => NULL,
            'category'          => NULL,
            'tag'               => NULL,
            'category_id'       => NULL,
            'max_links'         => NULL,
            'max_height'        => NULL,
            'categories_filter' => '1' ), $atts );
        // extra shot (widget)
        if ( $atts[ 'category_id' ] ) {
            return $this->renderSingle( $atts );
        }
        // standard path
        if ( !strlen( $atts[ 'list' ] ) && !strlen( $atts[ 'category' ] ) && !strlen( $atts[ 'tag' ] ) ) {
            //return $this->error('list, category or tag attribute is required');
        }

        $atts[ 'list_term_id_arr' ] = $this->stringArrToTermIdArr( explode( ',', $atts[ 'list' ] ), ListTaxonomy::TAXONOMY, 'list <code>%s</code> not found' );
        if ( is_wp_error( $atts[ 'list_term_id_arr' ] ) ) {
            return $this->error( $atts[ 'list_term_id_arr' ]->get_error_message() );
        }
        $atts[ 'category_term_id_arr' ] = $this->stringArrToTermIdArr( explode( ',', $atts[ 'category' ] ), CategoryTaxonomy::TAXONOMY, 'category <code>%s</code> not found' );
        if ( is_wp_error( $atts[ 'category_term_id_arr' ] ) ) {
            return $this->error( $atts[ 'category_term_id_arr' ]->get_error_message() );
        }

        if ( count( $atts[ 'list_term_id_arr' ] ) == 0 &&
        count( $atts[ 'category_term_id_arr' ] ) == 1 &&
        count( $atts[ 'tag_term_id_arr' ] ) == 0 ) {
            return $this->renderSingle( $atts );
        }
        return $this->render( $atts );
    }

    private function stringArrToTermIdArr( $slugs, $taxonomy, $error_format ) {
        $arr = array();
        foreach ( $slugs as $slug ) {
            $slug = trim( $slug );
            if ( !strlen( $slug ) ) {
                continue;
            }
            $res = get_term_by( 'slug', $slug, $taxonomy );
            if ( !$res ) {
                $res = get_term_by( 'name', $slug, $taxonomy );
            }
            if ( !$res ) {
                return new \WP_Error( 1, sprintf( $error_format, $slug ) );
            }
            $arr [] = $res->term_id;
        }
        return $arr;
    }

    private function renderSingle( $atts ) {
        if ( $atts[ 'category_id' ] ) {
            $cat_term = get_term_by( 'term_id', $atts[ 'category_id' ], CategoryTaxonomy::TAXONOMY );
        } else {
            $cat_term = get_term_by( 'slug', $atts[ 'category' ], CategoryTaxonomy::TAXONOMY );
            if ( !$cat_term ) {
                $cat_term = get_term_by( 'name', $atts[ 'category' ], CategoryTaxonomy::TAXONOMY );
            }
        }
        if ( !$cat_term ) {
            return $this->error( "category <code>{$atts[ 'category' ]}</code> not found" );
        }


        wp_enqueue_style( 'cmra-frontend' );
        wp_enqueue_script( 'cmra-frontend' );

        return ViewHelper::load( 'views/frontend/shortcodes/shortcode2.php', array(
            'category_term' => $cat_term,
            'max_links'     => $atts[ 'max_links' ],
            'max_height'    => intval( $atts[ 'max_height' ] )
        ) );
    }

    private function render( $atts ) {


        wp_enqueue_style( 'cmra-frontend' );
        wp_enqueue_script( 'cmra-frontend' );

        return ViewHelper::load( 'views/frontend/shortcodes/shortcode.php', array(
            'list_term_id_arr'     => $atts[ 'list_term_id_arr' ],
            'category_term_id_arr' => $atts[ 'category_term_id_arr' ],
            'tag_term_id_arr'      => $atts[ 'tag_term_id_arr' ],
            'max_links'            => $atts[ 'max_links' ],
            'max_height'           => intval( $atts[ 'max_height' ] ),
            'categories_filter'    => $atts[ 'categories_filter' ]
        ) );
    }

    private function error( $s ) {
        return ViewHelper::load( 'views/frontend/shortcodes/error.php', array( 'message' => $s ) );
    }

}
