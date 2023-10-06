<?php
$layout = isset($_GET['layout']) ? apply_filters('uListing-sanitize-data', $_GET['layout']) : '';
wp_enqueue_style('uListing-listing-type', ULISTING_URL . '/assets/css/admin/listing-type.css', []);
wp_enqueue_script('cloudflare', ULISTING_URL . '/assets/js/autocomplete.min.js', array(), ULISTING_VERSION);
$data = [
    'selected_layout' => $layout,
];

uListing_load_admin_scripts($data);
?>

<div id="uListing-main">
    <inventory-list v-if="getApiUrl"></inventory-list>
</div>