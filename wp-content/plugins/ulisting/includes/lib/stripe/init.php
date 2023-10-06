<?php

function stm_listing_stripe_enqueue_scripts_styles() {
	$v='1.0';
	wp_enqueue_script('stripe','https://js.stripe.com/v3/');
	wp_enqueue_script('stripe-card-component', ULISTING_PATH_LIB_STRIPE_URL . '/assets/js/stripe-card-component.js', array('vue'), $v, true);
	wp_enqueue_script('stripe-my-card', ULISTING_PATH_LIB_STRIPE_URL . '/assets/js/stripe-my-card.js', array('vue'), $v, true);
}
add_action('wp_enqueue_scripts', 'stm_listing_stripe_enqueue_scripts_styles');

function stm_listing_stripe_enqueue_scripts_styles_admin() {
	$v='1.0';
	wp_enqueue_script('stm-stripe-settings', ULISTING_PATH_LIB_STRIPE_URL . '/assets/js/stm-stripe-settings.js', array('vue.js'), $v, true);
}
add_action('admin_enqueue_scripts', 'stm_listing_stripe_enqueue_scripts_styles_admin');

//for delete
use uListing\Classes\StmAjaxAction;
StmAjaxAction::addAction('stm_stripe_synchronization_ajax', [ \uListing\Lib\Stripe\Classes\Stripe::class ,'stripe_synchronization_ajax']);

\uListing\Lib\Stripe\Classes\Stripe::init();


