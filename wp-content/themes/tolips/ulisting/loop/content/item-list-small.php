<?php
/**
 * @var $model uListing\Classes\StmListing
 */

use uListing\Classes\StmListingTemplate;
use uListing\Classes\StmListingAttribute;
use uListing\Classes\StmListingItemCardLayout;

$model = $args['model'];

$style_template = isset($element['params']['style_template']) ? $element['params']['style_template'] : '';
?>

<div class="listing-block-list-small <?php echo esc_attr($style_template); ?>">
	<div class="listing-thumbnail">
		<?php
			$feature_image   = $args['model']->getfeatureImage('thumbnail');
			$thumbnail_image = ($feature_image) ? $feature_image : ulisting_get_placeholder_image_url();
			$thumbnail_html  = '<a href="' . get_permalink($args['model']->ID) . '" class="listing-link">';
			$thumbnail_html  .= '<img src="' . $thumbnail_image . '" alt="'.esc_attr(get_bloginfo('name')).'"></a>';
			printf('%s', StmListingItemCardLayout::render_thumbnail_box("template_1", $thumbnail_html, "", "", $args['model']->ID));
		?>
	</div>

	<div class="listing-content">
		<div class="content-inner">
			<h3 class="listing-title">
				<a href="<?php echo get_permalink($args['model']) ?>">
					<?php echo esc_html($args['model']->post_title); ?>
				</a>
			</h3>
			<?php
				$lt_location = $args['model']->getAttributeValue( StmListingAttribute::TYPE_LOCATION );
				if (isset($lt_location) && !empty($lt_location['address'])) {
					echo '<div class="lt-location">' . esc_html($lt_location['address']) . '</div>';
				}
			?>
			<div class="listing-price">
				<?php 
				  	$price_element = $element;
				  	$price_model   = $model;
				  	tolips_listing_get_price($price_element, $price_model);
				?>
			</div>
		</div>    
	</div>    
</div>
