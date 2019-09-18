<?php

use com\cminds\rssaggregator\plugin\frontend\walkers\CategoryWalker;
use com\cminds\rssaggregator\plugin\taxonomies\CategoryTaxonomy;

$cat_terms = get_terms(CategoryTaxonomy::TAXONOMY, array(
    'hide_empty' => FALSE,
    'child_of' => $category_term->term_id
        ));

$cat_term_id_arr = array_map(function($x) {
    return $x->term_id;
}, (array) $cat_terms);
$cat_term_id_arr[] = $category_term->term_id;
?>

<div class="cmra">

    <div class="cmra-content-single">
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
                'show_title' => FALSE)
            )
        ));
        ?>
    </div>
</div>