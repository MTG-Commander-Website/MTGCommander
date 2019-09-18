<?php
/**
 * The template used for displaying page content in page.php
 *
 * @package Visualize
 */
?>

<article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
	<div class="entry-visual" <?php echo visualize_post_thumbnail_style(); ?>></div><!-- .entry-visual -->

	<div class="entry-content">
		<header class="entry-header">
			<?php the_title( '<h1 class="entry-title">', '</h1>' ); ?>
		</header><!-- .entry-header -->

		<?php the_content();
		wp_link_pages( array(
			'before' => '<div class="page-links">' . __( 'Pages:', 'visualize' ),
			'after'  => '</div>',
		) );
		edit_post_link( __( 'Edit', 'visualize' ), '<span class="edit-link">', '</span>' ); ?>
	</div><!-- .entry-content -->
</article><!-- #post-## -->
