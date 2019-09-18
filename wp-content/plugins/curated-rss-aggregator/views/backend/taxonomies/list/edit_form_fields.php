<?php

use com\cminds\rssaggregator\App;
use com\cminds\rssaggregator\plugin\taxonomies\CategoryTaxonomy;
use com\cminds\rssaggregator\plugin\walkers\AdminCategoryWalker1;
?>

<tr class="form-field">
    <th scope="row" valign="top"><label for="term-category_id">Related categories</label></th>
    <td>
        <ul id="term-category_id">
            <?php
            wp_list_categories(array(
                'taxonomy' => CategoryTaxonomy::TAXONOMY,
                'hide_empty' => FALSE,
                'show_option_all' => NULL,
                'hierarchical' => TRUE,
                'title_li' => NULL,
                'walker' => new AdminCategoryWalker1(),
                'meta_query' => array(
                    array(
                        'key' => sprintf('%s_list', App::PREFIX),
                        'value' => $list_id,
                        'compare' => 'IN'
                    )
                )
            ));
            ?>
        </ul>
        <p class="description">Categories related with edited list.</p>
    </td>
</tr>

<style>
    #term-category_id .children{
        padding-left: 15px;
    }
</style>

