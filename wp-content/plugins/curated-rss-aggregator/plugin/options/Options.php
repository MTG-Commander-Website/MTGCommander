<?php

namespace com\cminds\rssaggregator\plugin\options;

use com\cminds\rssaggregator\App;
use com\cminds\rssaggregator\plugin\helpers\ViewHelper;
use com\cminds\rssaggregator\plugin\helpers\HTMLHelper;

class Options {

    private static $defaultOptions = array(
        'columns_count' => 2,
        'max_number_of_links' => NULL,
        'links_rel_nofollow' => 0,
        'links_target_blank' => 0,
        'show_checkboxes' => 0,
        'show_tooltips' => 1,
        'show_dates' => 1,
        'cmra_user_display_categories' => 1,
        'bonus_info_format' => '<strong>List updated on </strong>{last-update-date}. <strong>Total number of items:</strong> {links-count}.',
        'category_font_size' => NULL,
        'category_background_color' => NULL,
        'category_text_color' => NULL,
        'link_max_words_count' => 55,
        'link_subtitle_max_words_count' => 55,
        'link_font_size' => NULL,
        'link_subtitle_font_size' => NULL,
        'show_link_subtitle_indent' => 0,
        'link_hover_color' => NULL,
        'link_image_width' => '30px',
        'link_image_height' => '30px',
        'link_date_format' => 'M d Y',
        'tooltip_max_words_count' => 55,
        'tooltip_background_color' => NULL,
        'tooltip_border_color' => NULL,
        'tooltip_text_color' => NULL,
        'new_tag_id' => -1,
        'new_tag_duration' => 259200,
        'link_order_by' => 'edit_time_desc',
        // 'show_favicons' => false,
        // 'link_favicons_to_source' => false,
        'user_agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/60.0.3112.90 Safari/537.36',
        'label_search_placeholder' => 'Search ...'
    );

    public function __construct() {
        add_action('init', array($this, 'actionInit'));
        add_action('admin_head', array($this, 'actionAdminHead'));
        add_action('admin_menu', array($this, 'actionAdminMenu'), 20);
    }

    public function actionInit() {
        if (isset($_POST['cmra_action_update']) && isset($_POST['nonce']) && is_admin()) {
            if (wp_verify_nonce($_POST['nonce'], 'cmra_action_update')) {
                foreach ($_POST as $k => $v) {
                    $this->updateOption($k, stripslashes($v));
                }
            }
        }
    }

    public function actionAdminHead() {
        if (preg_match('/_cmra-options$/', get_current_screen()->id)) {
            HTMLHelper::enqueueInputColorAssets();
        }
    }

    public function actionAdminMenu() {
        add_submenu_page(App::SLUG, 'Options', 'Options', 'manage_options', sprintf('%s-options', App::PREFIX), array($this, 'displayOptionsPage'));
    }

    public function displayOptionsPage() {
        $content = ViewHelper::load('views/backend/options/options.php');
        echo ViewHelper::load('views/backend/options/template.php', array(
            'nav' => $this->nav(),
            'content' => $content)
        );
    }

    public static function getOption($option) {
        if (static::isValidOption($option)) {
            return get_option(sprintf('%s_%s', App::PREFIX, $option), static::$defaultOptions[$option]);
        } else {
            return NULL;
        }
    }

    public static function updateOption($option, $value) {
        if (static::isValidOption($option)) {
            return update_option(sprintf('%s_%s', App::PREFIX, $option), $value);
        } else {
            return FALSE;
        }
    }

    public static function isValidOption($option) {
        return key_exists($option, static::$defaultOptions);
    }

    private static function nav() {
        global $self, $parent_file, $submenu_file, $plugin_page, $typenow, $submenu;
        $submenus = array();

        $menuItem = App::SLUG;

        if (isset($submenu[$menuItem])) {
            $thisMenu = $submenu[$menuItem];

            foreach ($thisMenu as $sub_item) {
                $slug = $sub_item[2];

                // Handle current for post_type=post|page|foo pages, which won't match $self.
                $self_type = !empty($typenow) ? $self . '?post_type=' . $typenow : 'nothing';

                $isCurrent = FALSE;
                $subpageUrl = get_admin_url('', 'admin.php?page=' . $slug);

                if ((!isset($plugin_page) && $self == $slug) || (isset($plugin_page) && $plugin_page == $slug && ($menuItem == $self_type || $menuItem == $self || file_exists($menuItem) === false))) {
                    $isCurrent = TRUE;
                }

                $url = (strpos($slug, '.php') !== false || strpos($slug, 'http') !== false) ? $slug : $subpageUrl;
                $isExternalPage = strpos($slug, 'http') !== FALSE;
                $submenus[] = array(
                    'link' => $url,
                    'title' => $sub_item[0],
                    'current' => $isCurrent,
                    'target' => $isExternalPage ? '_blank' : ''
                );
            }
        }
        return ViewHelper::load('views/backend/options/nav.php', array('submenus' => $submenus));
    }

}
