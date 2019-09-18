<?php

namespace com\cminds\rssaggregator\plugin\taxonomies;

use com\cminds\rssaggregator\App;
use com\cminds\rssaggregator\plugin\taxonomies\CategoryTaxonomy;
use com\cminds\rssaggregator\plugin\helpers\ViewHelper;
use com\cminds\rssaggregator\plugin\notices\AdminNoticeManager;
use com\cminds\rssaggregator\plugin\notices\AdminNotice;
use com\cminds\rssaggregator\plugin\options\Options;
use com\cminds\rssaggregator\plugin\misc\Misc;
use com\cminds\rssaggregator\plugin\cron\DeleteAbandonedJob;
use com\cminds\rssaggregator\plugin\helpers\SimplePieHelper;
use com\cminds\rssaggregator\plugin\helpers\ConditionalEchoHelper as Dbg;

class LinkTaxonomy extends TaxonomyAbstract {

    const TAXONOMY = 'cmra_link';

    public function __construct() {
        parent::__construct();
        add_action( 'admin_head', array( $this, 'actionAdminHead' ) );
        add_action( 'current_screen', array( $this, 'actionCurrentScreen' ) );
        add_action( sprintf( '%s_add_form_fields', static::TAXONOMY ), array( $this, 'actionAddFormFields' ) );
        add_action( sprintf( '%s_edit_form_fields', static::TAXONOMY ), array( $this, 'actionEditFormFields' ) );
        add_action( sprintf( 'create_%s', static::TAXONOMY ), array( $this, 'actionCreate' ) );
        add_action( sprintf( 'edited_%s', static::TAXONOMY ), array( $this, 'actionEdited' ) );
        add_action( 'pre_delete_term', array( $this, 'actionPreDeleteTerm' ), 10, 2 );
        add_action( 'edit_terms', array( $this, 'actionEditTerms' ), 10, 2 );
        add_action( 'quick_edit_custom_box', array( $this, 'actionQuickEditCustomBox' ), 10, 3 );
        add_filter( sprintf( 'manage_edit-%s_columns', static::TAXONOMY ), array( $this, 'filterManageColumns' ) );
        add_filter( sprintf( 'manage_%s_custom_column', static::TAXONOMY ), array( $this, 'filterManageCustomColumn' ), 10, 3 );
        add_filter( sprintf( '%s_row_actions', static::TAXONOMY ), array( $this, 'filterRowActions' ), 10, 2 );
        add_filter( 'get_terms_defaults', array( $this, 'filterGetTermsDefaults' ), 10, 2 );
    }

    public static function fromSimplePieItem( $item, $term_id, $config = array() ) {
        $term = static::getSimplePieItem( $item, $term_id );
        wp_update_term( $term->term_id, static::TAXONOMY, array(
            'name'        => wp_encode_emoji( wp_strip_all_tags( $item->get_title() ) ),
            'description' => wp_encode_emoji( wp_strip_all_tags( $item->get_description() ) )
        ) );
        update_term_meta( $term->term_id, sprintf( '%s_url', App::PREFIX ), $item->get_permalink() );
        if ( !empty( $config[ 'feed_url' ] ) ) {
            update_term_meta( $term->term_id, sprintf( '%s_feed_url', App::PREFIX ), $config[ 'feed_url' ] );
        }
        $feed_title = $item->feed->get_title();
        update_term_meta( $term->term_id, sprintf( '%s_feed_title', App::PREFIX ), wp_encode_emoji( wp_strip_all_tags( $feed_title ) ) );

        $subtitle = $item->get_description();
        if ( $config[ 'is_custom_subtitle' ] ) {
            $data = $item->get_item_tags( $config[ 'subtitle_namespace' ], $config[ 'subtitle_tag' ] );
            if ( is_array( $data ) && isset( $data[ 0 ][ 'data' ] ) ) {
                $subtitle = $data[ 0 ][ 'data' ];
            }
        }
        update_term_meta( $term->term_id, sprintf( '%s_subtitle', App::PREFIX ), wp_encode_emoji( wp_strip_all_tags( $subtitle ) ) );

        update_term_meta( $term->term_id, sprintf( '%s_create_time', App::PREFIX ), $item->get_date( 'U' ) ? : time() );
        update_term_meta( $term->term_id, sprintf( '%s_edit_time', App::PREFIX ), $item->get_date( 'U' ) ? : time() );

        /*
         * New method of grabbing image (same as in RSS importer
         */
        $img_url               = SimplePieHelper::getFirstImgSrc( $item, $config[ 'feed_url' ] );

        if ( strlen( $img_url ) && $config[ 'is_fix_relative_paths' ] && !preg_match( '/^http|^\/\//', $img_url ) ) {
            if ( strpos( $img_url, '/' ) !== 0 ) {
                $img_url = '/' . $img_url;
            }
            $img_url = $config[ 'feed_url_domain' ] . $img_url;
        }
        update_term_meta( $term->term_id, sprintf( '%s_image_url', App::PREFIX ), $img_url );
        return $term;
    }

    private static function getSimplePieItem( $item, $term_id ) {
        $term  = NULL;
        $terms = get_terms( static::TAXONOMY, array(
            'hide_empty' => FALSE,
            'meta_query' => array(
                'relation' => 'AND',
                array(
                    'key'     => sprintf( '%s_feed_id', App::PREFIX ),
                    'value'   => $item->get_id(),
                    'compare' => '='
                ),
                array(
                    'key'     => sprintf( '%s_category', App::PREFIX ),
                    'value'   => $term_id,
                    'compare' => '='
                )
            )
        ) );
        if ( !count( $terms ) ) {
            $res = wp_insert_term( wp_encode_emoji( wp_strip_all_tags( $item->get_title() ) ), static::TAXONOMY, array( 'slug' => sha1( uniqid() ) ) );
            if ( is_wp_error( $res ) ) {
                return $res;
            }
            update_term_meta( $res[ 'term_id' ], sprintf( '%s_feed_id', App::PREFIX ), $item->get_id() );
            update_term_meta( $res[ 'term_id' ], sprintf( '%s_category', App::PREFIX ), $term_id );
            $term = get_term( $res[ 'term_id' ] );
            Dbg::sprintf( 'New RSS link created: %d', $term->term_id );
        } else {
            $term = $terms[ 0 ];
            Dbg::sprintf( 'RSS link to update found: %d', $term->term_id );
        }
        return $term;
    }

    public function actionInit() {
        //parent::actionInit();
        register_taxonomy( static::TAXONOMY, NULL, array(
            'show_ui'           => TRUE,
            'show_admin_column' => FALSE,
            'show_in_menu'      => FALSE,
            'hierarchical'      => FALSE,
            'labels'            => array(
                'name'          => 'RSS Links',
                'singular_name' => 'RSS Link',
                'edit_item'     => 'Edit RSS Link',
                'view_item'     => 'View RSS Link',
                'update_item'   => 'Update RSS Link',
                'add_new_item'  => 'Add New RSS Link',
                'search_items'  => 'Search RSS Links',
                'not_found'     => 'No RSS links found'
            )
        ) );
        wp_register_script( 'cmra-backend-link-taxonomy', plugin_dir_url( App::PLUGIN_FILE ) . 'assets/backend/js/link-taxonomy.js', array( 'jquery', 'common', 'inline-edit-tax' ), App::VERSION );
    }

    public function actionAdminEnqueueScripts() {
        if ( get_current_screen()->taxonomy == static::TAXONOMY ) {
            wp_enqueue_style( 'cmra-backend-admin' );
            if ( wp_script_is( 'inline-edit-tax', 'enqueued' ) ) {
                wp_enqueue_script( 'cmra-backend-link-taxonomy' );
            }
        }
    }

    // public function actionAdminMenu() {
    //     add_submenu_page( App::SLUG, 'Curated Links', 'RSS Links', 'manage_options', sprintf( 'edit-tags.php?taxonomy=%s', static::TAXONOMY ) );
    // }

    public function actionAdminHead() {
        if ( get_current_screen()->taxonomy == static::TAXONOMY ) {
            echo "<style>#col-left{ display: none !important; }</style>\n";
            echo "<style>#col-right{ width: 100% !important; }</style>\n";
        }
    }

    public function actionCurrentScreen() {
        if ( get_current_screen()->taxonomy == static::TAXONOMY ) {
            new DeleteAbandonedJob( 20 );
        }
    }

    public function actionAddFormFields() {
        echo ViewHelper::load( 'views/backend/taxonomies/link/add_form_fields.php' );
    }

    public function actionEditFormFields( $term ) {
        echo ViewHelper::load( 'views/backend/taxonomies/link/edit_form_fields.php', array(
            'term'          => $term,
            'category_id'   => get_term_meta( $term->term_id, sprintf( '%s_category', App::PREFIX ), TRUE ),
            'url'           => get_term_meta( $term->term_id, sprintf( '%s_url', App::PREFIX ), TRUE ),
            'tag_id_arr'    => get_term_meta( $term->term_id, sprintf( '%s_tag', App::PREFIX ) ),
            'subtitle'      => get_term_meta( $term->term_id, sprintf( '%s_subtitle', App::PREFIX ), TRUE ),
            'image_url'     => get_term_meta( $term->term_id, sprintf( '%s_image_url', App::PREFIX ), TRUE ),
            'show_checkbox' => get_term_meta( $term->term_id, sprintf( '%s_show_checkbox', App::PREFIX ), TRUE )
        ) );
    }

    public function actionEdited( $term_id ) {
        // s_edit_time taken from feed
        //update_term_meta($term_id, sprintf('%s_edit_time', App::PREFIX), time());
        $key = sprintf( '%s_category', App::PREFIX );
        if ( isset( $_POST[ $key ] ) ) {
            $value = intval( $_POST[ $key ] );
            update_term_meta( $term_id, $key, $value );
            if ( $value === -1 ) {
                delete_term_meta( $term_id, $key );
            }
        }
        foreach ( array( '%s_url', '%s_subtitle', '%s_image_url', '%s_show_checkbox' ) as $key ) {
            $key   = sprintf( $key, App::PREFIX );
            $value = filter_input( INPUT_POST, $key );
            if ( $value !== NULL ) {
                update_term_meta( $term_id, $key, $value );
            }
        };
        $key = sprintf( '%s_tag', App::PREFIX );
        if ( isset( $_POST[ 'tax_input' ] ) && is_array( $_POST[ 'tax_input' ][ $key ] ) ) {
            $value = $_POST[ 'tax_input' ][ $key ];
            Misc::update_term_meta_array( $term_id, $key, $value );
        } else {
            if ( filter_input( INPUT_POST, 'action' ) == 'editedtag' ) {
                delete_term_meta( $term_id, $key );
            }
        }
        if ( Options::getOption( 'favicons_local_cache' ) ) {
            $this->updateFavicon( $term_id );
        }
    }

    public function actionCreate( $term_id ) {
        update_term_meta( $term_id, sprintf( '%s_create_time', App::PREFIX ), time() );
        return $this->actionEdited( $term_id );
    }

    public function actionPreDeleteTerm( $term_id, $taxonomy ) {
        if ( $taxonomy == static::TAXONOMY ) {
            wp_delete_attachment( intval( get_term_meta( $term_id, sprintf( '%s_favicon_attachment', App::PREFIX ), TRUE ) ) );
        }
    }

    public function filterManageColumns( $columns ) {
        unset( $columns[ 'posts' ] );
        unset( $columns[ 'slug' ] );
        $columns[ sprintf( '%s_url', App::PREFIX ) ]       = 'URL';
        $columns[ sprintf( '%s_edit_time', App::PREFIX ) ] = 'Date';
        //$columns[sprintf('%s_favicon_attachment', App::PREFIX)] = 'Favicon';
        $columns[ sprintf( '%s_category', App::PREFIX ) ]  = 'Category';
        $columns[ sprintf( '%s_tag', App::PREFIX ) ]       = 'Tags';
        return $columns;
    }

    public function filterManageCustomColumn( $out, $column_name, $term_id ) {
        if ( $column_name === sprintf( '%s_url', App::PREFIX ) ) {
            $url = get_term_meta( $term_id, $column_name, TRUE );
            if ( !$url ) {
                return;
            }
            $img = sprintf( 'https://www.google.com/s2/favicons?domain_url=%s', urlencode( $url ) );
            if ( Options::getOption( 'favicons_local_cache' ) ) {
                $attach_id = get_term_meta( $term_id, sprintf( '%s_favicon_attachment', App::PREFIX ), TRUE );
                $img       = wp_get_attachment_url( $attach_id );
            }
            return $img ? sprintf( '<img src="%s" alt="%s" class="cmra-admin-link-icon" /> %s', $img, $url, $url ) : $url;
        }
        if ( $column_name === sprintf( '%s_edit_time', App::PREFIX ) ) {
            $res = get_term_meta( $term_id, $column_name, TRUE );
            return $res ? date( 'Y-m-d H:i', $res ) : '';
        }
        if ( $column_name === sprintf( '%s_category', App::PREFIX ) ) {
            $id   = get_term_meta( $term_id, $column_name, TRUE );
            $term = get_term_by( 'id', $id, CategoryTaxonomy::TAXONOMY );
            return $term ? $term->name : '';
        }

    }

    public function actionEditTerms( $term_id, $taxonomy ) {
        if ( $taxonomy === static::TAXONOMY ) {
//            $key = sprintf('%s_url', App::PREFIX);
//            if (isset($_POST[$key]) && !strlen($_POST[$key])) {
//                if (defined('DOING_AJAX') && DOING_AJAX) {
//                    die('URL is required.');
//                } else {
//                    AdminNoticeManager::enqueue(new AdminNotice(uniqid(), 'error', 'URL is required.'));
//                    wp_redirect($_POST['_wp_http_referer']);
//                }
//                die();
//            }
            $key = sprintf( '%s_category', App::PREFIX );
            if ( isset( $_POST[ $key ] ) && intval( $_POST[ $key ] ) === -1 ) {
                AdminNoticeManager::enqueue( new AdminNotice( uniqid(), 'error', 'Category is required.' ) );
                wp_redirect( $_POST[ '_wp_http_referer' ] );
                die();
            }
        }
    }

    public function filterGetTermsDefaults( $defaults, $taxonomies ) {
        if ( $taxonomies [ 0 ] != static::TAXONOMY ) {
            return $defaults;
        }
        $defaults[ 'meta_key' ] = sprintf( '%s_edit_time', App::PREFIX );
        $defaults[ 'orderby' ]  = 'meta_value_num';
        $defaults[ 'order' ]    = 'DESC';
        $key                  = sprintf( '%s_category', App::PREFIX );
        if ( isset( $_GET[ $key ] ) ) {
            $id = intval( $_GET[ $key ] );
            if ( $id ) {
                $defaults[ 'meta_query' ] = array(
                    array(
                        'key'     => sprintf( '%s_category', App::PREFIX ),
                        'value'   => $id,
                        'compare' => '='
                    )
                );
            }
        }
        return $defaults;
    }

    public function filterRowActions( $actions, $tag ) {
        unset( $actions[ 'view' ] );
        //unset($actions['edit']);
        unset( $actions[ 'inline hide-if-no-js' ] );
        return $actions;
    }

    public function actionQuickEditCustomBox( $column_name, $screen, $taxonomy = NULL ) {
        if ( $taxonomy !== static::TAXONOMY ) {
            return;
        }
        $key = sprintf( '%s_url', App::PREFIX );
        if ( $column_name == $key ) {
            echo ViewHelper::load( 'views/backend/taxonomies/common/quick_edit_custom_box.php', array(
                'name'  => $column_name,
                'type'  => 'url',
                'title' => 'URL'
            ) );
        }
    }

    private function updateFavicon( $term_id ) {
        $url = get_term_meta( $term_id, sprintf( '%s_url', App:: PREFIX ), TRUE );
        if ( !$url ) {
            $url = 'localhost';
        }
        $tmpfile = download_url( sprintf( 'https://www.google.com/s2/favicons?domain_url=%s', urlencode( $url ) ), 15 );
        if ( is_wp_error( $tmpfile ) ) {
            AdminNoticeManager::enqueue( new AdminNoticeHelper( uniqid(), 'error', sprintf( 'Error occurred during favicon update: %s.', $tmpfile->get_error_message() ) ) );
            return;
        }
        wp_delete_attachment( intval( get_term_meta( $term_id, sprintf( '%s_favicon_attachment', App:: PREFIX ), TRUE ) ) );
        $wp_upload_dir = wp_upload_dir();
        $url           = preg_replace( '/^http(s?)/', '', $url );
        $filename      = sanitize_file_name( sprintf( '%s-%s-favicon.ico', App:: PREFIX, $url ) );
        $filename      = $wp_upload_dir[ 'path' ] . '/' . $filename;
        rename( $tmpfile, $filename );
        $filetype      = wp_check_filetype( basename( $filename ), null );
        $attachment    = array(
            'guid'           => $wp_upload_dir[ 'url' ] . '/' . basename( $filename ),
            'post_mime_type' => $filetype[ 'type' ],
            'post_title'     => preg_replace( '/\.[^.]+$/', '', basename( $filename ) ),
            'post_content'   => '',
            'post_status'    => 'private'
        );
        $attach_id     = wp_insert_attachment( $attachment, $filename );
        $key           = sprintf( '%s_favicon_attachment', App::PREFIX );
        update_term_meta( $term_id, $key, $attach_id );
    }

    public static function wpListCategoriesArgs() {
        $link_order_by = Options::getOption( 'link_order_by' );
        if ( strpos( $link_order_by, 'name' ) !== FALSE ) {
            return array(
                'orderby' => 'name',
                'order'   => strpos( $link_order_by, 'asc' ) ? 'asc' : 'desc',
            );
        }
        if ( strpos( $link_order_by, 'edit_time' ) !== FALSE ) {
            return array(
                'orderby'  => 'meta_value_num',
                'order'    => strpos( $link_order_by, 'asc' ) ? 'asc' : 'desc',
                'meta_key' => sprintf( '%s_edit_time', App::PREFIX ),
            );
        }
        return array();
    }

}
