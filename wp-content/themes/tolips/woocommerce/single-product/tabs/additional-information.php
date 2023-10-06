<?php
/**
 * Additional Information tab
 * 
 * @author        WooThemes
 * @package       WooCommerce/Templates
 * @version       3.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	// Exit if accessed directly
	exit;
}

global $product;

$heading = apply_filters( 'woocommerce_product_additional_information_heading', esc_html__( 'Additional Information', 'tolips' ) );
?>

<?php if ( $heading ): ?>
	<h2 class="tab-title hidden"><?php echo esc_html($heading); ?></h2>
<?php endif; ?>

<?php do_action( 'woocommerce_product_additional_information', $product ); ?>
