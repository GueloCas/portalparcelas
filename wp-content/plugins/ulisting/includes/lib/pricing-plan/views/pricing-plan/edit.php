<?php
$v = ULISTING_VERSION;
wp_enqueue_script('uListing-main-app', ULISTING_URL . '/assets/js/admin/dist/app.js', [], $v, true);

$data = [
    'uListingPreloader' => \uListing\Classes\StmListingSettings::get_preloader(),
    'uListingProImage'  => \uListing\Classes\StmListingSettings::get_pro_preloader(),
    'currentAjaxUrl'    => admin_url('admin-ajax.php'),
    'uListingAjaxNonce' => \uListing\Classes\StmVerifyNonce::createAjaxNonce(),
    'apiUrl'            => site_url()."/1/api/",
    'pricing_plan_id'   => get_post()->ID,
    'return_url'        => admin_url('post.php?post='. get_post()->ID.'&action=edit'),
];

wp_add_inline_script('uListing-main-app', "var settings_data = json_parse('". ulisting_convert_content(json_encode($data)) ."');", 'before');
?>

<div id="uListing-main">
    <pricing-plan v-if="getApiUrl"></pricing-plan>
</div>