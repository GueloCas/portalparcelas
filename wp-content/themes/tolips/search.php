<?php
/**
 * $Desc
 *
 * @author     Gaviasthemes Team     
 * @copyright  Copyright (C) 2021 gaviasthemes. All Rights Reserved.
 * @license    GNU/GPL v2 or later http://www.gnu.org/licenses/gpl-2.0.html
 * 
*/
  	get_header(); 

  	$sidebar_layout_config = tolips_get_option('archive_post_sidebar', ''); 
  	$left_sidebar = tolips_get_option('archive_post_left_sidebar', '');  
  	$right_sidebar = tolips_get_option('archive_post_right_sidebar', ''); 

	$left_sidebar_config  = array('active' => false);
	$right_sidebar_config = array('active' => false);
	$main_content_config  = array('class' => 'col-lg-12 col-lg-12 col-md-12 col-sm-12 col-xs-12');
	 
	$sidebar_config = tolips_sidebar_global($sidebar_layout_config, $left_sidebar, $right_sidebar);
	
	extract($sidebar_config);

?>

<section id="wp-main-content" class="clearfix main-page title-layout-standard">
  	<?php do_action( 'tolips_before_page_content' ); ?>
  	<div class="container">
	 	<div class="row main-page-content">
			<div class="content-page <?php echo esc_attr($main_content_config['class']); ?>"> 
			  	<div id="wp-content" class="wp-content">
				 	<?php  if ( have_posts() ) : ?>
						<div class="post-area posts-grids results-search clearfix post-items">
						  	<div class="lg-block-grid-2 md-block-grid-2 sm-block-grid-2 xs-block-grid-1 post-masonry-style">
							 	
							 	<?php  while ( have_posts() ) : the_post(); ?>
									<div class="item-columns item-masory">
                     			<?php get_template_part( 'templates/content/item', 'post-style-1' ); ?> 
                   			</div>
							 	<?php endwhile; ?> 

						  	</div>       
						</div>                    
				 	<?php else: ?>
						<div class="search-no-results-content">
							<div class="message"><?php echo esc_html__( 'Sorry, but nothing matched your search terms. Please try again with some different keywords.', 'tolips' ); ?></div>
							<?php get_search_form() ?>
								
						</div>
				 	<?php endif ?>
				 	<div class="pagination">
						<?php echo tolips_pagination(); ?>
				  	</div>
			  	</div>
			</div>

			<!-- Left sidebar -->
			<?php if($left_sidebar_config['active']): ?>
			  	<div class="sidebar wp-sidebar sidebar-left <?php echo esc_attr($left_sidebar_config['class']); ?>">
				 	<?php do_action( 'tolips_before_sidebar' ); ?>
				 	<div class="sidebar-inner">
						<?php dynamic_sidebar($left_sidebar_config['name'] ); ?>
				 	</div>
				 	<?php do_action( 'tolips_after_sidebar' ); ?>
			  	</div>
			<?php endif ?>

			<!-- Right Sidebar -->
			<?php if($right_sidebar_config['active']): ?>
			  	<div class="sidebar wp-sidebar sidebar-right <?php echo esc_attr($right_sidebar_config['class']); ?>">
				 	<?php do_action( 'tolips_before_sidebar' ); ?>
					<div class="sidebar-inner">
					  <?php dynamic_sidebar($right_sidebar_config['name'] ); ?>
					</div>
				 	<?php do_action( 'tolips_after_sidebar' ); ?>
			  	</div>
			<?php endif ?>

	 	</div>
  	</div>
  	<?php do_action( 'tolips_after_page_content' ); ?>
</section>
<?php get_footer(); ?>
