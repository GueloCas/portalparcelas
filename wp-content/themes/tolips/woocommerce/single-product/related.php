<?php
/**
 * Related Products
 *
 * @author 		WooThemes
 * @package 	WooCommerce/Templates
 * @version     3.9.0
 */

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

global $product;
$posts_per_page = 6;
$related = wc_get_related_products( $product->get_id(), $posts_per_page );

if ( sizeof( $related ) == 0 ) return;

$args = apply_filters( 'woocommerce_related_products_args', array(
	'post_type'				=> 'product',
	'ignore_sticky_posts'	=> 1,
	'no_found_rows' 		=> 1,
	'posts_per_page' 		=> $posts_per_page,
	'post__in' 				=> $related,
	'post__not_in'			=> array( $product->get_id() )
) );
$show = 3;
$products = new WP_Query( $args );

if ( $products->have_posts() ) : ?>

	<div class="widget related products">

		<h2 class="widget-title"><?php echo esc_html(tolips_get_option('related_heading_text', 'Related Products' )) ?></h2>

		<div class="products carousel-view count-row-1">
			<div class="init-carousel-owl-theme owl-carousel" data-items="<?php echo esc_attr($show); ?>"data-items_lg="<?php echo esc_attr($show); ?>" data-items_md="2" data-items_sm="2" data-items_xs="1" data-items_xx="1" data-loop="0" data-speed="800" data-auto_play="0" data-auto_play_speed="800" data-auto_play_timeout="6000" data-auto_play_hover="1" data-navigation="1" data-pagination="0" data-mouse_drag="1" data-touch_drag="1">
				<?php while ( $products->have_posts() ) : $products->the_post(); ?>
				
						<?php 
							if( WC_Product_Factory::get_product_type( get_the_ID() ) == 'crowdfunding'){ 
  								get_template_part('wpcftemplate/content', 'style-1' );
                      }else{
                        wc_get_template_part( 'content', 'product' );
                     }
						?>
						
				<?php endwhile; // end of the loop. ?>
			</div>	
		</div>	

	</div>

<?php endif;

wp_reset_postdata();
