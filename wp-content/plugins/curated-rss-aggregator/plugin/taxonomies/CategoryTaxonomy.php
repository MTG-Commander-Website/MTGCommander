<?php

namespace com\cminds\rssaggregator\plugin\taxonomies;

use com\cminds\rssaggregator\App;
use com\cminds\rssaggregator\plugin\taxonomies\ListTaxonomy;
use com\cminds\rssaggregator\plugin\helpers\ViewHelper;
use com\cminds\rssaggregator\plugin\misc\Misc;
use com\cminds\rssaggregator\plugin\helpers\HTMLHelper;
use com\cminds\rssaggregator\plugin\cron\FeedJobManager;

class CategoryTaxonomy extends TaxonomyAbstract {

    const TAXONOMY = 'cmra_category';

    public function __construct() {
        parent::__construct();
        add_action( 'admin_menu', array( $this, 'actionAdminMenu' ) );
        add_action( 'admin_head', array( $this, 'actionAdminHead' ) );
        add_action( sprintf( 'wp_ajax_%s_refresh', static::TAXONOMY ), array( $this, 'actionWpAjaxRefresh' ) );
        add_action( sprintf( '%s_add_form_fields', static::TAXONOMY ), array( $this, 'actionAddFormFields' ) );
        add_action( sprintf( '%s_edit_form_fields', static::TAXONOMY ), array( $this, 'actionEditFormFields' ) );
        add_action( sprintf( 'create_%s', static::TAXONOMY ), array( $this, 'actionCreate' ) );
        add_action( sprintf( 'edited_%s', static::TAXONOMY ), array( $this, 'actionEdited' ) );
        add_action( sprintf( 'delete_%s', static::TAXONOMY ), array( $this, 'actionDelete' ) );
        add_filter( sprintf( '%s_row_actions', static::TAXONOMY ), array( $this, 'filterRowActions' ), 10, 2 );
        add_filter( sprintf( 'manage_edit-%s_columns', static::TAXONOMY ), array( $this, 'filterManageColumns' ) );
        add_filter( sprintf( 'manage_%s_custom_column', static::TAXONOMY ), array( $this, 'filterManageCustomColumn' ), 10, 3 );
    }

    public function actionInit() {
        parent::actionInit();
        register_taxonomy( self::TAXONOMY, NULL, array(
            'label'             => 'Categories',
            'show_ui'           => TRUE,
            'show_admin_column' => TRUE,
            'hierarchical'      => TRUE
        ) );
        wp_register_script( 'cmra-backend-category-taxonomy', plugin_dir_url( App::PLUGIN_FILE ) . 'assets/backend/js/category-taxonomy.js', array( 'jquery', 'common' ), App::VERSION );
    }

    public function actionAdminMenu() {
        add_submenu_page( App::SLUG, 'Categories', 'Categories', 'manage_options', 'edit-tags.php?taxonomy=cmra_category' );
    }

    public function actionAdminHead() {
        if ( get_current_screen()->taxonomy == static::TAXONOMY ) {
            HTMLHelper::enqueueInputColorAssets();
            //echo "<style>.form-field.term-slug-wrap{ display: none !important; }</style>\n";
            //echo "<style>.inline-edit-col label:nth-child(2){ display: none !important; }</style>\n";
        }
    }

    public function actionAdminEnqueueScripts() {
        if ( get_current_screen()->taxonomy == static::TAXONOMY ) {
            wp_enqueue_script( 'cmra-backend-category-taxonomy' );
            wp_enqueue_style( 'cmra-backend-admin' );
        }
    }

    public static function actionWpAjaxRefresh() {
        $term_id = intval( filter_input( INPUT_POST, 'term_id' ) );
        if ( $term_id <= 0 ) {
            wp_send_json( array( 'status' => FALSE ) );
        }
        wp_send_json( array( 'status' => static::RefreshAllLinksForCategory( $term_id ) ) );
    }

    public function actionAddFormFields() {
        echo ViewHelper::load( 'views/backend/taxonomies/category/add_form_fields.php' );
    }

    public function actionEditFormFields( $term ) {
        echo ViewHelper::load( 'views/backend/taxonomies/category/edit_form_fields.php', array(
            'list_id_arr'                 => get_term_meta( $term->term_id, sprintf( '%s_list', App::PREFIX ) ),
            'show_favicons'               => get_term_meta( $term->term_id, sprintf( '%s_show_favicons', App::PREFIX ), TRUE ),
            'bg_color'                    => get_term_meta( $term->term_id, sprintf( '%s_bg_color', App::PREFIX ), TRUE ),
            'interval'                    => get_term_meta( $term->term_id, sprintf( '%s_interval', App::PREFIX ), TRUE ),
            'feed_url'                    => get_term_meta( $term->term_id, sprintf( '%s_feed_url', App::PREFIX ), TRUE ),
            'feed_name'                   => get_term_meta( $term->term_id, sprintf( '%s_feed_name', App::PREFIX ), TRUE ),
            'delete_after'                => get_term_meta( $term->term_id, sprintf( '%s_delete_after', App::PREFIX ), TRUE ),
            'advanced_subtitle_namespace' => get_term_meta( $term->term_id, sprintf( '%s_advanced_subtitle_namespace', App::PREFIX ), TRUE ),
            'advanced_subtitle_tag'       => get_term_meta( $term->term_id, sprintf( '%s_advanced_subtitle_tag', App::PREFIX ), TRUE ),
            'always_refresh_links'        => get_term_meta( $term->term_id, sprintf( '%s_always_refresh_links', App::PREFIX ), TRUE ),
            'fix_relative_paths'          => get_term_meta( $term->term_id, sprintf( '%s_fix_relative_paths', App::PREFIX ), TRUE ),
            'user_agent'                  => get_term_meta( $term->term_id, sprintf( '%s_user_agent', App::PREFIX ), TRUE )
        ) );
    }

    public function actionCreate( $term_id ) {
        $key = sprintf( '%s_list', App::PREFIX );
        if ( isset( $_POST[ 'tax_input' ] ) && is_array( $_POST[ 'tax_input' ][ $key ] ) ) {
            $value = $_POST[ 'tax_input' ][ $key ];
            Misc::update_term_meta_array( $term_id, $key, $value );
        } else {
            if ( filter_input( INPUT_POST, 'action' ) == 'editedtag' ) {
                delete_term_meta( $term_id, $key );
            }
        }
        $keys = array(
            sprintf( '%s_show_favicons', App::PREFIX ),
            sprintf( '%s_bg_color', App::PREFIX ),
            sprintf( '%s_interval', App::PREFIX ),
            sprintf( '%s_feed_url', App::PREFIX ),
            sprintf( '%s_feed_name', App::PREFIX ),
            sprintf( '%s_delete_after', App::PREFIX ),
            sprintf( '%s_always_refresh_links', App::PREFIX ),
            sprintf( '%s_advanced_subtitle_namespace', App::PREFIX ),
            sprintf( '%s_advanced_subtitle_tag', App::PREFIX ),
            sprintf( '%s_fix_relative_paths', App::PREFIX ),
            sprintf( '%s_user_agent', App::PREFIX ),
        );
        foreach ( $keys as $key ) {
            $value = filter_input( INPUT_POST, $key );
            if ( $value !== NULL ) {
                update_term_meta( $term_id, $key, $value );
            };
        }
        if ( filter_input( INPUT_POST, sprintf( '%s_refresh', App::PREFIX ) ) ) {
            static::RefreshAllLinksForCategory( $term_id );
        } else {
            FeedJobManager::scheduleEvent( $term_id );
        }
    }

    public function actionEdited( $term_id ) {
        return $this->actionCreate( $term_id );
    }

    public function actionDelete( $term_id ) {
        FeedJobManager::clearScheduledHook( $term_id );
        $terms = get_terms( LinkTaxonomy::TAXONOMY, array(
            'hide_empty' => FALSE,
            'meta_query' => array(
                array(
                    'key'     => sprintf( '%s_category', App::PREFIX ),
                    'value'   => $term_id,
                    'compare' => '='
                )
            )
        ) );
        foreach ( (array) $terms as $term ) {
            wp_delete_term( $term->term_id, LinkTaxonomy::TAXONOMY );
        }
    }

    public function filterRowActions( $actions, $tag ) {
        $actions[ 'refresh' ] = '<a href="javascript:void(0)" class="cmra-row-action-refresh" data-term-id="' . $tag->term_id . '">Refresh all RSS links</a>';
        unset( $actions[ 'view' ] );
        return $actions;
    }

    public function filterManageColumns( $columns ) {
        unset( $columns[ 'posts' ] );
        //unset($columns['slug']);
        unset( $columns[ 'description' ] );
        // $columns[ sprintf( '%s_interval', App::PREFIX ) ] = 'Processing interval';
        $columns[ sprintf( '%s_list', App::PREFIX ) ]     = 'Lists';
        // $columns[ sprintf( '%s_bg_color', App::PREFIX ) ] = 'Background color';
        $columns[ sprintf( '%s_feed_url', App::PREFIX ) ] = 'Feed URLs';
        return $columns;
    }

    public function filterManageCustomColumn( $out, $column_name, $term_id ) {
        if ( $column_name === sprintf( '%s_interval', App::PREFIX ) ) {
            $arr = wp_get_schedules();
            $res = get_term_meta( $term_id, $column_name, TRUE );
            return isset( $arr[ $res ] ) ? $arr[ $res ][ 'display' ] : 'None';
        }
        if ( $column_name === sprintf( '%s_bg_color', App::PREFIX ) ) {
            $color = get_term_meta( $term_id, $column_name, TRUE );
            return sprintf( '<span style="background: %s" class="cmra-admin-color">%s</span>', $color, $color );
        }
        if ( $column_name === sprintf( '%s_list', App::PREFIX ) ) {
            $arr_id = get_term_meta( $term_id, $column_name );
            if ( !$arr_id ) {
                return;
            }
            $items = get_terms( ListTaxonomy::TAXONOMY, array(
                'hide_empty'   => FALSE,
                'hierarchical' => FALSE,
                'include'      => implode( ',', $arr_id )
            ) );
            echo implode( ', ', array_map( function($item) {
                return $item->name;
            }, $items ) );
        }
        if ( $column_name === sprintf( '%s_feed_url', App::PREFIX ) ) {
            $url  = get_term_meta( $term_id, sprintf( '%s_feed_url', App::PREFIX ), TRUE );
            $name = get_term_meta( $term_id, sprintf( '%s_feed_name', App::PREFIX ), TRUE );
            if ( !strlen( $url ) ) {
                return;
            }
            $items = preg_split( '/[\n]+/', $url );
            $s     = '';
            foreach ( $items as $item ) {
                $s .= sprintf( '<div class="cmra-admin-link"><a href="%s" title="%s" target="_blank">%s</a></div>', $item, esc_html( $item ), esc_html( $item ) );
            }
            return $s;
        }
    }

    public static function RefreshAllLinksForCategory( $term_id, $reschedule = true, $silent = false ) {
        global $wpdb;

        if ( !$silent ) {
            update_term_meta( $term_id, sprintf( '%s_refresh', App::PREFIX ), time() );
        }

        $sql = "update $wpdb->termmeta tm join $wpdb->term_taxonomy  tt"
        . " on tm.term_id = tt.term_id"
        . " set tm.meta_value = -1"
        . " where tm.meta_value = %d and tm.meta_key = %s and tt.taxonomy = %s";

        $wpdb->query( $wpdb->prepare( $sql, $term_id, sprintf( '%s_category', App::PREFIX ), LinkTaxonomy::TAXONOMY ) );

        if ( $reschedule ) {
            FeedJobManager::scheduleEvent( $term_id, time() + 3 );
        }
        return TRUE;
    }

}
