<?php
/**
 * Loop
 *
 * Template can be modified by copying it to yourtheme/ulisting/loop/loop.php.
 *
 * @see     #
 * @package uListing/Templates
 * @version 1.0.1
 */

use uListing\Classes\StmListingTemplate;

$model->generation_attribute_elements("ulisting_listing_type_item_card_element_data_".$view_type, $listingType);

StmListingTemplate::load_template('loop/'.$view_type.'', [
	'model' => $model,
	'item_class' => $item_class,
	'listingType' => $listingType,
	'listing_item_card_layout' => $listing_item_card_layout
], true );
?>




