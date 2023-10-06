<?php
/**
 *	The template for displaying quickview product content
 */
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

global $post, $product;

$classes = 'product single-product' . ' product-' . $product->get_type();

?>

<div  id="product-<?php the_ID(); ?>" <?php post_class( $classes ); ?>>
    <div class="product-wrapper clearfix">
        <div class="product-single-inner row">
            <div class="column col-md-5 col-sm-6 col-xs-12 product_image_wrapper">
                <div class="column-inner">
                    <div class="image_frame scale-with-grid">
                        <?php wc_get_template( 'quickview/product-image.php' ); ?>
                    </div>
                </div>  
            </div>

            <div class="column col-md-7 col-sm-6 col-xs-12 summary entry-summary">
                <div class="column-inner">
                    <?php
                        woocommerce_template_single_title();
                        woocommerce_template_single_rating();
                        woocommerce_template_single_price();
                        woocommerce_template_single_excerpt();
                        woocommerce_template_single_add_to_cart();
                        woocommerce_template_single_meta();
                        woocommerce_template_single_sharing();
                    ?>
                </div>
            </div>  
        </div>
    </div>
    
    <?php
        /**
         * woocommerce_after_single_product_summary hook
         *
         * @hooked woocommerce_output_product_data_tabs - 10
         * @hooked woocommerce_output_related_products - 20
         */
        
        //do_action( 'woocommerce_after_single_product_summary' );
    ?>

    <meta itemprop="url" content="<?php the_permalink(); ?>" />

</div>


