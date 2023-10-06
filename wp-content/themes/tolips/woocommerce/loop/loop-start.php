<?php
/**
 * Product Loop Start
 *
 * @author 		WooThemes
 * @package 	WooCommerce/Templates
 * @version     3.3.0
 */

global $woocommerce_loop; 
$columns_lg = tolips_get_option('product_columns_lg', '3');
$columns_md = tolips_get_option('product_columns_md', '3');
$columns_sm = tolips_get_option('product_columns_sm', '2');
$columns_xs = tolips_get_option('product_columns_xs', '1');

?>
<div class="clearfix"></div>
<div class="products_wrapper grid-view">
	<div class="products lg-block-grid-<?php echo esc_attr($columns_lg); ?> md-block-grid-<?php echo esc_attr($columns_md); ?> sm-block-grid-<?php echo esc_attr($columns_sm); ?> xs-block-grid-<?php echo esc_attr($columns_xs); ?>">