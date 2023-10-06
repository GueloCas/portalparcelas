<?php
use uListing\Lib\PricingPlan\Classes\StmPricingPlans;
use uListing\Lib\PricingPlan\Classes\StmUserPlan;

$v = ULISTING_VERSION;
wp_enqueue_script('uListing-main-app', ULISTING_URL . '/assets/js/admin/dist/app.js', [], $v, true);

$data = [
	'uListingPreloader' => \uListing\Classes\StmListingSettings::get_preloader(),
	'uListingProImage'  => \uListing\Classes\StmListingSettings::get_pro_preloader(),
	'currentAjaxUrl'    => admin_url('admin-ajax.php'),
	'uListingAjaxNonce' => \uListing\Classes\StmVerifyNonce::createAjaxNonce(),
	'apiUrl'            => site_url()."/1/api/",
    'user_id'           => isset($_GET['id']) ? (int)sanitize_text_field($_GET['id']) : null
];

wp_add_inline_script('uListing-main-app', "var settings_data = json_parse('". ulisting_convert_content(json_encode($data)) ."');", 'before');
?>

<div class="wrap">
    <?php if ( isset($_GET['id']) ):?>
        <h1 class="wp-heading-inline"><?php esc_html_e("User Plan edit", "ulisting") ?></h1>
    <?php else: ?>
        <h1 class="wp-heading-inline"><?php esc_html_e("User Plan add", "ulisting") ?></h1>
    <?php endif;?>
    <div id="stm_pricing_plans_edit">
        <div id="uListing-main">
            <user-plan v-if="getApiUrl"></user-plan>
         </div>
    </div>
</div>
