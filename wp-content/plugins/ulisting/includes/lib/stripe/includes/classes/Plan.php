<?php
namespace uListing\Lib\Stripe\Classes;

use Stripe\Error\InvalidRequest;
use uListing\Lib\Stripe\Classes\Stripe;
use uListing\Classes\StmListingSettings;
use uListing\Lib\PricingPlan\Classes\StmPricingPlans;

class Plan {

	private $srtipe;
	private $plan;

	public function __construct(StmPricingPlans $plan) {
		$this->srtipe = new Stripe();
		$this->plan = $plan;
	}

	/**
	 * @param $plan_id plan id in stripe system
	 */
	public function save_plan_id($plan_id){
		update_post_meta($this->plan->ID, "stm_stripe_plan_id_".Stripe::get_active_mode(), $plan_id);
	}

	/**
	 * Stripe plan create
	 *
	 * @return array
	 */
	public function create(){
		$plan_data = $this->plan->getData();
		$currency   = StmListingSettings::getCurrency('currency');
		$result = array(
			'success' => true,
			'plan'    => null,
			'message' => "Success",
		);
		try{
			$stripe_plan = \Stripe\Plan::create([
				"amount" => ( $plan_data['price'] * 100 ),
				"interval" => Stripe::getPlanInterval($plan_data['duration_type']),
				"interval_count" => $plan_data['duration'],
				"product" => [
					"name" => $this->plan->post_title
				],
				"currency" => strtolower($currency),
				"id" => $this->plan->ID
			]);
			$this->save_plan_id($stripe_plan->id);
			$result['plan'] = $stripe_plan;
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
