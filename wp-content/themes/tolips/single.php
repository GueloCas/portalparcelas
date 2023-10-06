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

    $page_id = tolips_id();
    $default_sidebar_config = tolips_get_option('single_post_sidebar', 'right-sidebar'); 
    $default_left_sidebar = tolips_get_option('single_post_left_sidebar', 'default_sidebar');
    $default_right_sidebar = tolips_get_option('single_post_right_sidebar', 'default_sidebar');

    $sidebar_layout_config = get_post_meta($page_id, 'tolips_sidebar_config', true);
    $left_sidebar = get_post_meta($page_id, 'tolips_left_sidebar', true);
    $right_sidebar = get_post_meta($page_id, 'tolips_right_sidebar', true);

    if ($sidebar_layout_config == "") {
        $sidebar_layout_config = $default_sidebar_config;
    }
    if ($left_sidebar == "") {
        $left_sidebar = $default_left_sidebar;
    }
    if ($right_sidebar == "") {
        $right_sidebar = $default_right_sidebar;
    }

   $left_sidebar_config  = array('active' => false);
   $right_sidebar_config = array('active' => false);
   $main_content_config  = array('class' => 'col-lg-12 col-xs-12');

    $sidebar_config = tolips_sidebar_global($sidebar_layout_config, $left_sidebar, $right_sidebar);
   
    extract($sidebar_config);

 ?>
<section id="wp-main-content" class="clearfix main-page">
    <?php do_action( 'tolips_before_page_content' ); ?>
   <div class="container">  
    <div class="main-page-content row">
         <div class="content-page <?php echo esc_attr($main_content_config['class']); ?>">      
            <div id="wp-content" class="wp-content clearfix">
                <?php while ( have_posts() ) : the_post(); ?>

                    <?php get_template_part( 'content', get_post_format() ); ?>

                <?php endwhile; // end of the loop. ?>

                <?php 
                    if( comments_open() || get_comments_number() ) {
                        comments_template();
                    }
                ?>
                <?php tolips_post_nav(); ?>
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
