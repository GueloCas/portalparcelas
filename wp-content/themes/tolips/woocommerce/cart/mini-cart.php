<?php
/**
 * Mini-cart
 *
 * Contains the markup for the mini-cart, used by the cart widget
 *
 * @author 		WooThemes
 * @package 	WooCommerce/Templates
 * @version     5.2.0
 */

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

global $woocommerce;
?>

<?php do_action( 'woocommerce_before_mini_cart' ); ?>

<div class="cart_list product_list_widget <?php echo esc_attr( $args['list_class'] ); ?>">
	<div class="minicart-close"><i class="fas fa-times"></i></div>
	<?php if ( ! WC()->cart->is_empty() ) : ?>

		<?php
			foreach ( WC()->cart->get_cart() as $cart_item_key => $cart_item ) {
				$_product     = apply_filters( 'woocommerce_cart_item_product', $cart_item['data'], $cart_item, $cart_item_key );
				$product_id   = apply_filters( 'woocommerce_cart_item_product_id', $cart_item['product_id'], $cart_item, $cart_item_key );

				if ( $_product && $_product->exists() && $cart_item['quantity'] > 0 && apply_filters( 'woocommerce_widget_cart_item_visible', true, $cart_item, $cart_item_key ) ) {

					$product_name  = apply_filters( 'woocommerce_cart_item_name', $_product->get_title(), $cart_item, $cart_item_key );
					$thumbnail     = apply_filters( 'woocommerce_cart_item_thumbnail', $_product->get_image(), $cart_item, $cart_item_key );
					$product_price = apply_filters( 'woocommerce_cart_item_price', WC()->cart->get_product_price( $_product ), $cart_item, $cart_item_key );

					?>
					<div class="widget-product">
						<div class="product-thumbnail">
							<a href="<?php echo get_permalink( $product_id ); ?>" class="image pull-left">
								<?php echo trim($thumbnail); ?>
							</a>
						</div>
						<div class="product-body">	
							<div class="cart-main-content">
								<h3 class="name">
									<a href="<?php echo get_permalink( $product_id ); ?>">
										<?php echo esc_html($product_name); ?>
									</a>
								</h3>
								<p class="cart-item">
									<?php echo wc_get_formatted_cart_item_data( $cart_item ); ?>
									<?php echo apply_filters( 'woocommerce_widget_cart_item_quantity', '<span class="quantity">' . sprintf( '%s &times; %s', $cart_item['quantity'], $product_price ) . '</span>', $cart_item, $cart_item_key ); ?>
								</p>

								<?php
								    echo apply_filters(
								        'woocommerce_cart_item_remove_link',
								        sprintf(
								            '<a href="%s" class="remove" title="%s">&times;</a>',
								            esc_url( wc_get_cart_remove_url( $cart_item_key ) ),
								            esc_html__( 'Remove this item', 'tolips' )
								        ),
								        $cart_item_key
								    );
								?>
							</div>
						</div>	
					</div>
					<?php
				}
			}
		?>
		

	<?php else : ?>

		<div class="empty"><?php echo esc_html__( 'No products in the cart.', 'tolips' ); ?></div>

	<?php endif; ?>

</div><!-- end product list -->

<?php if ( sizeof( WC()->cart->get_cart() ) > 0 ) : ?>

	<p class="total"><strong><?php echo esc_html__( 'Subtotal', 'tolips' ); ?>:</strong> <?php echo WC()->cart->get_cart_subtotal(); ?></p>

	<?php do_action( 'woocommerce_widget_shopping_cart_before_buttons' ); ?>

	<p class="buttons clearfix">
		<a href="<?php echo wc_get_cart_url(); ?>" class="btn btn-theme pull-left btn-sm wc-forward"><?php echo esc_html__( 'View Cart', 'tolips' ); ?></a>
		<a href="<?php echo wc_get_checkout_url(); ?>" class="btn btn-theme pull-right btn-sm checkout wc-forward"><?php echo esc_html__( 'Checkout', 'tolips' ); ?></a>
	</p>

<?php endif; ?>

<?php do_action( 'woocommerce_after_mini_cart' ); ?>