<?php
/**
 * Loop region
 *
 * Template can be modified by copying it to yourtheme/ulisting/loop/region.php.
 *
 * @see     #
 * @package uListing/Templates
 * @version 1.0
 */

?>
<div <?php echo \uListing\Classes\Builder\UListingBuilder::generation_html_attribute($element) ?> >
	<?php 
		foreach ($args['model']->getRegion() as $region):
		  	$html = ($element['params']['template']) ? \uListing\Classes\StmListingItemCardLayout::render_region($element['params']['template'], $region) : '';
			echo html_entity_decode($html);
	  	endforeach;
	?>
</div>

