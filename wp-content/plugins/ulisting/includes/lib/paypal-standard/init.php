<?php
use uListing\Classes\StmAjaxAction;
function stm_listing_paypal_standard_enqueue_scripts_styles() {
	$v='1.0';
	wp_enqueue_script('stm-paypal-standard-settings', ULISTING_PATH_LIB_PAYPAL_STANDARD_URL . '/assets/js/stm-paypal-standard-settings.js', array('vue.js'), $v, true);
}

add_action('admin_enqueue_scripts', 'stm_listing_paypal_standard_enqueue_scripts_styles');
\uListing\Lib\PayPalStandard\Classes\PayPalStandard::init();


