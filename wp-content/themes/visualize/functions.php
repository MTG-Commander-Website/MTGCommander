<?php
/**
 * Visualize functions and definitions
 *
 * @package Visualize
 */

/**
 * Set the content width based on the theme's design and stylesheet.
 */
if ( ! isset( $content_width ) ) {
	$content_width = get_theme_mod( 'content_width', 672 ); /* pixels */
}

if ( ! function_exists( 'visualize_setup' ) ) :

/**
 * Sets up theme defaults and registers support for various WordPress features.
 *
 * Note that this function is hooked into the after_setup_theme hook, which
 * runs before the init hook. The init hook is too late for some features, such
 * as indicating support for post thumbnails.
 */
function visualize_setup() {
	/*
	 * Make theme available for translation.
	 * Translations can be filed in the /languages/ directory.
	 * If you're building a theme based on Visualize, use a find and replace
	 * to change 'visualize' to the name of your theme in all the template files
	 */
	load_theme_textdomain( 'visualize', get_template_directory() . '/languages' );

	/*
	 * Let WordPress manage the document title.
	 * By adding theme support, we declare that this theme does not use a
	 * hard-coded <title> tag in the document head, and expect WordPress to
	 * provide it for us.
	 */
	add_theme_support( 'title-tag' );

	// Add default posts and comments RSS feed links to head.
	add_theme_support( 'automatic-feed-links' );

	/*
	 * Enable support for Post Thumbnails on posts and pages.
	 *
	 * @link http://codex.wordpress.org/Function_Reference/add_theme_support#Post_Thumbnails
	 */
	add_theme_support( 'post-thumbnails' );
	set_post_thumbnail_size( 960, 900, true );

	// This theme uses wp_nav_menu() in one location.
	register_nav_menus( array(
		'primary' => __( 'Primary Menu', 'visualize' ),
	) );

	/*
	 * Switch default core markup for search form, comment form, and comments
	 * to output valid HTML5.
	 */
	add_theme_support( 'html5', array(
		'search-form', 'comment-form', 'comment-list', 'gallery', 'caption'
	) );

	/*
	 * Add support for instant widget live previews in the customizer.
	 */
	add_theme_support( 'customize-selective-refresh-widgets', true );

	/**
	 * Setup the WordPress core custom header feature.
	 */
	add_theme_support( 'custom-header', apply_filters( 'visualize_custom_header_args', array(
		'width'          => 1600,
		'height'         => 900,
		'header-text'    => false,
		'default-image' => get_template_directory_uri() . '/img/headers/beach.jpg',
		'random-defailt' => true,
		'video'          => true,
	) ) );

	/*
	 * Add support for custom logos.
	 */
	add_theme_support( 'custom-logo', array(
		'height' => 115,
		'width' => 230,
		'flex-width' => true,
		'header-text' => array( 'site-title', 'site-description' ),
	) );

	// Default custom headers packaged with the theme. %s is a placeholder for the theme template directory URI.
	register_default_headers( array(
		'beach' => array(
			'url' => '%s/img/headers/beach.jpg',
			'thumbnail_url' => '%s/img/headers/beach-thumb.jpg',
			'description' => __( 'Beach', 'visualize' )
		),
		'hillside' => array(
			'url' => '%s/img/headers/hillside.jpg',
			'thumbnail_url' => '%s/img/headers/hillside-thumb.jpg',
			'description' => __( 'Hillside', 'visualize' )
		),
		'SCampus' => array(
			'url' => '%s/img/headers/SCampus.jpg',
			'thumbnail_url' => '%s/img/headers/SCampus-thumb.jpg',
			'description' => __( 'SCampus', 'visualize' )
		),
	) );

	/*
	 * This theme styles the visual editor to resemble the theme style.
	 */
	// Disable user-defined font size selection to encourage consistency.
	add_theme_support( 'disable-custom-font-sizes' );

	// Disable color pickers in the editor in favor of colors defined in the customizer.
	add_theme_support( 'disable-custom-colors' );

	// Load the classic editor styles into the block editor.
	add_theme_support( 'editor-styles' );

	add_editor_style( array( 'editor-style.css', visualize_font_url() ) );

	// Load default block styles.
	add_theme_support( 'wp-block-styles' );

	// Add support for custom color scheme.
	$h = absint( get_theme_mod( 'hue', 250 ) );
	$s = absint( get_theme_mod( 'saturation', 10 ) );
	$s_heavy = 5 * $s;
	if ( $s_heavy > 100 ) {
		$s_heavy = 100;
	}
	add_theme_support(
		'editor-color-palette',
		array(
			array(
				'name'  => __( 'Dark', 'visualize' ),
				'slug'  => 'dark',
				'color' => 'hsl(' . $h . ', ' . $s . '%, 13%)',
			),
			array(
				'name'  => __( 'Medium-Dark', 'visualize' ),
				'slug'  => 'medium-dark',
				'color' => 'hsl(' . $h . ', ' . $s . '%, 33%)',
			),
			array(
				'name'  => __( 'Bold', 'visualize' ),
				'slug'  => 'bold',
				'color' => 'hsl(' . $h . ', ' . $s_heavy . '%, 50%)',
			),
			array(
				'name'  => __( 'Light Gray', 'visualize' ),
				'slug'  => 'light-gray',
				'color' => 'hsl(' . $h . ', ' . $s . '%, 93%)',
			),
			array(
				'name'  => __( 'White', 'visualize' ),
				'slug'  => 'white',
				'color' => 'hsl(' . $h . ', ' . $s . '%, 100%)',
			)
		)
	);

	/*
	 * Add theme support for starter content.
	 */
	add_theme_support( 'starter-content', array(
		
		'posts' => array(
			'about',
			'contact',
		),

		'options' => array(
			'show_on_front' => 'posts',
		),

		'nav_menus' => array(
			'primary' => array(
				'name' => __( 'Primary Menu', 'visualize' ),
				'items' => array(
					'link_home',
					'page_about',
					'page_contact',
				),
			),
		),

		'widgets' => array(
			'footer' => array(
				'search',
				'text_about',
				'categories',
			),
		),
	) );

}
endif; // visualize_setup
add_action( 'after_setup_theme', 'visualize_setup' );

/**
 * Register widget area.
 *
 * @link http://codex.wordpress.org/Function_Reference/register_sidebar
 */
function visualize_widgets_init() {
	register_sidebar( array(
		'name'          => __( 'Footer', 'visualize' ),
		'id'            => 'footer',
		'description'   => '',
		'before_widget' => '<aside id="%1$s" class="widget %2$s">',
		'after_widget'  => '</aside>',
		'before_title'  => '<h1 class="widget-title">',
		'after_title'   => '</h1>',
	) );
}
add_action( 'widgets_init', 'visualize_widgets_init' );

/**
 * Enqueue scripts and styles.
 */
function visualize_scripts() {
	wp_enqueue_style( 'visualize-style', get_stylesheet_uri(), array(), '20161223' );

	wp_enqueue_style( 'visualize-fonts', visualize_font_url(), array(), null );

	wp_enqueue_script( 'visualize-functions', get_template_directory_uri() . '/js/functions.js', array( 'jquery', 'jquery-masonry' ), '20160717', true );

	if ( is_singular() && comments_open() && get_option( 'thread_comments' ) ) {
		wp_enqueue_script( 'comment-reply' );
	}
}
add_action( 'wp_enqueue_scripts', 'visualize_scripts' );

/**
 * Register Google fonts for Visualize.
 *
 * @since Visualize 1.0
 *
 * @return string
 */
function visualize_font_url() {
	$font_url = '';
	/*
	 * Translators: If there are characters in your language that are not supported
	 * by Kadwa, translate this to 'off'. Do not translate into your own language.
	 */
	if ( 'off' !== _x( 'on', 'Kadwa font: on or off', 'visualize' ) ) {
		$font_url = add_query_arg( 'family', urlencode( 'Kadwa:400' ), "//fonts.googleapis.com/css" );
	}

	return $font_url;
}

/**
 * Enqueue styles for the block-based editor.
 *
 * @since Visualize 1.1
 */
function visualize_block_editor_styles() {
	// Add custom fonts. These do not work with `add_editor_style` in the block editor.
	wp_enqueue_style( 'visualize-fonts', visualize_font_url(), array(), null );
}
add_action( 'enqueue_block_editor_assets', 'visualize_block_editor_styles' );

/**
 * Customize the excerpt display.
 */
function visualize_excerpt_more( $more ) {
	global $post;
	$more = sprintf( __( 'Continue reading %s', 'visualize' ),
			'<span class="screen-reader-text">' . get_the_title() .
			' </span><span class="meta-nav">&rarr;</span>' ); 
	return '&hellip;<div><a class="excerpt-more button" href="' . esc_url( get_permalink( $post->ID ) ) . '">' . $more . '</a></div>';
}
add_filter( 'excerpt_more', 'visualize_excerpt_more' );

/**
 * Custom template tags for this theme.
 */
require get_template_directory() . '/inc/template-tags.php';

/**
 * Custom functions that act independently of the theme templates.
 */
require get_template_directory() . '/inc/extras.php';

/**
 * Theme options, via the customizer.
 */
require get_template_directory() . '/inc/customizer.php';
require get_template_directory() . '/inc/color-patterns.php';

/**
 * Load Jetpack compatibility file.
 */
require get_template_directory() . '/inc/jetpack.php';
