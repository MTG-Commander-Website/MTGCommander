<?php

namespace com\cminds\rssaggregator\plugin\taxonomies;

use com\cminds\rssaggregator\App;
use com\cminds\rssaggregator\plugin\helpers\ViewHelper;

class ListTaxonomy extends TaxonomyAbstract {

    const TAXONOMY = 'cmra_list';

    public function __construct() {
        parent::__construct();
        add_action('admin_menu', array($this, 'actionAdminMenu'));
        add_action('admin_head', array($this, 'actionAdminHead'));
        add_action(sprintf('%s_edit_form_fields', static::TAXONOMY), array($this, 'actionEditFormFields'));
        add_filter(sprintf('manage_edit-%s_columns', static::TAXONOMY), array($this, 'filterManageColumns'));
        add_filter(sprintf('manage_%s_custom_column', static::TAXONOMY), array($this, 'filterManageCustomColumn'), 10, 3);
        add_filter(sprintf('%s_row_actions', static::TAXONOMY), array($this, 'filterRowActions'), 10, 2);
    }

    public function actionInit() {
        parent::actionInit();

        register_taxonomy(static::TAXONOMY, NULL, array(
            'show_ui' => TRUE,
            'show_admin_column' => TRUE,
            'hierarchical' => FALSE,
            'labels' => array(
                'name' => 'Lists',
                'singular_name' => 'List',
                'edit_item' => 'Edit List',
                'view_item' => 'View List',
                'update_item' => 'Update List',
                'add_new_item' => 'Add New List',
                'search_items' => 'Search Lists'
            )
        ));

        wp_register_script( 'cmra-backend-list-taxonomy', plugin_dir_url( App::PLUGIN_FILE ) . 'assets/backend/js/list-taxonomy.js', array( 'jquery', 'common', 'inline-edit-tax' ), App::VERSION );
    }

    public function actionAdminEnqueueScripts() {
        if ( get_current_screen()->taxonomy == static::TAXONOMY ) {
            wp_enqueue_style( 'cmra-backend-admin' );
            if ( wp_script_is( 'inline-edit-tax', 'enqueued' ) ) {
                wp_enqueue_script( 'cmra-backend-list-taxonomy' );
            }
        }
    }

    public function actionAdminMenu() {
        add_submenu_page(App::SLUG, 'Lists', 'Lists', 'manage_options', sprintf('edit-tags.php?taxonomy=%s', static::TAXONOMY));
    }

    public function actionAdminHead() {
        if (get_current_screen()->taxonomy == static::TAXONOMY) {
            //echo "<style>.form-field.term-slug-wrap{ display: none !important; }</style>\n";
            //echo "<style>.inline-edit-col label:nth-child(2){ display: none !important; }</style>\n";
        }
    }

    public function actionEditFormFields($term) {
        echo ViewHelper::load('views/backend/taxonomies/list/edit_form_fields.php', array(
            'list_id' => $term->term_id
        ));

    }

    public function filterManageColumns($columns) {
        unset($columns['posts']);
        //unset($columns['slug']);
        //$columns[sprintf('%s_category', App::PREFIX)] = 'Related categories';
        return $columns;
    }

    public function filterManageCustomColumn($out, $column_name, $term_id) {
        if ($column_name === sprintf('%s_category', App::PREFIX)) {
            $items = get_terms(CategoryTaxonomy::TAXONOMY, array(
                'hide_empty' => FALSE,
                'hierarchical' => FALSE,
                'meta_query' => array(
                    array(
                        'key' => sprintf('%s_list', App::PREFIX),
                        'value' => $term_id,
                        'compare' => 'IN'
                    )
                )
            ));
            echo implode(', ', array_map(function($item) {
                        return $item->name;
                    }, $items));
        }
    }

    public function filterRowActions($actions, $tag) {
        unset($actions['view']);
        return $actions;
    }

}
