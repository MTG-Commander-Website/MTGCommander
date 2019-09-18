<?php

namespace com\cminds\rssaggregator\plugin\cron;

use com\cminds\rssaggregator\App;
use com\cminds\rssaggregator\plugin\taxonomies\LinkTaxonomy;

class DeleteOutdatedJob {

    public function __construct($term_id) {
        $delete_after = intval(get_term_meta($term_id, sprintf('%s_delete_after', App::PREFIX), TRUE));
        if (!$delete_after) {
            return;
        }
        $terms = get_terms(LinkTaxonomy::TAXONOMY, array(
            'hide_empty' => FALSE,
            'meta_query' => array(
                'relation' => 'AND',
                array(
                    'key' => sprintf('%s_edit_time', App::PREFIX),
                    'value' => time() - $delete_after,
                    'compare' => '<'
                ),
                array(
                    'key' => sprintf('%s_category', App::PREFIX),
                    'value' => $term_id,
                    'compare' => '='
                )
            )
        ));
        foreach ((array) $terms as $term) {
            wp_delete_term($term->term_id, LinkTaxonomy::TAXONOMY);
        }
    }

}
