<?php
/**
 * Template part for displaying posts.
 *
 * @package Libre
 */

?>

<article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
	<header class="entry-header">
		
		<?php $widont = remove_filter( 'the_title', 'widont' ); ?>
			<?php the_title( sprintf( '<h2 class="entry-title"><a href="%s" rel="bookmark">', esc_url( get_permalink() ) ), '</a></h2>' ); ?>
		<?php if ( $widont ) add_filter( 'the_title', 'widont' ); ?>
		
		<?php if ( 'post' == get_post_type() ) : ?>
		<div class="entry-meta">
			<?php libre_posted_on(); ?>
		</div><!-- .entry-meta -->
		<?php endif; ?>
	</header><!-- .entry-header -->

	<div class="entry-content">
		<?php
			/* translators: %s: Name of current post */
			the_content( sprintf(
				wp_kses( __( 'Continue reading %s <span class="meta-nav">&rarr;</span>', 'libre' ), array( 'span' => array( 'class' => array() ) ) ),
				the_title( '<span class="screen-reader-text">"', '"</span>', false )
			) );
		?>

		<?php
			wp_link_pages( array(
				'before' => '<div class="page-links">' . esc_html__( 'Pages:', 'libre' ),
				'after'  => '</div>',
			) );
		?>
        
<div class="content-posts">            
<?php // Display blog posts on any page @ http://m0n.co/l
 $temp = $wp_query; $wp_query= null;
 $wp_query = new WP_Query(); $wp_query->query('posts_per_page=10' . '&paged='.$paged);
 while ($wp_query->have_posts()) : $wp_query->the_post(); ?>

<div class="recent_posts" style="position:relative; z-index:100">
 <div class="header-posts">
 <h2><?php echo get_avatar( get_the_author_meta( 'ID' ), 32 ); the_author(); ?> </h2> <span class="timestamp"><?php echo human_time_diff(get_the_time('U'), current_time('timestamp')) . ' ago'; ?></span>
 </div>
 <h4><a href="<?php the_permalink(); ?>" title="Read more"><?php the_title(); ?></a></h4>
 <?php the_excerpt(); ?>
 
 <span class="number-comentaries"><?php echo $my_var = get_comments_number( $post_id ); ?> comments </span>

</div>
<?php endwhile; ?>
</div> 

 </div><!-- .entry-content -->
</article><!-- #post-## -->
