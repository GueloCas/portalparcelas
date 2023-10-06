<?php
/**
 * Builder element short code
 *
 * Template can be modified by copying it to yourtheme/ulisting/builder/element/short-code.php.
 *
 * @see     #
 * @package uListing/Templates
 * @version 1.0
 */
?>

<div <?php echo \uListing\Classes\Builder\UListingBuilder::generation_html_attribute($element) ?> >
	<?php echo do_shortcode(str_replace('u0022', '"', $element['params']['short_code'] ));?>
</div>

