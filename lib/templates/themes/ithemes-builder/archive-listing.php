<?php

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

function render_content() {
	if ( have_posts() ) : ?>
		<div class="loop">
			<div class="loop-header">
				<h4 class="loop-title">
					<?php
						the_post();
						
						if ( is_tax() ) { // Tag Archive
							$title = sprintf( __( 'Property in %s', 'epl' ), builder_get_tax_term_title() );
						}
						else if ( function_exists( 'is_post_type_archive' ) && is_post_type_archive() && function_exists( 'post_type_archive_title' ) ) { // Post Type Archive
							$title = post_type_archive_title( '', false );
						}
						else { // Default catchall just in case
							$title = __( 'Archive', 'epl' );
						}
						
						if ( is_paged() )
							printf( '%s &ndash; Page %d', $title, get_query_var( 'paged' ) );
						else
							echo $title;
						
						rewind_posts();
					?>
				</h4>
			</div>
			
			<div class="loop-content">
				<?php
					while ( have_posts() ) : // The Loop
						the_post();
						
						echo epl_property_blog();
					endwhile; // end of one post
				?>
			</div>
			
			<div class="loop-footer">
				<!-- Previous/Next page navigation -->
				<div class="loop-utility clearfix">
					<div class="alignleft"><?php previous_posts_link( __( '&laquo; Previous Page', 'epl' ) ); ?></div>
					<div class="alignright"><?php next_posts_link( __( 'Next Page &raquo;', 'epl' ) ); ?></div>
				</div>
			</div>
		</div>
		<?php
	else :
		//do_action( 'builder_template_show_not_found' );
		?><div class="hentry">
			<div class="entry-header clearfix">
				<h3 class="entry-title"><?php _e('Page Not Found', 'epl'); ?></h3>
			</div>
			
			<div class="entry-content clearfix">
				<p><?php _e('This area is now editable through our EPL plugin.', 'epl'); ?></p>
				<p><?php _e('Try searching for the page you are looking for or using the navigation in the header or sidebar', 'epl'); ?></p>
			</div>
		</div><?php		
	endif;
}
add_action( 'builder_layout_engine_render_content', 'render_content' );
do_action( 'builder_layout_engine_render', basename( __FILE__ ) );
