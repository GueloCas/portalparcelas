<?php
if ( ! defined( 'ABSPATH' ) ) exit;
/**
 * Loop custom label
 *
 * Template can be modified by copying it to yourtheme/ulisting/loop/custom-label.php.
 *
 * @see     #
 * @package uListing/Templates *
 * @version 1.5.7
 */

$label_texts =  $args['model']->getAttributeOption('label_text');
$label_colors =  $args['model']->getAttributeOption('label_color');
$label = '';
$top = '';
if ($label_texts) {
$customlabels = [];
    $top .= '<div class="inventory_category inventory_category_style_1  stm-col-xl-0 stm-col-lg-0 stm-col-md-0 stm-col-sm-0 stm-col-0 ulisting_element_label ">';
        foreach ($label_texts as $label_text) {
            $key = array_search($label_text->sort, array_column($label_colors, 'sort'));
            $top .='<span class="ulisting-listing-category ulisting-listing-label"
                          style="background:'.$label_colors[$key]->value.'">'.$label_text->value.'
                            </span>
            ';
        }
    $top .= '</div>';
}
echo  $top ;

