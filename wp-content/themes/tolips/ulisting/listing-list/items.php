<?php
/**
 * Listing list items
 *
 * Template can be modified by copying it to yourtheme/ulisting/listing-list/items.php.
 *
 * @see     #
 * @package uListing/Templates
 * @version 1.6.7
 */

use uListing\Classes\StmListingTemplate;

$config = [];
$sections = [];

$list_element = $listingType->getLayoutElements($layout_id, 'list');
$default_view = (isset($list_element['params']['default_item_view']) AND !empty($list_element['params']['default_item_view'])) ? $list_element['params']['default_item_view'] : 'grid';

$view_type = (isset($_COOKIE['stm_listing_item_preview_type'])) ? $_COOKIE['stm_listing_item_preview_type'] : $default_view;

$item_class = 'ulisting-item-'.$view_type.' ';
if( ($listing_item_card_layout = get_post_meta($listingType->ID, 'stm_listing_item_card_'.$view_type)) AND isset($listing_item_card_layout[0]) ) {
	$listing_item_card_layout = maybe_unserialize($listing_item_card_layout[0]);
	$config   = $listing_item_card_layout['config'];
	$sections = $listing_item_card_layout['sections'];

	if(isset($config['template']))
		$item_class .= $config['template'];

	if(isset($config['column'])){
		foreach ($config['column'] as $key => $val) {
			if($key == 'extra_large')
				$item_class .= " stm-col-xl-".(12/$val);
			if($key == 'large')
				$item_class .= " stm-col-lg-".(12/$val);
			if($key == 'medium')
				$item_class .= " stm-col-md-".(12/$val);
			if($key == 'small')
				$item_class .= " stm-col-sm-".(12/$val);
			if($key == 'extra_small')
				$item_class .= " stm-col-".(12/$val);
		}
	}
	else
		$item_class .= " stm-col-12";
}


$template = '';
foreach ($models as $model) {
	$model = $model->listingFeaturedStatus($model);
	$template .= StmListingTemplate::load_template('loop/loop', [
		'model' => $model,
		'view_type' => $view_type,
		'listingType' => $listingType,
		'item_class' => $item_class,
		'listing_item_card_layout' => $sections
	], false);
}

echo apply_filters('uListing-sanitize-data', $template);
