<?php
/**
 * Listing inventory title
 *
 * Template can be modified by copying it to yourtheme/ulisting/listing-list/title.php.
 *
 * @see     #
 * @package uListing/Templates
 * @version 1.0
 */
use uListing\Classes\StmListingType;


$title = (isset($args['listingType'])) ? esc_html__($args['listingType']->post_title, 'ulisting') : '';
$title_panel = '<div '.\uListing\Classes\Builder\UListingBuilder::generation_html_attribute($element).' > [title_panel_inner] </div>';

if(isset($element['params']['template']))
	echo \uListing\Classes\StmInventoryLayout::render_title($element['params']['template'] ,$title_panel, $title);
?>




