<?php
/**
 * Listing
 *
 * Template can be modified by copying it to yourtheme/ulisting/listing/listing.php.
 *
 * @see     #
 * @package uListing/Templates
 * @version 2.0.0
*/
use uListing\Classes\StmListing;
use uListing\Classes\StmListingTemplate;

$model                          = $args['model']->getType();
$region                         = $args['model']->getRegion();
$category                       = $args['model']->getCategory();
$element['params']['data-id']   = $args['model']->ID;

$models = \uListing\Classes\StmListingType::get_similar_listings(
	 [
		"type_id"       => $model->ID,
		"listing_id"    => $args['model']->ID,
		"region"        => isset($region[0]->term_id) ? $region[0]->term_id : null,
		"category"      => isset($category[0]->term_id) ? $category[0]->term_id : null,
		'attributes'    => [],
		'limits'        => isset($element['params']['listings_count']) ? $element['params']['listings_count'] : 3,
	 ]
);
?>

<div class="ulisting-similar-listings  <?php echo esc_attr(\uListing\Classes\Builder\UListingBuilder::generation_html_attribute($element));?>">
	<h3 class="ulisting-el-heading"><?php echo esc_html__('Similares','tolips'); ?></h3>
		<?php if( count($models) > 0 ): ?>
			<?php  
				foreach ( $models as $_model ) { 
				  echo apply_filters('uListing-listing-view', $_model);
				} 
			?>
	  <?php else:; ?>
		  <p class="ulisting-no-similar-listing"><?php echo esc_html__("No similar listings found", "tolips")?></p>
	  <?php endif; ?>
</div>

