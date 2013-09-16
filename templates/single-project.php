<?php
/**
 * The Template for displaying all single project.
 * Based on WP TwentyTwelve
 */

// custom ordering for the project post type.
global $wp_query;
$args = array_merge( $wp_query->query_vars, array( 'post_type' => 'project', 'orderby' => 'title', 'order' => 'ASC' ) );
query_posts( $args );

get_header(); ?>

	<div id="primary" class="site-content">
		<div id="content" role="main">

			<?php while ( have_posts() ) : the_post(); ?>

				<?php /*get_template_part( 'content', get_post_format() );*/ ?>
				<?php include(plugin_dir_path( __FILE__ ) . '/content-project.php'); ?>

				<nav class="nav-single">
					<h3 class="assistive-text"><?php _e( 'Project navigation', WP_OPENDATA_TEXT_DOMAIN ); ?></h3>
					<span class="nav-previous"><?php wp_opendata_previous_post( '%link', '<span class="meta-nav">' . _x( '&larr;', 'Previous dataset link', WP_OPENDATA_TEXT_DOMAIN ) . '</span> %title', 'project' ); ?></span>
					<span class="nav-next"><?php wp_opendata_next_post( '%link', '%title <span class="meta-nav">' . _x( '&rarr;', 'Next dataset link', WP_OPENDATA_TEXT_DOMAIN ) . '</span>', 'project' ); ?></span>					
				</nav><!-- .nav-single -->

				<?php comments_template( '', true ); ?>

			<?php endwhile; // end of the loop. ?>

		</div><!-- #content -->
	</div><!-- #primary -->

<?php get_sidebar(); ?>
<?php 
wp_reset_query();
get_footer(); 
?>