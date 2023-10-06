<?php
/**
 * The template for displaying product content within loops.
 *
 * Override this template by copying it to yourtheme/woocommerce/content-product.php
 *
 * @author  WooThemes
 * @package WooCommerce/Templates
 * @version 3.6.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

global $product, $woocommerce_loop;


// Action: woocommerce_after_shop_loop_item_title
remove_action( 'woocommerce_after_shop_loop_item_title', 'woocommerce_template_loop_price', 10 );
remove_action( 'woocommerce_after_shop_loop_item_title', 'woocommerce_template_loop_rating', 5 );

remove_action('woocommerce_shop_loop_item_title', 'woocommerce_template_loop_product_title', 10);
remove_action('woocommerce_after_shop_loop_item', 'woocommerce_template_loop_add_to_cart', 10);

// Ensure visibility
if ( ! $product || ! $product->is_visible() ) {
	return;
}

// Extra post classes
$classes = array();

$classes[] = 'product-block product';

?>

<div <?php post_class( $classes ); ?>>
	<div class="product-block-inner clearfix">
		<?php do_action( 'woocommerce_before_shop_loop_item' ); ?>

		<div class="product-thumbnail">
		 	<div class="product-thumbnail-inner">
          <?php 
             /**
              * woocommerce_before_shop_loop_item_title hook
              *
              * @hooked woocommerce_show_product_loop_sale_flash - 10
              * @hooked woocommerce_template_loop_product_thumbnail - 10
              */
             do_action( 'woocommerce_before_shop_loop_item_title' );
          ?>
       </div>   
         
        <div class="shop-loop-actions ">	
				  <div class="add-to-cart">
            <?php woocommerce_template_loop_add_to_cart(); ?>
          </div> 
          <?php
  					/**
  					 * woocommerce_after_shop_loop_item hook
  					 *
  					 * @hooked woocommerce_template_loop_add_to_cart - 10
  					 */
  					do_action( 'woocommerce_after_shop_loop_item' );
				  ?>

          <?php
              if( class_exists( 'YITH_WCWL' ) ) {
                echo do_shortcode( '[yith_wcwl_add_to_wishlist]' );
              }
          ?>   
    
          <div class="quickview">
            <a href="javascript:void(0);" data-product_id="<?php echo esc_attr( $product->get_id() ) ?>" class="btn-quickview product_type_<?php echo esc_attr( $product->get_type() ) ?>"><span class="far fa-eye"></span></a>
		      </div>
        </div>

		</div>

      <?php 
         /**
          * woocommerce_shop_loop_item_title hook
          *
          * @hooked woocommerce_template_loop_product_title - 10
          */
         do_action( 'woocommerce_shop_loop_item_title' );
      ?>      

      <?php
            /**
             * woocommerce_after_shop_loop_item_title hook
             *
             * @hooked woocommerce_template_loop_rating - 5
             * @hooked woocommerce_template_loop_price - 10
             */
            do_action( 'woocommerce_after_shop_loop_item_title' );
         ?>
		<div class="product-meta">
      <div class="clearfix"></div>
			<div class="shop-loop-price">
        <?php woocommerce_template_loop_price(); ?>
      </div>
      <h3 class="shop-loop-title"><a href="<?php esc_url( the_permalink() ); ?>"><?php the_title(); ?></a></h3>
			
			<div class="shop-loop-after-title">	
				<div class="shop-loop-after">	
					<?php
						/**
						 * woocommerce_after_shop_loop_item hook
						 *
						 * @hooked woocommerce_template_loop_add_to_cart - 10
						 */
						do_action( 'woocommerce_after_shop_loop_item' );
					?>
				</div>
			</div>

		</div>
	</div>	
</div>
