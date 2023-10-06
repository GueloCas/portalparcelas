<?php
/**
 * Loop Add to Cart
 *
 * @author 		WooThemes
 * @package 	WooCommerce/Templates
 * @version     3.3.0
 */

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

global $product;

echo apply_filters( 'woocommerce_loop_add_to_cart_link',
	sprintf( '<a href="%s" data-quantity="%s" data-product_id="%s" data-product_sku="%s" class="button %s product_type_%s ajax_add_to_cart"> %s</a>',
		esc_url( $product->add_to_cart_url() ),
		esc_attr( isset( $args['quantity'] ) ? $args['quantity'] : 1 ),
		esc_attr( $product->get_id() ),
		esc_attr( $product->get_sku() ),
		$product->is_purchasable() ? 'add_to_cart_button' : '',
		$product->get_type() == 'lt_package' ? esc_attr( $product->get_type() ) . ' product_type_simple' : esc_attr( $product->get_type() ),
		esc_html( $product->add_to_cart_text() )
	),
   $product,
   $args
);
 
