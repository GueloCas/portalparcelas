<?php
/**
 * Loop grid
 *
 * Template can be modified by copying it to yourtheme/ulisting/loop/grid.php.
 *
 * @see     #
 * @package uListing/Templates
 * @version 1.0
 */
$model->subtitle_text = isset($subtitle_text) ? $subtitle_text : '';
?>
<div class="<?php echo esc_html($item_class)?>">
   <div class="inventory-item-content">
      <?php
      	echo \uListing\Classes\Builder\UListingBuilder::render($listing_item_card_layout, "ulisting_item_card_".$listingType->ID."_grid", [
      		'model' => $model,
      		'listingType' => $listingType,
            'listing_type' => $listingType
      	]);
      ?>
   </div>   
</div>
