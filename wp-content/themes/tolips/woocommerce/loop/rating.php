<?php
/**
 * Loop Rating
 *
 * @author 		WooThemes
 * @package 	WooCommerce/Templates
 * @version     3.6.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

global $product;

if ( get_option( 'woocommerce_enable_review_rating' ) === 'no' )
	return;
?>

<?php if ( $rating_html = $product->get_rating_html() ) : ?>
	<?php echo wp_kses($rating_html, true); ?>
<?php else: ?>
	<div class="star-rating"></div>
<?php endif; ?>