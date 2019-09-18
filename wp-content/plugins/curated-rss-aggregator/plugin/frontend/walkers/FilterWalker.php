<?php
namespace com\cminds\rssaggregator\plugin\frontend\walkers;

use com\cminds\rssaggregator\plugin\options\Options;

class FilterWalker extends \Walker_Category {

    public function start_lvl(&$output, $depth = 0, $args = array()) {
        $indent = str_repeat("\t", $depth);
        $output .= "$indent<ul class='children'>\n";
    }

    public function end_lvl(&$output, $depth = 0, $args = array()) {
        $indent = str_repeat("\t", $depth);
        $output .= "$indent</ul>\n";
    }

    public function start_el(&$output, $category, $depth = 0, $args = array(), $id = 0) {
        $output .= sprintf('<li class="cmra-filter-list-entry" title="%s" data-name="%s" data-id="%s">', esc_attr($category->description), esc_attr($category->name), $category->term_id);
        if( Options::getOption('cmra_user_display_categories')) {
	        $output .= '<input type="checkbox" name="display_category[]" ' . checked( true, true, false ) . ' /> ';
        }
        $output .= $category->name;
    }

    public function end_el(&$output, $page, $depth = 0, $args = array()) {
        $output .= "</li>\n";
    }

}
