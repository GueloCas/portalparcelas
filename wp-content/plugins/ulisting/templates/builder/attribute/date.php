<?php
/**
 * Builder attribute date
 *
 * Template can be modified by copying it to yourtheme/ulisting/builder/attribute/date.php.
 *
 * @see     #
 * @package uListing/Templates
 * @version 1.0
 */
use uListing\Classes\StmListingAttribute;
?>
<div <?php echo \uListing\Classes\Builder\UListingBuilder::generation_html_attribute($element) ?>>
	<?php echo StmListingAttribute::render_attribute($args['model'], $element)?>
</div>