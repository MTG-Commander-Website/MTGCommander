<?php

namespace com\cminds\rssaggregator\plugin\controllers;

use com\cminds\rssaggregator\App;
use com\cminds\rssaggregator\plugin\cron\FetchFeedJob;
use com\cminds\rssaggregator\plugin\helpers\ViewHelper;
use com\cminds\rssaggregator\plugin\helpers\ConditionalEchoHelper as Dbg;

class DiagnosticController {

    private $testRunResult = NULL;

    public function __construct() {
        add_action('init', function() {
            if (filter_input(INPUT_POST, 'nonce') && wp_verify_nonce(filter_input(INPUT_POST, 'nonce'), 'cmra_diagnostic_action1')) {
                $term_id = filter_input(INPUT_POST, 'cat');
                if ($term_id == -1) {
                    return;
                }
                Dbg::$isShowOutput = TRUE;
                $handler = set_error_handler([$this, 'errorHandler']);
                register_shutdown_function([$this, 'shutdown']);
                ob_start();
                new FetchFeedJob($term_id);
                $this->testRunResult = ob_get_clean();
                set_error_handler($handler);
            }
        });

        add_filter('cmra_options_diagnostic_tab', function() {
            if (!empty($this->testRunResult)) {
                echo ViewHelper::load('views/backend/options/diagnostics_result.php', ['result' => $this->testRunResult]);
            } else {
                echo ViewHelper::load('views/backend/options/diagnostics_form.php');
            }
        });
    }

    public function shutdown() {
        $error = error_get_last();
        if ($error !== NULL) {
            Dbg::sprintf(sprintf("%s at line %s: %s", $error['file'], $error['line'], $error['message']));
            exit;
        }
    }

    public function errorHandler($errno, $errstr, $errfile, $errline) {
        // https://core.trac.wordpress.org/ticket/29204
        if (strpos($errstr, 'non-static method WP_Feed_Cache::create()')) {
            return TRUE;
        }
        Dbg::sprintf(sprintf("%s at line %s: %s", $errfile, $errline, $errstr));
        return TRUE;
    }

}
