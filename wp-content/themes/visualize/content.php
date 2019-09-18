<?php
/**
 * @package Visualize
 */
?>

<article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
	<div class="entry-visual" <?php visualize_post_thumbnail_style(); ?>>
		<?php if ( has_post_thumbnail() ) {
			the_post_thumbnail( 'post-thumbnail', array( 'class' => 'screen-reader-text' ) );
		} ?>
	</div><!-- .entry-visual -->
	<div class="entry-content">
		<header class="entry-header">
				<?php the_title( sprintf( '<h1 class="entry-title"><a href="%s" rel="bookmark">', esc_url( get_permalink() ) ), '</a></h1>' ); ?>
		</header><!-- .entry-header -->
		<?php /* translators: %s is the post title */
		the_content( sprintf( __( 'Continue reading %s', 'visualize' ),
			'<span class="screen-reader-text">' . get_the_title() .
			' </span><span class="meta-nav">&rarr;</span>' ) ); ?>
		<footer class="entry-footer">
			<?php visualize_posted_on(); ?>
		</footer><!-- .entry-footer -->
	</div><!-- .entry-content -->
</article><!-- #post-## -->