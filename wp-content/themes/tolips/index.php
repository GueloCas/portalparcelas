<?php
/**
 *
 * @author     Gaviasthemes Team     
 * @copyright  Copyright (C) 2021 gaviasthemes. All Rights Reserved.
 * @license    GNU/GPL v2 or later http://www.gnu.org/licenses/gpl-2.0.html
 * 
 */

get_header();
$class_main = 'col-xl-12 col-lg-12 col-md-12 col-sm-12 col-xs-12';
if(is_active_sidebar('default_sidebar')){ 
  $class_main = 'col-xl-8 col-lg-8 col-md-12 col-sm-12 col-xs-12';
}

?>

<section id="wp-main-content" class="clearfix main-page">
  <?php do_action( 'tolips_before_page_content' ); ?>
  <div class="container"> 
    <div class="main-page-content main-page-index row">
      
      <!-- Main content -->
      <div class="content-page <?php echo esc_attr($class_main) ?>">      
        <div id="wp-content" class="wp-content content-page-index">   
          <div class="blog-grid-style-2 gva-posts-grid">
            <div class="lg-block-grid-2 md-block-grid-2 sm-block-grid-2 xs-block-grid-1 post-masonry-style post-masonry-index">
              <?php if ( have_posts() ) : ?>
                <?php
                   // Start the Loop.
                   while ( have_posts() ) : the_post();

                      /*
                       * Include the post format-specific template for the content. If you want to
                       * use this in a child theme, then include a file called called content-___.php
                       * (where ___ is the post format) and that will be used instead.
                       */
                      echo '<div class="item-columns item-masory">';
                        get_template_part( 'templates/content/item', 'post-style-1' );
                      echo '</div>';  

                   endwhile;
                   // Previous/next page navigation.         

                else :
                   // If no content, include the "No posts found" template.
                   get_template_part( 'content', 'none' );

                endif;
              ?>
            </div>
          </div>  

         <div class="pagination">
            <?php echo tolips_pagination(); ?>
         </div>
        </div>  
      </div>  

      <!-- Left sidebar -->
    
       <div class="sidebar wp-sidebar sidebar-left col-xl-4 col-lg-4 col-md-12 col-sm-12 col-xs-12">
          <?php do_action( 'tolips_before_sidebar' ); ?>
          <div class="sidebar-inner">
            <?php get_sidebar(); ?>
          </div>
          <?php do_action( 'tolips_after_sidebar' ); ?>
       </div>
    </div>
  </div>              
  <?php do_action( 'tolips_after_page_content' ); ?>
</section>
<?php get_footer(); ?>
