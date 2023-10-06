<?php
use uListing\Classes\StmListingType;
wp_enqueue_style('uListing-listing-type', ULISTING_URL . '/assets/css/admin/listing-type.css', []);
$active_tab = 'attribute';

if ( isset($_COOKIE['ulisting_listing_type_active_tab']) )
	$active_tab = $_COOKIE['ulisting_listing_type_active_tab'];

$pages = StmListingType::get_tab_pages();

$data = [
    'activeTab'  => $active_tab,
    'tabs'       => $pages,
    'type_id'    => get_the_ID(),
    'is_create'  => !isset($_GET['post'])
];
uListing_load_admin_scripts($data, ['stm-listing-vuedraggable']);
?>

<div id="uListing-main" class="listing-type">
    <listing-type-settings v-if="getApiUrl"></listing-type-settings>
</div>