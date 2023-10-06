<?php

namespace uListing\Lib\Stripe\Classes;

use uListing\Classes\StmUser;
use Stripe\Error\InvalidRequest;
use uListing\Lib\PricingPlan\Classes\StmPricingPlans;

class Subscription {

	private $plan;
	private $user;
	private $srtipe;
	private $customer;

	public function __construct() {
		$this->srtipe   = new Stripe();
	}

	/**
	 * Create subscription in stripe
	 *
	 * @param StmPricingPlans $plan
	 * @param StmUser $user
	 *
	 * @return array
	 */
	public function create(StmPricingPlans $plan, StmUser $user){

		$this->plan     = $plan;
		$this->user     = $user;
		$this->customer = new Customer($user->ID);

		$result = array(
			'success'      => true,
			'subscription' => null,
			'message'      => "Success",
		);

		$customer_id = $this->customer->get_customer_id();
		if(is_array($customer_id))
			return $customer_id;

		try{

		    $subscription = \Stripe\Subscription::create([
				"customer" => $customer_id,
				"items" => [
					[ "plan" => $this->plan->ID ],
				]
			]);
			$result['subscription'] = $subscription;

		}catch (InvalidRequest $e){
			$result['success'] = false;
			$result['message'] = $e->getMessage();
		}

		return $result;
	}

	/**
	 * Update subscription in stripe billing, only cancel_at_period_end argument
	 *
	 * @param $subscription_id subscription id in stripe billing system
	 * @param array $params
	 *
	 * @return array
	 */
	public function update($subscription_id, $params = array()){

		$result = array(
			'success'      => true,
			'subscription' => null,
			'message'      => "Success",
		);

		try{

			$subscription = \Stripe\Subscription::retrieve($subscription_id);
			if (isset($params['cancel_at_period_end']))
				$subscription->cancel_at_period_end = $params['cancel_at_period_end'];
			$subscription->save();
			$result['subscription'] = $subscription;

		}catch (InvalidRequest $e){
			$result['success'] = false;
			$result['message'] = $e->getMessage();
		}

		return $result;

	}

	/**
	 * cancel subscription in stripe billing
	 *
	 * @param $subscription_id subscription id in stripe billing system
	 *
	 * @return array
	 */
	public function cancel($subscription_id){

		$result = array(
			'success'      => true,
			'subscription' => null,
			'message'      => "Success",
		);

		try{

			$subscription = \Stripe\Subscription::retrieve($subscription_id);
			$subscription->cancel();
			$result['subscription'] = $subscription;

		}catch (InvalidRequest $e){
			$result['success'] = false;
			$result['message'] = $e->getMessage();
		}
		return $result;
	}
}


