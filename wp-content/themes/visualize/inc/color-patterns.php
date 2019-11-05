<?php
/**
 * Generate custom colors CSS.
 */

function visualize_custom_colors_css() {
	$h = absint( get_theme_mod( 'hue', 250 ) );
	$s = absint( get_theme_mod( 'saturation', 10 ) );
	$s_heavy = 5 * $s;
	if ( $s_heavy > 100 ) {
		$s_heavy = 100;
	}

	// Defaults for all colors, so don't need to do anything.
	if ( 250 === $h && 10 === $s && ! is_customize_preview() ) {
		return '';
	}

	$css = '
body,
input,
select,
textarea,
input[type="text"]:focus,
input[type="email"]:focus,
input[type="url"]:focus,
input[type="password"]:focus,
input[type="search"]:focus,
textarea:focus,
.home .has-header-image .main-nav a,
.entry-meta .author a,
.post-categories a,
.post-navigation,
.paging-navigation,
.post-navigation a,
.paging-navigation a,
.comments-area,
.has-dark-color {
	color: hsl(' . $h . ', ' . $s . '%, 13%);
}

button:focus,
.button:focus,
.site-main .entry-content .button:focus,
.wp-block-button__link:focus,
.site-main .entry-content .wp-block-file__button:focus,
.site-main .entry-content .wp-block-button__link:focus,
input[type="button"]:focus,
input[type="reset"]:focus,
input[type="submit"]:focus,
button:hover,
.button:hover,
.site-main .entry-content .button:hover,
.wp-block-button__link:hover,
.site-main .entry-content .wp-block-file__button:hover,
.site-main .entry-content .wp-block-button__link:hover,
input[type="button"]:hover,
input[type="reset"]:hover,
input[type="submit"]:hover,
.home .has-header-image .site-branding,
.wp-custom-header-video-button,
.site-header,
.main-nav,
.main-nav .sub-menu,
.excerpt-more.button,
.entry-meta .author:first-letter,
.post-image.button,
.widget-area,
.has-dark-background-color {
	background: hsl(' . $h . ', ' . $s . '%, 13%);
}

input[type="text"]:focus,
input[type="email"]:focus,
input[type="url"]:focus,
input[type="password"]:focus,
input[type="search"]:focus,
textarea:focus,
.post-categories a {
	border-color: hsl(' . $h . ', ' . $s . '%, 33%);
}

.hentry,
.post-navigation,
.paging-navigation {
	border-top-color: hsl(' . $h . ', ' . $s . '%, 33%);
}

.post-tags a:before {
    border-right-color: hsl(' . $h . ', ' . $s . '%, 33%);
}

.post-tags a:after {
    border-left-color: hsl(' . $h . ', ' . $s . '%, 33%);
}

input[type="text"],
input[type="email"],
input[type="url"],
input[type="password"],
input[type="search"],
textarea,
blockquote,
.has-medium-dark-color {
	color: hsl(' . $h . ', ' . $s . '%, 33%);
}

.post-categories a:focus,
.post-categories a:hover,
.post-categories a:active,
.post-tags a,
.has-medium-dark-background-color {
	background: hsl(' . $h . ', ' . $s . '%, 33%);
}

a:hover,
a:focus,
a:active,
.home .has-header-image .main-nav li:hover > a,
.home .has-header-image .main-nav li > a:focus,
.has-bold-color {
	color: hsl(' . $h . ', ' . $s_heavy . '%, 50%);
}

.home .has-header-image .main-nav li:hover > a,
.home .has-header-image .main-nav li > a:focus {
	border-bottom-color: hsl(' . $h . ', ' . $s_heavy . '%, 50%);
}

button,
.button,
.site-main .entry-content .button,
.wp-block-button__link,
.site-main .entry-content .wp-block-file__button,
.site-main .entry-content .wp-block-button__link,
input[type="button"],
input[type="reset"],
input[type="submit"],
::selection,
.excerpt-more:hover,
.excerpt-more:focus,
.excerpt-more:active,
.wp-custom-header-video-button:hover,
.wp-custom-header-video-button:focus,
.button.post-image:hover,
.button.post-image:focus,
.button.post-image:active,
.has-bold-background-color {
	background: hsl(' . $h . ', ' . $s_heavy . '%, 50%);
}

::-moz-selection {
	background: hsl(' . $h . ', ' . $s_heavy . '%, 50%);
}

.widget-area a:hover,
.widget-area a:focus,
.widget-area a:active,
.site-footer a:hover,
.site-footer a:focus,
.site-footer a:active,
.site-title a:hover,
.site-title a:focus,
.site-title a:active,
.main-nav li:hover > a,
.main-nav li > a:focus,
.post-navigation a:focus,
.post-navigation a:hover,
.post-navigation a:active,
.paging-navigation a:hover,
.paging-navigation a:focus,
.paging-navigation a:active {
	color: hsl(' . $h . ', ' . $s_heavy . '%, 70%);
}

.hentry .mejs-controls .mejs-time-rail .mejs-time-current,
.widget .mejs-controls .mejs-time-rail .mejs-time-current {
	background: hsl(' . $h . ', ' . $s_heavy . '%, 70%);
}

.main-nav li:hover > a,
.main-nav li > a:focus {
	border-bottom-color: hsl(' . $h . ', ' . $s_heavy . '%, 70%);
}

.widget-area a,
.site-footer,
.site-footer a {
	color: hsl(' . $h . ', ' . $s . '%, 80%);
}

input[type="text"],
input[type="email"],
input[type="url"],
input[type="password"],
input[type="search"],
textarea {
	border-color: hsl(' . $h . ', ' . $s . '%, 80%);
}

.comment-list article {
    border-bottom-color: hsl(' . $h . ', ' . $s . '%, 80%);
}

hr {
	background-color: hsl(' . $h . ', ' . $s . '%, 80%);
}

.has-light-gray-color {
	color: hsl(' . $h . ', ' . $s . '%, 93%);
}

pre,
.home .has-header-image .main-nav,
.home .has-header-image .main-nav .sub-menu,
.entry-footer.entry-meta,
.page-header,
.post-navigation,
.paging-navigation,
.has-light-gray-background-color {
	background: hsl(' . $h . ', ' . $s . '%, 93%);
}';

	return $css;
}