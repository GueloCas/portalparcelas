<?php
use uListing\Classes\StmListingCategory;
use uListing\Classes\StmListingType;

$listing_category  = new StmListingCategory();
$listing_category->loadData($term);
$listing_type      = StmListingType::getDataList();
$listing_type_list = array();

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
    'listing_type'      => $listing_category->getListingTypes(),
    'text_domains'      => [
        'title'        => __('Listing Type(s)', 'ulisting'),
        'description'  => __('Choose on what listing types should this term be available.', 'ulisting'),
        'placeholder'  => __('Select one', 'ulisting'),
    ],
];
uListing_load_admin_scripts($data);
?>

<tr class="ulisting-main form-field" id="uListing-main">
    <template>
        <th scope="row">
            <label><?php echo __('Listing Type(s)', 'ulisting');?></label>
        </th>
        <listing-category-edit v-if="getApiUrl"></listing-category-edit>
    </template>
</tr>
