<?php
/**
 * Loop photo count
 *
 * Template can be modified by copying it to yourtheme/ulisting/loop/photo-count.php.
 *
 * @see     #
 * @package uListing/Templates
 * @version 2.0.0
 */
?>
<div <?php echo \uListing\Classes\Builder\UListingBuilder::generation_html_attribute($element) ?> >
	<?php echo (isset($element['params']['template'])) ? \uListing\Classes\StmListingItemCardLayout::render_photo_count($element['params']['template'], $args['model']) : null;?>
</div>

