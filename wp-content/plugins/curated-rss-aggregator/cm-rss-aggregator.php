<?php

/*
  Plugin Name: CM Curated RSS Aggregator
  Plugin URI: https://www.cminds.com/wordpress-plugins-library/curated-wordpress-rss-aggregator-plugin-by-creativeminds/
  Description: Best tool for importing, merging and displaying Curated RSS and Atom feeds on your WordPress site.
  Author: CreativeMindsSolutions
  Author URI: https://www.cminds.com/
  Version: 1.1.8
 */

namespace com\cminds\rssaggregator;

if (version_compare('5.3', PHP_VERSION, '>')) {
    die(sprintf('We are sorry, but you need to have at least PHP 5.3 to run this plugin (currently installed version: %s)'
                    . ' - please upgrade or contact your system administrator.', PHP_VERSION));
}

if (!class_exists('com\cminds\rssaggregator\App')) {

    require_once plugin_dir_path(__FILE__) . 'plugin/Psr4AutoloaderClass.php';

    $loader = new plugin\Psr4AutoloaderClass();
    $loader->register();
    $loader->addNamespace(__NAMESPACE__, untrailingslashit(plugin_dir_path(__FILE__)));

    class App extends plugin\PluginAbstract {

        const VERSION = '1.1.7';
        const PREFIX = 'cmra';
        const SLUG = 'cm-rss-aggregator';
        const PLUGIN_NAME = 'Curated RSS Aggregator';
        const PLUGIN_NAME_EXTENDED = 'Curated RSS Aggregator Free';
        const PLUGIN_FILE = __FILE__;

    }

    include_once plugin_dir_path(__FILE__) . 'bundle/licensing/cminds-free.php';

    new App();
} else {
    die('Plugin is already activated.');
}
