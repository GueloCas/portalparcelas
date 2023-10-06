<?php
wp_enqueue_style('uListing-listing-type', ULISTING_URL . '/assets/css/admin/listing-type.css', []);
$data = [
    'listing_id' => get_the_ID(),
    'is_create'  => !isset($_GET['post'])
];

uListing_load_admin_scripts($data, ['stm-listing-vuedraggable']);
?>

<div id="uListing-main" class="listing-type">
    <listing-list v-if="getApiUrl"></listing-list>
</div>