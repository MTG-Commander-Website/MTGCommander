<?php
/**
 * The template for displaying image attachments
 *
 * @package Visualize
 */

get_header(); ?>

	<div id="primary" class="content-area">
		<main id="main" class="site-main" role="main">

			<?php
				// Start the loop.
				while ( have_posts() ) : the_post();
			?>

			<article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
				<div class="entry-visual" <?php echo visualize_post_thumbnail_style(); ?>></div><!-- .entry-visual -->

				<div class="entry-content">
					<header class="entry-header">
						<?php the_title( '<h1 class="entry-title">', '</h1>' ); ?>
					</header><!-- .entry-header -->
						<div class="single-content">
							<?php if ( has_excerpt() ) : ?>
								<div class="entry-caption">
									<?php the_excerpt(); ?>
								</div><!-- .entry-caption -->
							<?php endif; ?>
							<?php the_content(); ?>
							<?php
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

						$image = wp_get_attachment_image_src( $post->ID, 'full' );
						echo '<p><a class="button post-image" href="' . $image[0] . '">' . __( 'View Full-size Image', 'visualize' ) . '</a></p>';

						edit_post_link( __( 'Edit', 'visualize' ), '<p class="edit-link">', '</p>' );
					?>
				</footer><!-- .entry-footer -->

			</article><!-- #post-## -->

			<?php visualize_post_nav(); ?>

			<?php
				// If comments are open or we have at least one comment, load up the comment template
				if ( comments_open() || '0' != get_comments_number() ) :
					comments_template();
				endif;
			?>

		<?php endwhile; // end of the loop. ?>

		</main><!-- #main -->
	</div><!-- #primary -->

<?php get_footer(); ?>
