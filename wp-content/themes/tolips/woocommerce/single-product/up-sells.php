<?php
/**
 * Single Product Up-Sells
 *
 * @author 		WooThemes
 * @package 	WooCommerce/Templates
 * @version     3.0.0
 */

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

global $product, $woocommerce_loop;

$upsells = $product->get_upsell_ids();

if ( sizeof( $upsells ) == 0 ) return;

$meta_query = WC()->query->get_meta_query();

$args = array(
	'post_type'           => 'product',
	'ignore_sticky_posts' => 1,
	'no_found_rows'       => 1,
	'posts_per_page'      => $posts_per_page,
	'orderby'             => $orderby,
	'post__in'            => $upsells,
	'post__not_in'        => array( $product->get_id() ),
	'meta_query'          => $meta_query
);

$products = new WP_Query( $args );

$woocommerce_loop['columns'] = $columns;

if ( $products->have_posts() ) : ?>

	<div class="upsells widget products">

		<h2 class="widget-title"><?php echo esc_html(tolips_get_option('upsell_heading_text', 'You may also like&hellip;' )) ?></h2>
		<div class="widget-content">
			<div class="lg-block-grid-3 md-block-grid-3 sm-block-grid-2">
				<?php while ( $products->have_posts() ) : $products->the_post(); ?>
					<?php wc_get_template_part( 'content', 'product' ); ?>
				<?php endwhile; // end of the loop. ?>
			</div>
		</div>	
		
	</div>

<?php endif;

wp_reset_postdata();
