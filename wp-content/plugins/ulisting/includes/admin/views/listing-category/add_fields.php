<?php
use uListing\Classes\StmListingType;

$listing_type      = StmListingType::getDataList();
$listing_type_list = [];

foreach ($listing_type as $key => $val) {
    $listing_type_list[] = [
		'id'   => $key,
		'text' => $val,
	];
}


/**
 * Listing Category data
 */
$data = [
    'listing_type_list' => $listing_type_list,
    'listing_type'      => $listing_type,
    'text_domains'      => [
        'title'       => __('Listing Type(s)', 'ulisting'),
        'description' => __('Choose on what listing types should this term be available.', 'ulisting'),
        'placeholder' => __('Select one', 'ulisting'),
    ],
];
uListing_load_admin_scripts($data);
?>

<div id="uListing-main">
    <listing-category-add v-if="getApiUrl"></listing-category-add>
</div>
