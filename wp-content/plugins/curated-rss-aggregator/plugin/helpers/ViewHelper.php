<?php

namespace com\cminds\rssaggregator\plugin\helpers;

use com\cminds\rssaggregator\App;

class ViewHelper {

    public static function load($filename, $data = array()) {
        ob_start();
        extract($data, EXTR_SKIP);
        include plugin_dir_path(App::PLUGIN_FILE) . $filename;
        return ob_get_clean();
    }

}
