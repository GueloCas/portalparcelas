<?php
/**
 * Listing inventory reset filter
 *
 * Template can be modified by copying it to yourtheme/ulisting/listing-list/reset-filter.php.
 *
 * @see     #
 * @package uListing/Templates
 * @version 1.0
 */

$reset_url = $args['listingType']->getPageUrl();

if(isset($_GET['layout']))
	$reset_url .= "?layout=".sanitize_text_field($_GET['layout']);

$reset_filter = __('Reset filter', 'ulisting');
$reset_filter_panel = '<a  href="'.esc_url($reset_url).'"  '.\uListing\Classes\Builder\UListingBuilder::generation_html_attribute($element).' > [reset_filter_panel_inner] </a>';
if(isset($element['params']['template']))
	echo \uListing\Classes\StmInventoryLayout::render_reset_filter($element['params']['template'] ,$reset_filter_panel, $reset_filter);
?>




