<?php
/**
 * Jetpack Compatibility File
 * See: http://jetpack.me/
 *
 * @package Visualize
 */

/**
 * Add theme support for Infinite Scroll.
 * See: http://jetpack.me/support/infinite-scroll/
 */
function visualize_jetpack_setup() {
	add_theme_support( 'infinite-scroll', array(
		//'footer_widgets' => 'footer',
		'type' => 'scroll', // force-scroll for now, even if there may be widgets in the footer. May adjust pending user feedback.
		'container' => 'main',
		'footer'    => false,
	) );
}
add_action( 'after_setup_theme', 'visualize_jetpack_setup' );
