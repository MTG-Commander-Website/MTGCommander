<?php

namespace com\cminds\rssaggregator\plugin\notices;

use com\cminds\rssaggregator\App;
use com\cminds\rssaggregator\plugin\notices\AdminNotice;

class AdminNoticeManager {

    private static $sessionKey = 'session_key';

    public static function init() {
        add_action('init', array(get_called_class(), 'actionInit'));
        add_action('admin_notices', array(get_called_class(), 'actionAdminNotices'));
    }

    public static function actionInit() {
        if (!session_id()) {
            session_start();
        }
        self::$sessionKey = sprintf('%s_admin_notice_manager', App::PREFIX);
        if (!isset($_SESSION[self::$sessionKey]) || !is_array($_SESSION[self::$sessionKey])) {
            $_SESSION[self::$sessionKey] = array();
        }
    }

    public static function actionAdminNotices() {
        $items = (array) $_SESSION[self::$sessionKey];
        foreach ($items as $item) {
            echo $item;
        }
        unset($_SESSION[self::$sessionKey]);
    }

    public static function enqueue(AdminNotice $item) {
        $items = (array) $_SESSION[self::$sessionKey];
        $items[] = $item;
        $_SESSION[self::$sessionKey] = $items;
    }

}
