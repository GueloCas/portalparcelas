<?php
global $wp_router;

$wp_router->post( array(
		'uri'  => '/payment/stripe/web-hook',
		'uses' => array(\uListing\Lib\Stripe\Classes\WebHook::class, 'web_hook')
	)
);

$wp_router->post( array(
		'uri'  => '/api/payment/stripe/card/add',
		'uses' => array(\uListing\Lib\Stripe\Classes\Stripe::class, 'api_card_add')
	)
);

$wp_router->post( array(
		'uri'  => '/api/payment/stripe/card/make-default',
		'uses' => array(\uListing\Lib\Stripe\Classes\Stripe::class, 'api_card_make_default')
	)
);

$wp_router->post( array(
		'uri'  => '/api/payment/stripe/card/delete',
		'uses' => array(\uListing\Lib\Stripe\Classes\Stripe::class, 'api_card_delete')
	)
);




