<?php
/**
 * Listing list
 *
 * Template can be modified by copying it to yourtheme/ulisting/listing-list/listing-list.php.
 *
 * @see     #
 * @package uListing/Templates
 * @version 1.3.8
 */

use uListing\Classes\StmListingTemplate;
$feature_list = "";
$listing_list = "";
$is_ajax = (isset($is_ajax)) ? $is_ajax : null;

wp_add_inline_script('ulisting-inventory-list', " var ulisting_inventory_list_params =  json_parse('".ulisting_convert_content(json_encode($element))."') ", 'before');

$upload = wp_get_upload_dir();
wp_enqueue_style('ulisting_builder_stytle_ulisting_item_card_'.$args['listingType']->ID.'_grid', $upload['baseurl']."/ulisting/css/ulisting_item_card_".$args['listingType']->ID."_grid.css");
wp_enqueue_style('ulisting_builder_stytle_ulisting_item_card_'.$args['listingType']->ID.'_list', $upload['baseurl']."/ulisting/css/ulisting_item_card_".$args['listingType']->ID."_list.css");

if (sizeof($args['feature_models']))
	$feature_list = StmListingTemplate::load_template('listing-list/items', ['is_ajax' => $is_ajax, 'layout_id' => isset($args['layout_id']) ? $args['layout_id'] : null, 'listingType' => $args['listingType'], 'models' => $args['feature_models']]);

if (sizeof($args['models']))
	$listing_list = StmListingTemplate::load_template('listing-list/items', ['is_ajax' => $is_ajax, 'layout_id' => isset($args['layout_id']) ? $args['layout_id'] : null, 'listingType' => $args['listingType'], 'models' => $args['models']]);

if (!isset($hidden_panel))
	$list_panel = '<div '.\uListing\Classes\Builder\UListingBuilder::generation_html_attribute($element).'> <div data-v-bind_class="{\'ulisting-preloader\':preloader}" id="stm-listing-list-panel"> [list_panel_inner] </div> </div>';
else
	$list_panel = '[list_panel_inner]';

if (isset($element['params']['template']))
	echo \uListing\Classes\StmInventoryLayout::render_list($element['params']['template'], $list_panel, $feature_list, $listing_list, apply_filters('ulisting_filter_no_results', ''));
?>