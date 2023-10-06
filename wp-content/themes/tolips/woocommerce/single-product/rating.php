<?php
/**
 * Single Product Rating
 *
 * @author      WooThemes
 * @package     WooCommerce/Templates
 * @version     3.6.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

global $product;

if ( get_option( 'woocommerce_enable_review_rating' ) === 'no' ) {
	return;
}

$rating_count = $product->get_rating_count();
$review_count = $product->get_review_count();
$average      = $product->get_average_rating();
$protocol = is_ssl() ? 'https' : 'http';
?>

	<div class="woocommerce-product-rating" itemprop="aggregateRating" itemscope itemtype="<?php echo esc_attr($protocol) ?>://schema.org/AggregateRating">
		<div class="star-rating" title="<?php printf( esc_attr__( 'Rated %s out of 5', 'tolips' ), $average ); ?>">
			<span style="width:<?php echo ( ( $average / 5 ) * 100 ); ?>%">
				<strong itemprop="ratingValue" class="rating"><?php echo esc_html( $average ); ?></strong> <?php printf( esc_html__( 'out of %s5%s', 'tolips' ), '<span itemprop="bestRating">', '</span>' ); ?>
				<?php printf( _n( 'based on %s customer rating', 'based on %s customer ratings', $rating_count, 'tolips' ), '<span itemprop="ratingCount" class="rating">' . $rating_count . '</span>' ); ?>
			</span>
		</div>
		<?php if ( comments_open() ) : ?><a href="#reviews" class="woocommerce-review-link" rel="nofollow">(<?php printf( _n( '%s customer review', '%s customer reviews', $review_count, 'tolips' ), '<span itemprop="reviewCount" class="count">' . $review_count . '</span>' ); ?>)</a><?php endif ?>
	</div>