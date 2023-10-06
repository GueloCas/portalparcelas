<?php
/**
 * Listing inventory title
 *
 * Template can be modified by copying it to yourtheme/ulisting/listing-list/matches.php.
 *
 * @see     #
 * @package uListing/Templates
 * @version 1.4.0
 */

$title = isset($element['params']['title']) ? $element['params']['title'] : '';

$element['params']['class'] .= ' ulisting-matches-wrap ';

$matches =  '<div '.\uListing\Classes\Builder\UListingBuilder::generation_html_attribute($element).'><span class="ulisting-matcher-text">'. $title . ': </span><span class="ulisting-matches-count">'. '{{matches}}</span></div>';

echo html_entity_decode($matches);