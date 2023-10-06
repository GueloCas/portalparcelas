<?php
/**
 * The Template for displaying all single products.
 *
 * Override this template by copying it to yourtheme/woocommerce/single-product.php
 *
 * @author 		WooThemes
 * @package 	WooCommerce/Templates
 * @version     1.6.4
 */

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly
	get_header(); 
  
	$page_id = tolips_id();

	$default_sidebar_config = tolips_get_option('product_sidebar_config', ''); 
	$default_left_sidebar = tolips_get_option('product_left_sidebar', ''); 
	$default_right_sidebar = tolips_get_option('product_right_sidebar', ''); 

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
    <?php
      /**
       * woocommerce_before_main_content hook
       *
       * @hooked woocommerce_output_content_wrapper - 10 (outputs opening divs for the content)
       * @hooked woocommerce_breadcrumb - 20
       */
      do_action( 'woocommerce_before_main_content' );
    ?>
    
   <div class="container">	
   	 <div class="main-page-content row">
         <div class="content-page <?php echo esc_attr($main_content_config['class']); ?>">      
           
     			<?php while ( have_posts() ) : the_post(); ?>

     				<?php wc_get_template_part( 'content', 'single-product' ); ?>

     			<?php endwhile; // end of the loop. ?>
     				
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

    <?php
      /**
       * woocommerce_after_main_content hook
       *
       * @hooked woocommerce_output_content_wrapper_end - 10 (outputs closing divs for the content)
       */
      do_action( 'woocommerce_after_main_content' );
    ?>

    <div class="related-section">
      <div class="container">
        <?php woocommerce_output_related_products() ?>
      </div>
    </div>
</section>

<?php get_footer(); ?>