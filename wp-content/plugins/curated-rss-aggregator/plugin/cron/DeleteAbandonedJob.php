<?php

namespace com\cminds\rssaggregator\plugin\cron;

use com\cminds\rssaggregator\App;
use com\cminds\rssaggregator\plugin\taxonomies\CategoryTaxonomy;
use com\cminds\rssaggregator\plugin\taxonomies\LinkTaxonomy;

class DeleteAbandonedJob {

    public function __construct($count = 100) {
        $cat_terms = get_terms(CategoryTaxonomy::TAXONOMY, array('hide_empty' => FALSE));
        $cat_term_id_arr = array_map(function($x) {
            return $x->term_id;
        }, (array) $cat_terms);
        $terms = get_terms(LinkTaxonomy::TAXONOMY, array(
            'hide_empty' => FALSE,
            'number' => $count,
            'meta_query' => array(
                'relation' => 'OR',
                array(
                    'key' => sprintf('%s_category', App::PREFIX),
                    'value' => $cat_term_id_arr,
                    'compare' => 'NOT IN'
                ),
                array(
                    'key' => sprintf('%s_category', App::PREFIX),
                    'compare' => 'NOT EXISTS'
                )
            )
        ));
        if (!is_array($terms) || count($terms) == 0) {
            return;
        }
        foreach ($terms as $term) {
            wp_delete_term($term->term_id, LinkTaxonomy::TAXONOMY);
        }
    }

}
