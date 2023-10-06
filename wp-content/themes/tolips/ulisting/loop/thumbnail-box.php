<?php
if ( ! defined( 'ABSPATH' ) ) exit;
/**
 * Loop thumbnail box
 *
 * Template can be modified by copying it to yourtheme/ulisting/loop/thumbnail-box.php.
 *
 * @see     #
 * @package uListing/Templates
 * @version 2.0.0
 */

$top = "";
$bottom = "";
$template = '';
$thumbnail_panel = "";

$element['params']['class'] .= " ulisting-thumbnail-box";
$element['params']['data-id'] = $args['model']->ID;
$size = isset($element['params']['image_size']) && strpos($element['params']['image_size'], 'x') !== false ? $element['params']['image_size'] : '500x500';
$feature_image = $args['model']->getfeatureImage(explode('x', $size));

$feature_background_image = ($feature_image ) ? $feature_image : ulisting_get_placeholder_image_url();
$style = " background-image: url('".$feature_background_image."');
	       background-repeat: no-repeat;
	       background-position: center center;
	       background-size: cover;";
$label_texts =  $args['model']->getAttributeOption('label_text');
$show_custom_label = false;
	if (!empty($label_texts)) {
		if (is_array($label_texts))
			foreach($label_texts as $label) {
				$show_custom_label = !empty($label->value);
			}
	}
if(isset($element['elements_top'])) {
	foreach ($element['elements_top'] as  $element_top) {

		if($element_top['type'] == 'basic')
			$template = 'builder/'.$element_top['type'].'/'.$element_top['params']['type'];

		if($element_top['type'] == 'attribute')
			$template = \uListing\Classes\StmListingItemCardLayout::get_element_template($element_top);

		if (isset($element_top['params']['template_path'])){
			$template = $element_top['params']['template_path'];
		}
		
		if ( isset($element_top['columns']) ) {
			foreach ($element_top['columns'] as $key => $column) {
				foreach ($column['elements'] as $el_key => $elem) {
					if ($elem['field_group'] == 'category') {
						if (!empty($label_texts)) {
							if (is_array($label_texts))
								if ($show_custom_label == true) $element_top['columns'][$key]['elements'][$el_key]['params']['type'] = 'custom-label';
						}
					}
				}
			}	
		}
		
		$top.= \uListing\Classes\StmListingTemplate::load(
			$template,
			[
				"args" => $args,
				"element" => $element_top,
			],
			"ulisting/",
			(isset($element_top['params']['default_path'])) ? ABSPATH.$element_top['params']['default_path'] : ""
		);
	}
}

if(isset($element['elements_bottom'])) {
	foreach ($element['elements_bottom'] as  $element_bottom) {

		if($element_bottom['type'] == 'basic')
			$template = 'builder/'.$element_bottom['type'].'/'.$element_bottom['params']['type'];

		if($element_bottom['type'] == 'attribute')
			$template = \uListing\Classes\StmListingItemCardLayout::get_element_template($element_bottom);

		if(isset($element_bottom['params']['template_path'])){
			$template = $element_bottom['params']['template_path'];
		}

		$bottom.= \uListing\Classes\StmListingTemplate::load(
			$template,
			[
				"args" => $args,
				"element" => $element_bottom,
			],
			"ulisting/",
			(isset($element_bottom['params']['default_path'])) ?  ABSPATH.$element_bottom['params']['default_path']  : ""
		);
	}
}

$thumbnail_panel = '<a href="'.get_permalink( $args['model']->ID ).'" class="ulisting-thumbnail-box-link"></a><div style="'.$style.'" '.\uListing\Classes\Builder\UListingBuilder::generation_html_attribute($element).'>[thumbnail_panel_inner]</div>';

if(isset($element['params']['template']))
 	echo \uListing\Classes\StmListingItemCardLayout::render_thumbnail_box($element['params']['template'], $thumbnail_panel ,$top, $bottom, $args['model']->ID);
?>



