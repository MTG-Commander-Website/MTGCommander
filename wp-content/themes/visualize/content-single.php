<?php
/**
 * @package Visualize
 */
?>

<article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
	<div class="entry-visual" <?php echo visualize_post_thumbnail_style(); ?>>
		<?php if ( has_post_thumbnail() ) {
			the_post_thumbnail( 'post-thumbnail', array( 'class' => 'screen-reader-text' ) );
		} ?>
	</div><!-- .entry-visual -->

	<div class="entry-content">
		<header class="entry-header">
			<?php the_title( '<h1 class="entry-title">', '</h1>' ); ?>
		</header><!-- .entry-header -->
			<div class="single-content">
				<?php /* translators: %s is the post title */
				the_content( sprintf( __( 'Continue reading %s', 'visualize' ),
					'<span class="screen-reader-text">' . get_the_title() .
					' </span><span class="meta-nav">&rarr;</span>' ) );
				wp_link_pages( array(
				'before' => '<div class="page-links">' . __( 'Pages:', 'visualize' ),
				'after'  => '</div>',
				) );
			?>
			</div>
	</div><!-- .entry-content -->
	<footer class="entry-footer entry-meta">
		<?php if ( is_multi_author() ) : ?>
			<p class="author vcard"><a class="url fn n" href="<?php echo esc_url( get_author_posts_url( get_the_author_meta( 'ID' ) ) ); ?>"><?php echo esc_html( get_the_author() ); ?></a></p>
		<?php endif;

			$time_string = '<p class="entry-date"><time class="published" datetime="%1$s">%2$s</time>';
			if ( get_the_time( 'U' ) !== get_the_modified_time( 'U' ) ) {
				$time_string .= '<time class="updated" datetime="%3$s">%4$s</time>';
			}
			$time_string .= '</p>';

			printf( $time_string,
				esc_attr( get_the_date( 'c' ) ),
				esc_html( get_the_date() ),
				esc_attr( get_the_modified_date( 'c' ) ),
				esc_html( get_the_modified_date() )
			);

			if ( visualize_categorized_blog() ) {
				echo get_the_category_list();
			}
			echo get_the_tag_list( '<ul class="post-tags"><li>', '</li><li>', '</li></ul>' );

			if ( has_post_thumbnail() ) {
				if ( function_exists( 'wp_get_original_image_path' ) ) {
					// WordPress 5.3 resizes even the "full" image. Get the actual original uploaded full-size image.
					$image = wp_get_original_image_path( get_post_thumbnail_id( $post->ID ) );
					$upload_dir = wp_get_upload_dir();
					$image = str_replace( $upload_dir['basedir'], $upload_dir['baseurl'], $image );
				} else {
					$image = wp_get_attachment_image_src( get_post_thumbnail_id( $post->ID ), 'full' );
					$image = $image[0]; // URL
				}
				echo '<p><a class="button post-image" href="' . esc_url( $image ) . '">' . __( 'View Full-size Image', 'visualize' ) . '</a></p>';
			}
			edit_post_link( __( 'Edit', 'visualize' ), '<p class="edit-link">', '</p>' );
		?>
	</footer><!-- .entry-footer -->

</article><!-- #post-## -->