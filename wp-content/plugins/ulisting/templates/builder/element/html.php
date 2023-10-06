<?php
/**
 * Builder element html
 *
 * Template can be modified by copying it to yourtheme/ulisting/builder/element/html.php.
 *
 * @see     #
 * @package uListing/Templates
 * @version 1.3.8
 */

?>
<div <?php echo \uListing\Classes\Builder\UListingBuilder::generation_html_attribute($element) ?> >
	<?php echo html_entity_decode(str_replace('u0022', '"', $element['params']['html'] ))?>
</div>

