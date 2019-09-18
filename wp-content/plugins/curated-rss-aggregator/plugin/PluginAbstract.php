<?php

namespace com\cminds\rssaggregator\plugin;

abstract class PluginAbstract {

    const VERSION = '';
    const PREFIX = '';
    const SLUG = '';
    const PLUGIN_NAME = '';
    const PLUGIN_NAME_EXTENDED = '';
    const PLUGIN_FILE = '';

    public function __construct() {

        new taxonomies\ListTaxonomy();
        new taxonomies\CategoryTaxonomy();
        new taxonomies\LinkTaxonomy();
        new options\Options();
        new shortcodes\Shortcode();
        cron\FeedJobManager::init();
        notices\AdminNoticeManager::init();
        new controllers\DiagnosticController();

        add_action('admin_menu', array($this, 'actionAdminMenu'));
        add_action('init', array($this, 'actionInit'));
        register_activation_hook(static::PLUGIN_FILE, array($this, 'activationHook'));
        register_deactivation_hook(static::PLUGIN_FILE, array($this, 'deactivationHook'));
    }

    public function actionAdminMenu() {
        add_menu_page(static::SLUG, static::PLUGIN_NAME_EXTENDED, 'manage_options', static::SLUG, null, 'dashicons-list-view');
    }

    public function actionInit() {
        wp_register_style('cmra-backend-admin', plugin_dir_url(static::PLUGIN_FILE) . 'assets/backend/css/admin.css', array(), static::VERSION);
    }

    public function activationHook() {
        register_taxonomy(taxonomies\CategoryTaxonomy::TAXONOMY, NULL, array(
            'label' => 'Categories',
            'show_ui' => TRUE,
            'show_admin_column' => TRUE,
            'hierarchical' => TRUE
        ));
        $items = get_terms(taxonomies\CategoryTaxonomy::TAXONOMY, array('hide_empty' => FALSE));
        if (!is_array($items)) {
            return;
        }
        foreach ($items as $item) {
            cron\FeedJobManager::scheduleEvent($item->term_id, time() + rand(0, 120));
        }
    }

    public function deactivationHook() {
        $items = get_terms(taxonomies\CategoryTaxonomy::TAXONOMY, array('hide_empty' => FALSE));
        if (!is_array($items)) {
            return;
        }
        foreach ($items as $item) {
            cron\FeedJobManager::clearScheduledHook($item->term_id);
        }
    }

}
