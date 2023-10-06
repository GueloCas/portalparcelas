<?php
namespace uListing\Lib\PayPal\Classes;

use PayPal\Api\Plan;
use PayPal\Api\Patch;
use PayPal\Api\Currency;
use PayPal\Api\PatchRequest;
use PayPal\Common\PayPalModel;
use PayPal\Api\MerchantPreferences;
use PayPal\Api\PaymentDefinition;
use PayPal\Exception\PayPalConnectionException;
use uListing\Classes\StmListingSettings;
use uListing\Lib\PricingPlan\Classes\StmPricingPlans;

class StmPayPalPlan {

	const STATUS_CREATED  = "CREATED";
	const STATUS_ACTIVE   = "ACTIVE";
	const STATUS_INACTIVE = "INACTIVE";

	public $apiContext;

	public function __construct() {
		$this->apiContext = self::getApiContext();
	}

	public static function getApiContext() {
		return \uListing\Lib\PayPal\Classes\PayPal::getApiContext();
	}

	/**
	 * @param string $status
	 *
	 * @return \PayPal\Api\PlanList
	 */
	public static function getList($status = self::STATUS_ACTIVE) {

		$apiContext = self::getApiContext();
		try {
			$params = array(
				'page_size' => '20',
				'status' => $status,
			);
			$planList = Plan::all($params, $apiContext);
		} catch (Exception $ex) {
			echo apply_filters('stm_no_echo_variable', $ex->getMessage());
			exit(1);
		}catch (PayPalConnectionException $ex) {
			$result['success']    = false;
			$result['message']    = $ex->getMessage();
			$result['error_data'] = $ex->getData();
			return $result;
		}
		return $planList;
	}

	/**
	 * @param StmPricingPlans $stm_plan
	 *
	 * @return array
	 */
	public static function createPlan(StmPricingPlans $stm_plan) {

		$successUrl = get_site_url(null,'/paypal/subscription-agreement/success');
		$cancelUrl = get_site_url(null,'/paypal/subscription-agreement/canceled');

		$result = array(
			'success' => true,
			'data'    => null,
			'message' => "Success",
		);

		$plan_data  = $stm_plan->getData();
		$currency   = StmListingSettings::getCurrency('currency');
		$apiContext = self::getApiContext();

		if(!$stm_plan) {
			$result['success'] = false;
			$result['message'] = "Error stm plan not found";
			return $result;
		}

		if(!PayPal::getCurrencies($currency)) {
			$result['success'] = false;
			$result['message'] = "Error currency";
			return $result;
		}

		if( !isset($plan_data['duration_type']) OR !$plan_data['duration'] OR !$plan_data['price']) {
			$result['success'] = false;
			$result['message'] = "Stm Plan data invalid";
			return $result;
		}

		$plan = new Plan();
		$plan->setName($stm_plan->post_title)
		     ->setDescription($stm_plan->post_title.' Plan')
		     ->setType('INFINITE');

		// Payment Definition
		$paymentDefinition = new PaymentDefinition();
		$paymentDefinition->setName($stm_plan->post_title.' Plan Regular Payments')
		                  ->setType('REGULAR')
		                  ->setFrequency(PayPal::getFrequency($plan_data['duration_type']))
		                  ->setFrequencyInterval($plan_data['duration'])
		                  ->setCycles("0")
		                  ->setAmount(new Currency(array('value' => $plan_data['price'], 'currency' => $currency)));
		$plan->setPaymentDefinitions(array($paymentDefinition));

		// Merchant Preferences
		$merchantPreferences = new MerchantPreferences();
		$merchantPreferences->setReturnUrl($successUrl)
		                    ->setCancelUrl($cancelUrl)
		                    ->setAutoBillAmount("yes")
		                    ->setInitialFailAmountAction("CONTINUE")
		                    ->setMaxFailAttempts("0");
		$plan->setMerchantPreferences($merchantPreferences);

		try {
			$result['data'] = $plan->create($apiContext);;
		} catch (Exception $ex) {
			$result['success'] = false;
			$result['message'] = $ex->getMessage();
			return $result;
		}catch (PayPalConnectionException $ex) {
			$result['success'] = false;
			$result['message'] = $ex->getMessage();
			$result['error_data'] = $ex->getData();
		}

		return $result;
	}

	/**
	 * @param $id_plan
	 * @param $params
	 *
	 * @return array
	 */
	public static function updatePlan($id_plan, $params) {

		$apiContext = self::getApiContext();
		$result = array(
			'success' => true,
			'data'    => null,
			'message' => "Success",
		);

		try {
			$plan = Plan::get($id_plan, $apiContext);
		} catch (Exception $ex) {
			$result['success'] = false;
			$result['message'] = $ex->getMessage();
			return $result;
		}catch (PayPalConnectionException $ex) {
			$result['success'] = false;
			$result['message'] = $ex->getMessage();
			return $result;
		}

		try {
			$patch = new Patch();
			$value = new PayPalModel('{
		       "state":"'.$params['state'].'"
		     }');
			$patch->setOp('replace')
			      ->setPath('/')
			      ->setValue($value);
			$patchRequest = new PatchRequest();
			$patchRequest->addPatch($patch);
			$plan->update($patchRequest, $apiContext);
			$result['data'] = Plan::get($id_plan, $apiContext);

		} catch (Exception $ex) {
			$result['success'] = false;
			$result['message'] = $ex->getMessage();
			return $result;
		}catch (PayPalConnectionException $ex) {
			$result['success']   = false;
			$result['message']   = $ex->getMessage();
			$result['errorData'] = $ex->getData();
			return $result;
		}
		return $result;
	}

	/**
	 * @param $id_plan
	 *
	 * @return array
	 */
	public static function deletePlan($id_plan) {

		$apiContext = self::getApiContext();
		$result = array(
			'success' => true,
			'data'    => null,
			'message' => "Success",
		);

		try {
			$plan = Plan::get($id_plan, $apiContext);
		} catch (Exception $ex) {
			$result['success'] = false;
			$result['message'] = $ex->getMessage();
			return $result;
		}catch (PayPalConnectionException $ex) {
			$result['success'] = false;
			$result['message'] = $ex->getMessage();
			return $result;
		}

		try {
			$result['data'] = $plan->delete($apiContext);
		} catch (Exception $ex) {
			$result['success'] = false;
			$result['message'] = $ex->getMessage();
		}catch (PayPalConnectionException $ex) {
			$result['success'] = false;
			$result['message'] = $ex->getMessage();
			return $result;
		}

		return $result;
	}
}