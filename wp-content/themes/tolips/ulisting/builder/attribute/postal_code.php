<?php
/**
 * Builder attribute postal code
 *
 * Template can be modified by copying it to yourtheme/ulisting/builder/attribute/postal_code.php.
 *
 * @see     #
 * @package uListing/Templates
 * @version 1.0
 */
use uListing\Classes\StmListingAttribute;
$location = $args['model']->getAttributeValue(StmListingAttribute::TYPE_LOCATION);

?>
<div <?php echo \uListing\Classes\Builder\UListingBuilder::generation_html_attribute($element) ?>>
	<?php echo (isset($location['postal_code'])) ? $location['postal_code'] : '';?>
</div>
