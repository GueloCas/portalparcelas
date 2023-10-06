<?php
global $wp_router;
$wp_router->post( array(
		'uri'  => \uListing\Lib\PayPalStandard\Classes\PayPalStandardIpn::get_ipn_uri(),
		'uses' => array(\uListing\Lib\PayPalStandard\Classes\PayPalStandardIpn::class, 'ipn')
	)
);