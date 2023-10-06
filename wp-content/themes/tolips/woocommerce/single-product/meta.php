<?php
/**
 * Single Product Meta
 *
 * @author 		WooThemes
 * @package 	WooCommerce/Templates
 * @version     3.0.0
 */

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

global $post, $product;

$sku = $product->get_sku() ? $product->get_sku() : esc_html__( 'n/a', 'tolips' );
?>
<div class="product_meta">

	<?php do_action( 'woocommerce_product_meta_start' ); ?>

	<?php if ( wc_product_sku_enabled() && ( $product->get_sku() || $product->is_type( 'variable' ) ) ) : ?>

		<span class="sku_wrapper"><?php esc_html__( '<span class="title">SKU:</span>', 'tolips' ); ?> <span class="sku" itemprop="sku"><?php echo esc_html($sku); ?></span>.</span>

	<?php endif; ?>

     <?php echo wc_get_product_category_list( $product->get_id(), ', ', '<span class="posted_in">' . _n( '<span class="title">Category:</span>', '<span class="title">Categories:</span>', count( $product->get_category_ids() ), 'tolips' ) . ' ', '</span>' ); ?>

   <?php echo wc_get_product_tag_list( $product->get_id(), ', ', '<span class="tagged_as">' . _n( '<span class="title">Tag:</span>', '<span class="title">Tags:</span>', count( $product->get_tag_ids() ), 'tolips' ) . ' ', '</span>' ); ?>

	<?php do_action( 'woocommerce_product_meta_end' ); ?>

</div>

