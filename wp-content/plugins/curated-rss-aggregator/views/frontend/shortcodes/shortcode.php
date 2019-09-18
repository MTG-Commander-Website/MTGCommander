<?php

use com\cminds\rssaggregator\App;
use com\cminds\rssaggregator\plugin\frontend\walkers\FilterWalker;
use com\cminds\rssaggregator\plugin\frontend\walkers\CategoryWalker;
use com\cminds\rssaggregator\plugin\frontend\walkers\TagWalker;
use com\cminds\rssaggregator\plugin\taxonomies\CategoryTaxonomy;
use com\cminds\rssaggregator\plugin\options\Options;

$meta_query = array('relation' => 'AND');
if (count($list_term_id_arr)) {
    $meta_query = array_merge($meta_query, array(
        array(
            'key' => sprintf('%s_list', App::PREFIX),
            'value' => $list_term_id_arr,
            'compare' => 'IN'
        )
    ));
}

$cat_terms = get_terms(CategoryTaxonomy::TAXONOMY, array(
    'hide_empty' => FALSE,
    'include' => count($category_term_id_arr) ? $category_term_id_arr : NULL,
    'meta_query' => $meta_query
        ));

$cat_term_id_arr = array_map(function($x) {
    return $x->term_id;
}, (array) $cat_terms);
$cat_term_id_arr[] = -1;
?>

<div class="cmra">

    <div class="cmra-content">
        <?php
        wp_list_categories(array(
            'style' => NULL,
            'hide_empty' => FALSE,
            'hierarchical' => TRUE,
            'title_li' => NULL,
            'show_option_all' => '',
            'include' => $cat_term_id_arr,
            'taxonomy' => CategoryTaxonomy::TAXONOMY,
            'walker' => new CategoryWalker(array(
                'max_links' => $max_links,
                'max_height' => $max_height,
                'tag_term_id_arr' => $tag_term_id_arr))
        ));
        ?>
    </div>
</div>
