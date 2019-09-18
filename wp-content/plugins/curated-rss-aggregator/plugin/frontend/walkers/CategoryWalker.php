<?php

namespace com\cminds\rssaggregator\plugin\frontend\walkers;

use com\cminds\rssaggregator\App;
use com\cminds\rssaggregator\plugin\taxonomies\LinkTaxonomy;
use com\cminds\rssaggregator\plugin\frontend\walkers\LinkWalker;
use com\cminds\rssaggregator\plugin\options\Options;

class CategoryWalker extends \Walker_Category {

    private $max_links = 0;
    private $max_height = 0;
    private $show_title = TRUE;
    private $tag_term_id_arr = NULL;

    public function __construct($arr = array()) {
        $this->max_links = intval(Options::getOption('max_number_of_links'));
        if (isset($arr['max_links']) && $arr['max_links'] !== NULL) {
            $this->max_links = $arr['max_links'];
        }
        if (isset($arr['max_height'])) {
            $this->max_height = $arr['max_height'];
        }
        if (isset($arr['show_title'])) {
            $this->show_title = $arr['show_title'];
        }
        if (isset($arr['tag_term_id_arr']) && is_array($arr['tag_term_id_arr']) && count($arr['tag_term_id_arr'])) {
            $this->tag_term_id_arr = $arr['tag_term_id_arr'];
        }
    }

    public function start_lvl(&$output, $depth = 0, $args = array()) {
        $output .= "<div class='children'>";
    }

    public function end_lvl(&$output, $depth = 0, $args = array()) {
        $output .= "</div>";
    }

    public function start_el(&$output, $category, $depth = 0, $args = array(), $id = 0) {
        $bonus_style = '';
        if ($depth == 0) {
            $output .= '<div class="cmra-category-box">';
            if ($this->max_height) {
                $bonus_style = sprintf('max-height: %spx;overflow-y: auto;', $this->max_height);
            }
        }
        $color = get_term_meta($category->term_id, sprintf('%s_bg_color', App::PREFIX), TRUE);
        if ($color) {
            $output .= sprintf('<div class="cmra-category" style="background:%s;%s" data-id="%s" data-role="category">', $color, $bonus_style, $category->term_id);
        } else {
            $output .= sprintf('<div class="cmra-category" style="%s" data-id="%s" data-role="category">', $bonus_style, $category->term_id);
        }
        if ($this->show_title || $depth > 0) {
            $output .= sprintf('<div class="cmra-header">%s</div>', $category->name);
        }

        // wp term order ugly hack
        $_get = $_GET;
        $_GET['orderby'] = 1;
        $_GET['taxonomy'] = 1;

        $meta_query = array(
            'relation' => 'AND',
            array(
                'key' => sprintf('%s_category', App::PREFIX),
                'value' => $category->term_id,
                'compare' => '='
        ));
        if ($this->tag_term_id_arr) {
            if (in_array(Options::getOption('new_tag_id'), $this->tag_term_id_arr)) {
                $meta_query = array_merge($meta_query, array(
                    array(
                        'relation' => 'OR',
                        array(
                            'key' => sprintf('%s_tag', App::PREFIX),
                            'value' => $this->tag_term_id_arr,
                            'compare' => 'IN'
                        ),
                        array(
                            'key' => sprintf('%s_edit_time', App::PREFIX),
                            'value' => time() - Options::getOption('new_tag_duration'),
                            'compare' => '>'
                        )
                    )
                ));
            } else {
                $meta_query = array_merge($meta_query, array(
                    array(
                        'relation' => 'OR',
                        array(
                            'key' => sprintf('%s_tag', App::PREFIX),
                            'value' => $this->tag_term_id_arr,
                            'compare' => 'IN'
                        )
                    )
                ));
            }
        }
        $show_favicons = get_term_meta($category->term_id, sprintf('%s_show_favicons', App::PREFIX), TRUE);
        $html = wp_list_categories(array_merge(array(
            'hide_empty' => FALSE,
            'hierarchical' => TRUE,
            'echo' => FALSE,
            'title_li' => NULL,
            'show_option_none' => NULL,
            'taxonomy' => LinkTaxonomy::TAXONOMY,
            'walker' => new LinkWalker(array('show_favicons' => $show_favicons)),
            'number' => $this->max_links,
            'meta_query' => $meta_query
                        ), LinkTaxonomy::wpListCategoriesArgs()));

        $_GET = $_get;

        if ($html) {
            $output .= "<ul class='cmra-category-link-list'>{$html}</ul>";
        }
    }

    public function end_el(&$output, $page, $depth = 0, $args = array()) {
        $output .= "</div>";
        if ($depth == 0) {
            $output .= "</div>";
        }
    }

}
