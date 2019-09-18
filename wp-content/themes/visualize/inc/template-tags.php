<?php
/**
 * Custom template tags for this theme.
 *
 * Eventually, some of the functionality here could be replaced by core features.
 *
 * @package Visualize
 */

if ( ! function_exists( 'visualize_post_thumbnail_style' ) ) :
/**
 * Display the Featured Image/Post Thumbnail inline style for the current post.
 */
function visualize_post_thumbnail_style() {
	$img = visualize_get_post_image( 'post-thumbnail' );
	if ( ! is_array( $img ) ) {
		if ( '' === $img ) {
			echo 'class="empty"';
			return;
		}
		$fullimgdata = getimagesize( $img );
		$img = array(
			0 => $img, // url
			1 => $fullimgdata[0], // width
			2 => $fullimgdata[1], // height
		);
	}
	echo 'style="background-image: url(' . esc_url( $img[0] ) . ');" data-width="' . absint( $img[1] ) . '" data-height="' . absint( $img[2] ) . '"';
}
endif;

if ( ! function_exists( 'visualize_post_thumbnail_img' ) ) :
/**
 * Render the Featured Image/Post Thumbnail img tag for the current post.
 */
function visualize_post_thumbnail_img() {
	$img = visualize_get_post_image( 'post-thumbnail' );
	if ( ! is_array( $img ) ) {
		if ( '' === $img ) {
			echo '<div id="full-page-image" class="empty"></div>';
			return;
		}
		$fullimgdata = getimagesize( $img );
		$imgdata = array(
			0 => $img, // url
			1 => $fullimgdata[0], // width
			2 => $fullimgdata[1], // height
		);
		$img = $imgdata;
	}

	// Determine image orientation.
	$orientation = ( $img[1] > $img[2] ) ? 'landscape' : 'portrait';

	echo '<div id="full-page-image" style="background-image: url(' . esc_url( $img[0] ) . ');" class="' . $orientation . '"></div>';
}
endif;

/**
 * Get the featured image (post thumbnail) URL, if it exists, or otherwise,
 * look for another image in the post to use as the featured image.
 *
 * Based on cxnh_quickshare_get_post_image(), from the QuickShare plugin by Nick Halsey.
 *
 * @param string $size WordPress image size to get.
 * @return string $url URL of the post image.
 *
 * @since Visualize 1.0
 */
function visualize_get_post_image( $size = 'post-thumbnail' ) {
	global $post;
	$imgdata = array();

	// If there's a featured image, use it.
	if ( has_post_thumbnail() ) {
		$imgdata = wp_get_attachment_image_src( get_post_thumbnail_id( $post->ID ), $size );
	} elseif ( is_attachment() ) {
		$imgdata = wp_get_attachment_image_src( $post->ID, $size ); // Attachment post type, so post id is attachment id.
	} else {
		// Next, try grabbing first attached image.
		$args = array(
			'numberposts' => 1,
			'post_parent' => $post->ID,
			'post_type' => 'attachment',
			'post_mime_type' => 'image'
		);
		$attachments = get_children( $args ); // Array is keyed by attachment id.
		if ( ! empty( $attachments ) ) {
			$rekeyed_array = array_values( $attachments );
			$imgdata = wp_get_attachment_image_src( $rekeyed_array[0]->ID , 'post-thumbnail' );
		} else {
			// Finally, look for the first img tag brute-force. Presumably if there's a caption or it's a gallery or anything it should have come up as an attachment.
			$result = preg_match_all( '/<img.+src=[\'"]([^\'"]+)[\'"].*>/i', $post->post_content, $matches ); // Find img tags, grab srcs.
			if ( $result > 0 ) {
				return $matches[1][0]; // Grab the first img src, no way to select size if we've gotten this deep.
			}
		}
	}

	if ( ! empty( $imgdata ) ) {
		return $imgdata;
	} else {
		// Use the default/fallback post image, if it exists.
		return get_theme_mod( 'default_image', get_stylesheet_directory_uri() . '/img/default.png' );
	}
}

if ( ! function_exists( 'visualize_gallery_excerpt' ) ) :
/**
 * Display an excerpt from a gallery format's gallery: one full row of images, with a read more link.
 * If no gallery exists in the post, display the content excerpt instead.
 */
function visualize_gallery_excerpt() {
	global $post;
	$data = get_post_gallery( $post, false );
	$srcs = array();
	$ids = array();
	if ( ! array_key_exists( 'ids', $data ) ) {
		$srcs = get_post_gallery_images(); // We have to use whatever size the user selected or the default since the ids aren't directly available.
	} else {
		$ids = explode( ',', $data['ids'] );
	}

	if ( empty( $ids ) && empty( $srcs ) ) {
		return;
	}

	echo '<ul class="gallery-excerpt">';

	// Display up to 8 images.
	$i = 0;
	while ( $i < 8 ) {
		if ( ! array_key_exists( $i, $ids ) && ! array_key_exists( $i, $srcs ) ) {
			break;
		}
		if ( ! empty( $srcs ) ) {
			$src = $srcs[$i];
			$orientation = 'landscape'; // This is the fallback for older shortcodes, so hopefully they're landscape.
		} else {
			$img = wp_get_attachment_image_src( absint( trim( $ids[$i] ) ), 'medium_large' ); // Depending on aspect ratio, the "medium" size may not be big enough. We need at least 300px square.
			$src = $img[0];
			$orientation = ( $img[1] > $img[2] ) ? 'landscape' : 'portrait';
			if ( 'landscape' === $orientation && 1.33 * $img[1] > $img[2] ) {
				$orientation .= ' wide';
			} elseif ( 1.33 * $img[2] > $img[1] ) {
				$orientation .= ' tall';
			}
		}
		if ( $src ) {
			echo '<li class="gallery-excerpt-item"><img src="' . esc_url( $src ) . '" class="gallery-excerpt-image ' . $orientation . '" alt=""></li>';
		}
		$i++;
	}

	if ( has_post_thumbnail() ) {
		$title = sprintf ( __( 'See more %s', 'visualize' ),
			'<span class="screen-reader-text">' . get_the_title() .
			' </span><span class="meta-nav"> &rarr;</span>' );
	} else {
		$title = get_the_title() . '<span class="meta-nav">&rarr;</span>';
	}
	echo '<li class="gallery-excerpt-item"><a href="' . esc_url( get_the_permalink() ) . '" class="excerpt-more button" rel="bookmark">' . $title . '</a></li>';
	echo '</ul>';
}
endif;


/**
 * Return an approximate post word count.
 */
function visualize_word_count() {
	$content = get_the_content();
	return str_word_count( wp_strip_all_tags( $content ) );
}

if ( ! function_exists( 'visualize_paging_nav' ) ) :
/**
 * Display navigation to next/previous set of posts when applicable.
 */
function visualize_paging_nav() {
	// Don't print empty markup if there's only one page.
	if ( $GLOBALS['wp_query']->max_num_pages < 2 ) {
		return;
	}
	?>
	<nav class="navigation paging-navigation" role="navigation" aria-label="<?php _e( 'Posts navigation', 'visualize' ); ?>">
		<div class="inner">
			<ul class="nav-links">

				<?php if ( get_next_posts_link() ) : ?>
				<li class="nav-previous"><?php next_posts_link( __( '<span class="meta-nav">&larr;</span> Older posts', 'visualize' ) ); ?></li>
				<?php endif; ?>

				<?php if ( get_previous_posts_link() ) : ?>
				<li class="nav-next"><?php previous_posts_link( __( 'Newer posts <span class="meta-nav">&rarr;</span>', 'visualize' ) ); ?></li>
				<?php endif; ?>

			</ul><!-- .nav-links -->
		</div>
	</nav><!-- .navigation -->
	<?php
}
endif;

if ( ! function_exists( 'visualize_post_nav' ) ) :
/**
 * Display navigation to next/previous post when applicable.
 */
function visualize_post_nav() {
	// Don't print empty markup if there's nowhere to navigate.
	$previous = ( is_attachment() ) ? get_post( get_post()->post_parent ) : get_adjacent_post( false, '', true );
	$next     = get_adjacent_post( false, '', false );

	if ( ! $next && ! $previous ) {
		return;
	}
	?>
	<nav class="navigation post-navigation" role="navigation">
		<div class="inner">
			<h1 class="screen-reader-text"><?php _e( 'Post navigation', 'visualize' ); ?></h1>
			<ul class="nav-links">
				<?php
					previous_post_link( '<li class="nav-previous">%link</li>', _x( '<span class="meta-nav">&larr;</span>&nbsp;%title', 'Previous post link', 'visualize' ) );
					next_post_link(     '<li class="nav-next">%link</li>',     _x( '%title&nbsp;<span class="meta-nav">&rarr;</span>', 'Next post link',     'visualize' ) );
				?>
			</ul><!-- .nav-links -->
		</div><!-- .inner-->
	</nav><!-- .navigation -->
	<?php
}
endif;

if ( ! function_exists( 'visualize_posted_on' ) ) :
/**
 * Prints HTML with meta information for the current post-date/time and author.
 */
function visualize_posted_on() {

	$time_string = '<span class="entry-date"><a href="%1$s"><time class="published" datetime="%2$s">%3$s</time>';
	if ( get_the_time( 'U' ) !== get_the_modified_time( 'U' ) ) {
		$time_string .= '<time class="updated" datetime="%4$s">%5$s</time>';
	}
	$time_string .= '</a></span>';

	$posted_on = sprintf( $time_string,
		esc_url( get_permalink() ),
		esc_attr( get_the_date( 'c' ) ),
		esc_html( get_the_date() ),
		esc_attr( get_the_modified_date( 'c' ) ),
		esc_html( get_the_modified_date() )
	);

	if ( is_multi_author() ) {
		$byline = sprintf(
			_x( 'By %s', 'post author', 'visualize' ),
			'<span class="author vcard"><a class="url fn n" href="' . esc_url( get_author_posts_url( get_the_author_meta( 'ID' ) ) ) . '">' . esc_html( get_the_author() ) . '</a></span>'
		);
	} else {
		$byline = '';
	}

	echo ( $byline === '' ) ? '' : '<p class="byline">' . $byline . '</p>'; 
	echo '<p class="posted-on">' . $posted_on . '</p>';

	edit_post_link( __( 'Edit', 'visualize' ), '<p class="edit-link">', '</p>' );

}
endif;

/**
 * Returns true if a blog has more than 1 category.
 *
 * @return bool
 */
function visualize_categorized_blog() {
	if ( false === ( $all_the_cool_cats = get_transient( 'visualize_categories' ) ) ) {
		// Create an array of all the categories that are attached to posts.
		$all_the_cool_cats = get_categories( array(
			'fields'     => 'ids',
			'hide_empty' => 1,

			// We only need to know if there is more than one category.
			'number'     => 2,
		) );

		// Count the number of categories that are attached to the posts.
		$all_the_cool_cats = count( $all_the_cool_cats );

		set_transient( 'visualize_categories', $all_the_cool_cats );
	}

	if ( $all_the_cool_cats > 1 ) {
		// This blog has more than 1 category so visualize_categorized_blog should return true.
		return true;
	} else {
		// This blog has only 1 category so visualize_categorized_blog should return false.
		return false;
	}
}

/**
 * Flush out the transients used in visualize_categorized_blog.
 */
function visualize_category_transient_flusher() {
	// Like, beat it. Dig?
	delete_transient( 'visualize_categories' );
}
add_action( 'edit_category', 'visualize_category_transient_flusher' );
add_action( 'save_post',     'visualize_category_transient_flusher' );


/**
 * Display the footer credits area.
 */
function visualize_footer_credits() {
	$sep_span = '<span class="sep" role="separator" aria-hidden="true"> | </span>';
	?>&copy <?php echo date('Y'); ?> <a href="<?php echo esc_url( home_url() ); ?>" id="footer-copy-name"><?php echo esc_html( get_theme_mod( 'copy_name', get_bloginfo( 'name' ) ) ); ?></a>
	<?php if( get_theme_mod( 'powered_by_wp', true ) || is_customize_preview() ) { ?>
		<span class="wordpress-credit" <?php if ( ! get_theme_mod( 'powered_by_wp', true ) && is_customize_preview() ) { echo 'style="display: none;"'; } ?>>
			<?php echo $sep_span; ?><a href="http://wordpress.org/" rel="generator"><?php printf( __( 'Proudly powered by %s', 'visualize' ), 'WordPress' ); ?></a>
		</span>
	<?php } if( get_theme_mod( 'theme_meta', false ) || is_customize_preview() ) { ?>
		<span class="theme-credit" <?php if ( ! get_theme_mod( 'theme_meta', false ) && is_customize_preview() ) { echo 'style="display: none;"'; } ?>>
			<?php echo $sep_span; printf( __( 'Theme: %1$s by %2$s.', 'visualize' ), 'Visualize', '<a href="https://celloexpressions.com/" rel="designer">Nick Halsey</a>' ); ?>
		</span>
	<?php } if ( function_exists( 'the_privacy_policy_link' ) ) {
		the_privacy_policy_link( $sep_span, '' );
	}
}