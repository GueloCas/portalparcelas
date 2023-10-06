<?php
namespace uListing\Lib\Stripe\Classes;

use Stripe\Error\InvalidRequest;
use uListing\Lib\Stripe\Classes\Stripe;
use uListing\Classes\StmListingSettings;
use uListing\Lib\PricingPlan\Classes\StmPricingPlans;

class Charge {

	private $srtipe;

	public function __construct() {
		$this->srtipe = new Stripe();
	}

	/**
	 * Stripe Charge create
	 *
	 * @param $amount
	 * @param $data
	 *
	 * @return array
	 */
	public function create($amount, $data){
		$result = array(
			'success' => true,
			'charge'    => null,
			'message' => "Success",
		);
		$access = false;
		$currency = StmListingSettings::getCurrency('currency');
		$charge_data = [
			"amount" => $amount,
			"currency" => $currency,
		];

		if(isset($data['description']))
			$charge_data['description'] = $data['description'];

		if(isset($data['metadata']))
			$charge_data['metadata'] = $data['metadata'];

		if(isset($data['customer_id']))
			$charge_data['customer'] = $data['customer_id'];

		if(isset($data['token']))
			$charge_data['token'] = $data['token'];

		if(!isset($charge_data["customer"]) AND !isset($charge_data["token"])) {
			$result['success'] = false;
			$result['message'] = __("Error in payment , not all attributes come ","ulisting");
			return $result;
		}

		try{
			$result['charge'] = \Stripe\Charge::create($charge_data);;
		}catch (InvalidRequest $e){
			$result['success'] = false;
			$result['message'] = $e->getMessage();
		}catch (Authentication $e){
			$result['success'] = false;
			$result['message'] = $e->getMessage();
		}
		return $result;
	}

	/**
	 * Stripe plan update only argument active
	 *
	 * @return array
	 */
	public function update(){
		$stripe_plan = null;
		$plan_data   = $this->plan->getData();

		$result = array(
			'success' => true,
			'plan'    => null,
			'message' => "Success",
		);
		try{
			// update plan active
			$stripe_plan = \Stripe\Plan::retrieve($this->plan->ID);
			$stripe_plan->active = ($plan_data['status'] == StmPricingPlans::STATUS_ACTIVE) ? true : false;
			$stripe_plan->save();
			$result['plan'] = $stripe_plan;
			// update product name
			$product = \Stripe\Product::retrieve($stripe_plan->product);
			$product->name = $this->plan->post_title;
			$product->save();

		}catch (InvalidRequest $e){
			$result['success'] = false;
			$result['message'] = $e->getMessage();
		}catch (Authentication $e){
			$result['success'] = false;
			$result['message'] = $e->getMessage();
		}
		return $result;
	}

	/**
	 * Delete plan in stripe billing
	 *
	 * @return array
	 */
	public function delete(){
		$result = array(
			'success' => true,
			'message' => "Success",
		);
		try{
			// get plan
			$stripe_plan = \Stripe\Plan::retrieve($this->plan->ID);
			// get product for plan
			$product = \Stripe\Product::retrieve($stripe_plan->product);
			$stripe_plan->delete();
			$product->delete();
		}catch (InvalidRequest $e){
			$result['success'] = false;
			$result['message'] = $e->getMessage();
		}catch (Authentication $e){
			$result['success'] = false;
			$result['message'] = $e->getMessage();
		}
		return $result;
	}
}
