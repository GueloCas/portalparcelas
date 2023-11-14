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
	$reset_url .= "?layout=".$_GET['layout'];

$reset_filter = '<i class="las la-sync"></i>' . esc_html__('Resetear filtro', 'tolips');
$reset_filter_panel = '<a  href="'.$reset_url.'"  '.\uListing\Classes\Builder\UListingBuilder::generation_html_attribute($element).' > [reset_filter_panel_inner] </a>';
if(isset($element['params']['template']))
   echo '<div class="listing-reset-filter">';
	echo \uListing\Classes\StmInventoryLayout::render_reset_filter($element['params']['template'] ,$reset_filter_panel, $reset_filter);
   echo '</div>';
?>




