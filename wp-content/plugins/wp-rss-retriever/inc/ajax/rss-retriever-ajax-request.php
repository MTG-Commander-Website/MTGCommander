<?php

function rss_retriever_ajax_request() { 
    if ( isset($_REQUEST) ) {

        // get variables
        $settings = isset($_REQUEST['settings']) ? $_REQUEST['settings'] : null; 
        $settings['ajax'] = 'false';

        try {
            $feed = new RSS_Retriever_Feed($settings);
            $output = $feed->display_feed();
        } catch (Exception $e) {
            $output = $e->getMessage() . "\n";
        }

        // encode and return the array
        echo json_encode($output);
    }
    die();
}
 
add_action( 'wp_ajax_rss_retriever_ajax_request', 'rss_retriever_ajax_request' );
add_action( 'wp_ajax_nopriv_rss_retriever_ajax_request', 'rss_retriever_ajax_request' );