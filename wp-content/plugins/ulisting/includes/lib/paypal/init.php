<?php
use uListing\Classes\StmAjaxAction;
function stm_listing_paypal_enqueue_scripts_styles() {
	$v='1.0';
	wp_enqueue_script('stm-paypal-settings', ULISTING_PATH_LIB_PAYPAL_URL . '/assets/js/stm-paypal-settings.js', array('vue.js'), $v, true);
}

add_action('admin_enqueue_scripts', 'stm_listing_paypal_enqueue_scripts_styles');
StmAjaxAction::addAction('stm_paypal_synchronization_ajax', [ \uListing\Lib\PayPal\Classes\PayPal::class ,'paypal_synchronization_ajax']);
\uListing\Lib\PayPal\Classes\PayPal::init();


