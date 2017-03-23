<?php
/**
 * Template Name: Full Width Practiques, No Sidebar
 * The template for displaying a page without a sidebar.
 *
 * @package Libre
 */

get_header(); ?>


<div id="primary" class="content-area">
		<main id="main" class="site-main" role="main">
		<?php while ( have_posts() ) : the_post(); ?>
			<?php get_template_part( 'template-parts/content', 'page' ); ?>
  			<?php
					// If comments are open or we have at least one comment, load up the comment template.
					if ( comments_open() || get_comments_number() ) :
						comments_template();
					endif;
				?>
		<?php endwhile; // End of the loop. ?>
            
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


		</main><!-- #main -->
	</div><!-- #primary -->    
<?php get_footer(); ?>