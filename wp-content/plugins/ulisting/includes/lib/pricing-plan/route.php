<?php
global $wp_router;

$wp_router->post( array(
		'uri'  => ULISTING_BASE_URL.'/pricing-plan/payment',
		'uses' => function(){
			wp_send_json(\uListing\Lib\PricingPlan\Classes\StmPricingPlans::payment_pricing_plan());
			die;
		}
	)
);

$wp_router->post( array(
		'uri'  => '/api/pricing-plan/user-plan/cancel',
		'uses' => function(){
				wp_send_json(\uListing\Lib\PricingPlan\Classes\StmUserPlan::api_cancel());
				die;
			}
	)
);

$wp_router->get( array(
		'uri'  => ULISTING_BASE_URL.'/pricing-plan/form-data',
		'uses' => function(){
			wp_send_json(\uListing\Lib\PricingPlan\Classes\StmPricingPlans::pricing_plan_data());
			die;
		}
	)
);





