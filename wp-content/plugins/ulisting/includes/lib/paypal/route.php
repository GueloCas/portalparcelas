<?php
global $wp_router;

$wp_router->post( array(
		'uri'  => '/payment/paypal/web-hook',
		'uses' => array(\uListing\Lib\PayPal\Classes\WebHook::class, 'web_hook')
	)
);

$wp_router->get( array(
		'uri'  => '/paypal/subscription-agreement/success',
		'uses' => array(\uListing\Lib\PayPal\Classes\PayPal::class, 'subscription_agreement_success')
	)
);

$wp_router->get( array(
		'uri'  => '/paypal/subscription-agreement/canceled',
		'uses' => array(\uListing\Lib\PayPal\Classes\PayPal::class, 'subscription_agreement_canceled')
	)
);