<?php
/**
 * Visualize Theme Customizer
 *
 * @package Visualize
 */

/**
 * Do lots of fun stuff with the Customizer.
 *
 * @param WP_Customize_Manager $wp_customize Theme Customizer object.
 */
function visualize_customize_register( $wp_customize ) {
	// Register default controls for postMessage transport, for instant previews.
	$wp_customize->get_setting( 'blogname' )->transport         = 'postMessage';
	$wp_customize->get_setting( 'blogdescription' )->transport  = 'postMessage';

	// Hide controls when they aren't used on the current page.
	$wp_customize->get_control( 'header_image' )->active_callback    = 'is_front_page';
	$wp_customize->get_control( 'blogdescription' )->active_callback = 'is_front_page';

	// Add the Visualize section and options.
	$wp_customize->add_section( 'visualize', array(
		'title'       => __( 'Visualize', 'visualize' ),
		'description' => __( 'Settings specific to the Visualize theme.', 'visualize' ),
		'priority'    => 40,
	) );

	// Add the color & background settings.
	$wp_customize->add_setting( 'saturation' , array(
		'default'           => 10,
		'transport'         => 'postMessage',
		'sanitize_callback'	=> 'absint',
	) );

	$wp_customize->add_setting( 'hue' , array(
		'default'           => 250,
		'transport'         => 'postMessage',
		'sanitize_callback'	=> 'absint',
	) );

	$wp_customize->add_control( 'saturation', array(
		'label'       => __( 'Color Saturation', 'visualize' ),
		'type'        => 'range',
		'section'     => 'visualize',
		'input_attrs' => array(
			'min' => 0,
			'max' => 100,
			'step' => 5,
		),
	) );

	$wp_customize->add_control( new WP_Customize_Color_Control( $wp_customize, 'hue', array(
		'label'   => __( 'Color Scheme Hue', 'visualize' ),
		'description' => __( 'All theme colors will be tinted according to this selection.', 'visualize' ),
		'section' => 'visualize',
		'mode'    => 'hue',
	) ) );

	$wp_customize->add_setting( 'default_image', array(
		'default'           => get_stylesheet_directory_uri() . '/img/default.png',
		'sanitize_callback' => 'esc_url_raw',
	) );

	$wp_customize->add_control( new WP_Customize_Image_Control( $wp_customize, 'default_image', array(
		'label'   => __( 'Default Featured Image', 'visualize' ),
		'section' => 'visualize',
	) ) );

	$wp_customize->add_setting( 'copy_name' , array(
		'default'	        => get_bloginfo( 'name' ),
		'transport'         => 'postMessage',
		'sanitize_callback' => 'sanitize_text_field',
	) );

	$wp_customize->add_setting( 'powered_by_wp' , array(
		'default'	        => true,
		'transport'         => 'postMessage',
		'sanitize_callback' => 'visualize_sanitize_boolean',
	) );

	$wp_customize->add_setting( 'theme_meta' , array(
		'default'	        => false,
		'transport'         => 'postMessage',
		'sanitize_callback' => 'visualize_sanitize_boolean',
	) );

	$wp_customize->add_control( 'copy_name', array(
		'label'   => __( 'Copyright Name', 'visualize' ),
		'section' => 'visualize',
		'type'    => 'text',
	) );

	$wp_customize->add_control( 'powered_by_wp', array(
		'label'   => __( 'Proudly Powered By WordPress', 'visualize' ),
		'section' => 'visualize',
		'type'    => 'checkbox',
	) );

	$wp_customize->add_control( 'theme_meta', array(
		'label'   => __( 'Show theme credit in footer', 'visualize' ),
		'section' => 'visualize',
		'type'    => 'checkbox',
	) );

	// Partial refresh for better user experience (faster loading of changes).
	// This is a supplement to the initial postMessage setting update that handles PHP 
	// logic more complex than basic color swaps in the CSS (such as contrast ratios).
	$wp_customize->selective_refresh->add_partial( 'visualize_colors', array(
		'selector'        => '#visualize-colors',
		'settings'        => array( 'saturation', 'hue' ),
		'render_callback' => 'visualize_custom_colors_css',
	) );

	// Add selective refresh support for the title and tagline, and the footer options.
	$wp_customize->selective_refresh->add_partial( 'blogname', array(
	    'selector' => '.site-title',
	    'render_callback' => 'visualize_customize_partial_blogname',
	) );
	$wp_customize->selective_refresh->add_partial( 'blogdescription', array(
	    'selector' => '.site-description',
	    'render_callback' => 'visualize_customize_partial_blogdescription',
	) );
	$wp_customize->selective_refresh->add_partial( 'footer_credits', array(
	    'selector' => '.site-info',
		'settings' => array( 'copy_name', 'powered_by_wp', 'theme_meta' ),
	    'render_callback' => 'visualize_footer_credits',
	) );

}
add_action( 'customize_register', 'visualize_customize_register' );

function visualize_sanitize_boolean( $value ) {
	if ( $value ) {
		return true;
	} else {
		return false;
	}
}

/**
 * Binds JS handlers to make the customizer preview reload changes asynchronously.
 */
function visualize_customize_preview_js() {
	wp_enqueue_script( 'visualize-customize-preview', get_template_directory_uri() . '/js/customize-preview.js', array( 'customize-preview', 'jquery' ), '20170121', true );
}
add_action( 'customize_preview_init', 'visualize_customize_preview_js' );


/**
 * Load dynamic logic for the customizer controls pane.
 */
function visualize_customize_controls_js() {
	wp_enqueue_script( 'visualize-customize-controls', get_template_directory_uri() . '/js/customize-controls.js', array( 'customize-controls', 'jquery' ), '20161231', true );
}
add_action( 'customize_controls_enqueue_scripts', 'visualize_customize_controls_js' );


/**
 * Display custom color CSS.
 */
function visualize_custom_color_css() {
	if ( is_customize_preview() ) {
		$data = ' data-hue="' . absint( get_theme_mod( 'hue', 250 ) ) . '"';
		$data .= ' data-saturation="' . absint( get_theme_mod( 'saturation', 10 ) ) . '"';
	} else {
		$data = '';
	}
	echo '<style type="text/css" id="visualize-colors"' . $data . '>' .
		visualize_custom_colors_css() .
	'</style>';
}
add_action( 'wp_head', 'visualize_custom_color_css' );

/**
 * Render the site title for the selective refresh partial.
 *
 * @since Visualize 1.0
 * @see visualize_customize_register()
 *
 * @return void
 */
function visualize_customize_partial_blogname() {
	bloginfo( 'name' );
}

/**
 * Render the site tagline for the selective refresh partial.
 *
 * @since Visualize 1.0
 * @see visualize_customize_register()
 *
 * @return void
 */
function visualize_customize_partial_blogdescription() {
	bloginfo( 'description' );
}
