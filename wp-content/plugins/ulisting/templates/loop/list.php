<?php
/**
 * Loop list
 *
 * Template can be modified by copying it to yourtheme/ulisting/loop/list.php.
 *
 * @see     #
 * @package uListing/Templates
 * @version 1.0
 */

?>
<div class="<?php echo esc_html($item_class)?>">
	<?php
		echo \uListing\Classes\Builder\UListingBuilder::render($listing_item_card_layout, "ulisting_item_card_".$listingType->ID."_list", [
			'model' => $model,
			'listingType' => $listingType,
		]);
	?>
</div>
