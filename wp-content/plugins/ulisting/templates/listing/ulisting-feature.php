<?php
/**
 * Listing ulisting feature
 *
 * Template can be modified by copying it to yourtheme/ulisting/listing/ulisting-feature.php.
 *
 * @see     #
 * @package uListing/Templates
 * @version 1.3.8
 */
$is_module = isset($is_module) ? '9' : '3';
?>

<ul class="stm-row">
<?php
	foreach ($listings as $listing){
		$item = "";
		$listingType =  $listing->getType();
		if( ($listing_item_card_layout = get_post_meta($listingType->ID, 'stm_listing_item_card_'.$view_type)) AND isset($listing_item_card_layout[0]) ) {
			$listing_item_card_layout = maybe_unserialize($listing_item_card_layout[0]);
			$config   = $listing_item_card_layout['config'];
			$sections = $listing_item_card_layout['sections'];
		}
		$item.= \uListing\Classes\StmListingTemplate::load_template('loop/loop', [
			'model'       => $listing,
			'view_type'   => $view_type,
			'listingType' => $listingType,
			'item_class'  => $item_class,
			'listing_item_card_layout' => $sections
		]);
		$output = "<li class='stm-col-{$is_module}'>".$item."</li>";
		echo apply_filters('uListing-sanitize-data', $output);
	}
?>
</ul>
