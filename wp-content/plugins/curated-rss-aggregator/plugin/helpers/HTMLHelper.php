<?php

namespace com\cminds\rssaggregator\plugin\helpers;

use com\cminds\rssaggregator\App;

class HTMLHelper {

    public static function inputColor($name, $value = '#FFFFFF', $arr = array()) {
        if (isset($arr['class'])) {
            $arr['class'] = $arr['class'] . ' cmra-input-color';
        } else {
            $arr['class'] = 'cmra-input-color';
        }
        $arr = array_merge(array(
            'size' => '40',
            'aria-required' => 'false',
            'id' => uniqid('id')
                ), $arr);
        array_walk($arr, function(&$v, $k) {
            $v = sprintf('%s="%s"', $k, $v);
        });
        return sprintf('<input name="%s" type="text" value="%s" %s />', $name, esc_attr($value), implode(' ', $arr));
    }

    public static function enqueueInputColorAssets() {
        wp_enqueue_style('wp-color-picker');
        wp_enqueue_script('cmra-backend-color-picker', plugin_dir_url(App::PLUGIN_FILE) . 'assets/backend/js/color-picker.js', array('wp-color-picker'), App::VERSION);
    }

    public static function inputFontSize($name, $value = NULL, $arr = array()) {
        $arr = array_merge(array(
            'placeholder' => 'e.g. 16px or 1.1em',
            'size' => '40',
            'aria-required' => 'false',
            'id' => uniqid('id')
                ), $arr);
        array_walk($arr, function(&$v, $k) {
            $v = sprintf('%s="%s"', $k, $v);
        });
        return sprintf('<input name="%s" type="text" value="%s" %s />', $name, esc_attr($value), implode(' ', $arr));
    }

}
