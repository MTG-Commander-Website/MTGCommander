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
				<?php the_title( sprintf( '<h2 class="entry-title"><a href="%s" rel="bookmark">', esc_url( get_permalink() ) ), '</a></h2>' ); ?>
		</header><!-- .entry-header -->
		<?php /* translators: %s is the post title */
		the_excerpt( sprintf( __( 'Continue reading %s', 'visualize' ),
			'<span class="screen-reader-text">' . get_the_title() .
			' </span><span class="meta-nav">&rarr;</span>' ) ); ?>
	</div><!-- .entry-content -->
</article><!-- #post-## -->