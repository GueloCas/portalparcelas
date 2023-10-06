<?php
/**
 * Builder attribute file
 *
 * Template can be modified by copying it to yourtheme/ulisting/builder/attribute/file.php.
 *
 * @see     #
 * @package uListing/Templates
 * @version 1.3.9
 */
use uListing\Classes\StmListingAttribute;
?>
<div <?php echo \uListing\Classes\Builder\UListingBuilder::generation_html_attribute($element) ?>>
    <?php echo StmListingAttribute::render_attribute($args['model'], $element);?>
</div>
