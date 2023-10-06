<?php
/**
 * Listing list map
 *
 * Template can be modified by copying it to yourtheme/ulisting/listing-list/map.php.
 *
 * @see     #
 * @package uListing/Templates
 * @version 2.0.8
 */

$map_type     = \uListing\Classes\StmListingSettings::get_current_map_type();
$access_token = \uListing\Classes\StmListingSettings::get_map_api_key($map_type);
$open_map_by_hover = uListing\Classes\StmListingSettings::getMapHover();

$element['params']['class'] .= " ulisting-listing-map-custom stm-listing-map-custom_".$element['id'];
$map_panel = '<div '.\uListing\Classes\Builder\UListingBuilder::generation_html_attribute($element).'>[map_panel_inner] </div>';
$map = '<div class="ulisting-listing-map-loader"><div class="ulisting-angrytext"><i class="fa fa-map-marker"></i></div> </div>
            <stm-listing-map inline-template 
                :markers="markers" 
                v-on:exists-map="exists_map"
                :polygon="polygon">
       ';

$is_google = $map_type === 'google';
if ( $is_google ){
	$map .= uListing\Classes\StmListingTemplate::load_template('listing-list/maps/google', ['open_map_by_hover' => $open_map_by_hover]);
} else {
	$map .= uListing\Classes\StmListingTemplate::load_template('listing-list/maps/osm', ['access_token' => $access_token, 'type' => $map_type, 'open_map_by_hover' => $open_map_by_hover]);;
}
$map .= '</stm-listing-map>';


if(isset($element['params']['template']))
	echo \uListing\Classes\StmInventoryLayout::render_map($element['params']['template'], $map_panel, $map);
?>










