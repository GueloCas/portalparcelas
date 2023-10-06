<?php
namespace uListing\Lib\PayPal\Classes;

use PayPal\Api\Plan;
use uListing\Classes\StmListingSettings;
use uListing\Classes\StmListingTemplate;
use uListing\Classes\StmPaymentMethod;
use uListing\Classes\StmUser;
use uListing\Classes\Vendor\Validation;
use uListing\Lib\PayPal\Classes\StmPayPalPlan;
use uListing\Lib\PricingPlan\Classes\StmPayment;
use uListing\Lib\PricingPlan\Classes\StmPricingPlans;
use uListing\Lib\PricingPlan\Classes\StmUserPlan;
use uListing\Lib\PricingPlan\Classes\StmUserPlanMeta;
use uListing\Classes\StmPaymentGateway;
use uListing\Lib\PayPal\Classes\StmPayPalAgreement;

class PayPal extends StmPaymentGateway{

	const ID = "paypal";
	const MODE_SANDBOX = 'sandbox';
	const MODE_LIVE    = 'live';

	public $apiContext;
	public $client_id;
	public $client_secret;
	public $webhook_id;
	public $mode;

	public function __construct() {
		$this->id                 = self::ID;
		$this->image              = ULISTING_PATH_LIB_PAYPAL_URL."/assets/images/paypal.png";
		$this->title              = __('PayPal', "ulisting");
		$this->description        = __('PayPal payment method allows you to accept payments for Subscription Pricing Plans', "ulisting");
		$this->icon               = ULISTING_PATH_LIB_PAYPAL_URL."/assets/images/paypal_logo.png";
		$this->method_title       = __('PayPal', "ulisting");
		$this->method_description = __('PayPal payment method allows you to accept payments for Subscription Pricing Plans', "ulisting");
		$this->supports           = apply_filters('ulisting_paypal_supports', array(StmPaymentMethod::SUPPORT_SUBSCRIPTION));
		$this->enabled            = apply_filters('ulisting_paypal_enabled', $this->get_option('enabled', 'no'));
		$this->mode               = apply_filters('ulisting_paypal_sandbox', $this->get_option('mode'));
		$this->apiContext         = $this->get_api_context();
	}

	/**
	 * @return \PayPal\Rest\ApiContext
	 */
	public function get_api_context() {
		$data = $this->settings_data;
		if( !isset($data['client_id']) OR !isset($data['client_secret']))
			return false;
		$apiContext = new \PayPal\Rest\ApiContext(
			new \PayPal\Auth\OAuthTokenCredential(
				$data['client_id'],
				$data['client_secret']
			)
		);
		$apiContext->setConfig( array(
			'mode' => $data['mode']
		));
		return $apiContext;
	}

	/**
	 * @return \PayPal\Rest\ApiContext
	 */
	public static function getApiContext() {
		$paypal = new PayPal();
		return $paypal->get_api_context();
	}

	/**
	 * Paypal install payment method
	 */
	public function install(){

	}

	/**
	 * Paypal uninstall payment method
	 */
	public function uninstall(){

	}

	/**
	 * PayPal init
	 */
	public static function init() {
		$paypal = new PayPal();
		add_action("ulisting_settings_save", [self::class, "save_settings"]);
		$uListing_subscription_active = ulisting_subscription_active();

		if ($uListing_subscription_active)
			add_filter("ulisting_get_payment_methods", [self::class, "add_payment_method"]);

		if ($paypal->enabled == "yes" && $uListing_subscription_active) {
			add_action("ulisting_pricing_plan_subscription_update", [self::class, "update_plan_or_created"]);
			add_action("ulisting_pricing_plan_subscription_delete", [self::class, "delete_plan"]);
			add_filter("ulisting_payment_method_list", [self::class, "add_payment_method_list"]);
			add_filter("ulisting_payment_subscription", [self::class, "payment_subscription"]);
			add_filter("ulisting_subscription_canceld", [self::class, "subscription_canceld"]);
		}
	}

	/**
	 * @param $payment_methods
	 *
	 * @return mixed
	 */
	public static function add_payment_method_list($payment_methods) {
		$payment_methods[self::ID] = esc_html__("Paypal", "ulisting");
		return $payment_methods;
	}

	/**
	 * @param $payment_methods
	 *
	 * @return mixed
	 */
	public static function add_payment_method($payment_methods) {
		$payment_methods[self::ID] = new \uListing\Lib\PayPal\Classes\PayPal();
		return $payment_methods;
	}

	/**
	 * @param $data
     * @return array
	 */
	public static function payment_subscription($data) {
		if(isset($data['payment_method']) AND $data['payment_method'] == self::ID){
			$result     = array(
				'success' => false,
				'errors'  => false,
				'message' => "",
			);
			$user         = new StmUser(get_current_user_id());
			$pricing_plan = $data['pricing_plan'];
			if($paypal_plan_id = self::getPayPalPlanId($pricing_plan->ID)) {
				$result = self::createAgreementForPlan(
					$paypal_plan_id,
					array(
						'name' => $pricing_plan->post_title,
						'description' => $pricing_plan->post_title.' plan subscription',
					)
				);
				if($result['success']) {
					$url = $result['approvalUrl'];
					$parts = parse_url($url);
					$token = explode("token=", $parts['query']) ;
					StmUserPlan::createNew($pricing_plan,
						$user->ID,
						array(
							'token'          => $token[1],
							'first_pay'      => 1,
							'payment_method' => self::ID
							)
					);
				}
				return $result;
			}
		}
		return $data;
	}

	/**
	 * @param $data
	 */
	public static function save_settings($data) {
		if(!empty($data)) {
			$paypal = new PayPal();
			foreach (apply_filters('ulisting_sanitize_array', $data) as $key => $val){
				$paypal->update_option(esc_attr($key), esc_attr($val));
			}
		}
	}

	/**
	 * @return string
	 */
	public function render_settings() {
		return  ulisting_render_template(ULISTING_PATH_LIB_PAYPAL . '/includes/admin/views/settings/index.php', ['data' => $this->settings_data]);
	}

	/**
	 * @return paypal data
	 */
	public static function getData() {
		$paypal = new PayPal();
		return $paypal->settings_data;
	}

	/**
	 * @param null $mode
	 *
	 * @return array|mixed
	 */
	public static function getMode($mode = null) {
		$modes = array(
			self::MODE_SANDBOX => esc_html__('Sandbox', "ulisting"),
			self::MODE_LIVE    => esc_html__('Live', "ulisting"),
		);
		return ($mode) ? $modes[$mode] : $modes;
	}

	/**
	 * @return mixed
	 */
	public static function get_active_mode(){
		$paypal = new PayPal();
		return $paypal->get_option('mode', 'sandbox');
	}

	/**
	 * @param null $currency
	 *
	 * @return array|mixed
	 */
	public static function getCurrencies($currency = null) {
		$currencies = array(
			"AUD" => __("Australian dollar AUD", "ulisting"),
			"BRL" => __("Brazilian real BRL", "ulisting"),
			"CAD" => __("Canadian dollar CAD", "ulisting"),
			"CZK" => __("Czech koruna CZK", "ulisting"),
			"DKK" => __("Danish krone DKK", "ulisting"),
			"EUR" => __("Euro EUR", "ulisting"),
			"HKD" => __("Hong Kong dollar HKD", "ulisting"),
			"HUF" => __("Hungarian forint HUF", "ulisting"),
			"ILS" => __("Israeli new shekel ILS", "ulisting"),
			"JPY" => __("Japanese yen JPY", "ulisting"),
			"MYR" => __("Malaysian ringgit MYR", "ulisting"),
			"MXN" => __("Mexican peso MXN", "ulisting"),
			"TWD" => __("New Taiwan dollar TWD", "ulisting"),
			"NZD" => __("New Zealand dollar NZD", "ulisting"),
			"NOK" => __("Norwegian krone NOK", "ulisting"),
			"PHP" => __("Philippine peso PHP", "ulisting"),
			"PLN" => __("Polish złoty PLN", "ulisting"),
			"GBP" => __("Pound sterling GBP", "ulisting"),
			"RUB" => __("Russian ruble RUB", "ulisting"),
			"SGD" => __("Singapore dollar SGD", "ulisting"),
			"SEK" => __("Swedish krona SEK", "ulisting"),
			"CHF" => __("Swiss franc CHF", "ulisting"),
			"THB" => __("Thai baht THB", "ulisting"),
			"USD" => __("United States dollar USD", "ulisting"),
		);

		if($currency)
			return $currencies[$currency];

		return $currencies;
	}

	/**
	 * @return bool
	 */
	public static function checkCurrency(){
		return ( $currency_data = StmListingSettings::getCurrency() AND isset($currency_data->currency) AND self::getCurrencies($currency_data->currency)) ? true : false;
	}

	/**
	 * @param $plan_id paypal system
	 */
	public static function getPlan($plan_id) {
		$apiContext = self::getApiContext();
		$result = array(
			'success' => true,
			'plan'    => null,
			'message' => "Success",
		);

		try {
			$result['plan'] = Plan::get($plan_id, $apiContext);
		} catch (Exception $ex) {
			$result['success'] = false;
			$result['message'] = $ex->getMessage();
			return $result;
		}catch (PayPalConnectionException $ex) {
			$result['success'] = false;
			$result['message'] = $ex->getMessage();
			return $result;
		}
		return $result;
	}

	/**
	 * @param string $status
	 *
	 * @return \PayPal\Api\PlanList
	 */
	public static function getPlanList( $status = "ACTIVE" ) {
		return StmPayPalPlan::getList($status);
	}

	/**
	 * @param $stm_plan_id stm plan id
	 *
	 * @return array
	 */
	public static function createPlan($stm_plan_id) {
		$result = array(
			'success' => true,
			'data'    => null,
			'message' => "Success",
		);

		if( !($plan = StmPricingPlans::find_one($stm_plan_id)) ) {
			$result['success'] = false;
			$result['message'] = "Error Plan not found !!!";
			return $result;
		}

		$plan_data  = $plan->getData();
		if(!isset($plan_data['status'])) {
			$result['success'] = false;
			$result['message'] = "Stm Plan status invalid";
			return $result;
		}

		$state  = ($plan_data['status'] == StmPricingPlans::STATUS_ACTIVE) ? StmPayPalPlan::STATUS_ACTIVE : StmPayPalPlan::STATUS_INACTIVE;
		$result = StmPayPalPlan::createPlan($plan);

		if(!$result['success'])
			return $result;

		self::setPayPalPlanId($stm_plan_id, $result['data']->getId());
		return self::updatePlan($result['data']->getId(), array( 'state' => $state ));
	}

	/**
	 * @param $plan_id paypal system
	 * @param $state
	 *
	 * @return array
	 */
	public static function updatePlan($plan_id, $params) {
		$result = StmPayPalPlan::updatePlan(
			$plan_id,
			$params
		);
		return $result;
	}

	/**
	 * @param $plan_id paypal system
	 *
	 * @return array result
	 */
	public static function deletePlan($plan_id) {
		return StmPayPalPlan::deletePlan($plan_id);
	}

	/**
	 * @param $plan_id paypal system
	 * @param $params array name, description
	 *
	 * @return array result (success, approvalUrl, agreement, message)
	 */
	public static function createAgreementForPlan($plan_id, $params) {
		return StmPayPalAgreement::createAgreement(
			$plan_id,
			$params
		);
	}

	/**
	 * @param $token
	 *
	 * @return array result (success, agreement, message)
	 */
	public static function executeAgreement($token) {
		return StmPayPalAgreement::executeAgreement($token);
	}

	/**
	 * @param $agreement_id
	 *
	 * @return array
	 */
	public static function cancelAgreement($agreement_id) {
		return StmPayPalAgreement::cancel($agreement_id);
	}

	/**
	 * @param $id Agreement id paypal system
	 *
	 * @return array
	 */
	public static function getAgreement($id){
		return StmPayPalAgreement::getAgreement($id);
	}

	/**
	 * @param $plan_id stm id
	 */
	public static function synchronizationPlan($plan_id) {
		$pay_pal_plan_id = 0;
		$result = array(
			'success' => true,
			'message' => "Success",
		);

		if( !($stm_plan = StmPricingPlans::find_one($plan_id)) ) {
			$result['success'] = false;
			$result['message'] = __('Plan not found !!!');
			return $result;
		}

		// if not exists paypal plan create plan
		if( !($pay_pal_plan_id = self::getPayPalPlanId($stm_plan->ID)) AND $result = self::createPlan($stm_plan->ID) AND !$result['success']) {
			return $result;
		}

		// if exists paypal plan
//		if($pay_pal_plan_id){
//			$plan_data = $stm_plan->getData();
//			$state  = ($plan_data['status'] == StmPricingPlans::STATUS_ACTIVE) ? StmPayPalPlan::STATUS_ACTIVE : StmPayPalPlan::STATUS_INACTIVE;
//			$result = self::updatePlan($pay_pal_plan_id, $state);
//		}

		return $result;
	}

	/**
	 * @param $plan_id stm listing id
	 *
	 * @return paypal system plan id
	 */
	public static function getPayPalPlanId($plan_id) {
		if( ($data = get_post_meta($plan_id, 'stm_paypal_plan_id_'.self::get_active_mode())) AND isset($data[0]))
			return $data[0];
		return null;
	}

	/**
	 * @param $plan_id stm listing id
	 * @param $paypal_plan_id paypal system plan id
	 */
	public static function setPayPalPlanId($plan_id, $paypal_plan_id) {
		update_post_meta($plan_id, 'stm_paypal_plan_id_'.PayPal::get_active_mode(), $paypal_plan_id);
	}

	public static function paypal_synchronization_ajax() {
		//sleep(1);
		$result = array(
			'success' => false,
			'message' => "Success",
		);

		if(isset($_POST['plan_id']) AND $plan = StmPricingPlans::find_one(sanitize_text_field($_POST['plan_id']))) {
            $result = self::synchronizationPlan($plan->ID);
        }

		if(isset($result['success']) AND isset($result['data']))
			$result['paypal_plan_id'] = $result['data']->getId();

		wp_send_json($result);
		die;
	}

	/**
	 * if subscription agreement success execute agreement and redirect user plan detail
	 */
	public static function subscription_agreement_success() {
		$data = array(
			'success' => false
		);
		if(isset($_GET['token'])) {
			$user_subscription = StmUserPlan::query()
								  ->asTable('user_plan')
								    ->select(' user_plan.* ')
								      ->join(" left join ".StmUserPlanMeta::get_table()." as user_plan_meta on user_plan_meta.`user_plan_id` = user_plan.`id` and user_plan_meta.`meta_key` = 'token' ")
	                                    ->where("user_plan.`user_id`", get_current_user_id())
	                                      ->where('user_plan_meta.`meta_value`', sanitize_text_field($_GET['token']))
	                                        ->findOne();

			if($user_subscription) {
				if(!$user_subscription->getMeta('billing_agreement_id')) {
					$result = PayPal::executeAgreement(sanitize_text_field($_GET['token']));
					if($result['success']) {
						$user_subscription->status = StmUserPlan::STATUS_PENDING;
						$user_subscription->save();
						$agreement = $result['agreement'];
						$user_subscription->setMeta('billing_agreement_id', $agreement->getId());
					}
				}
				// redirect user plan detail
				wp_redirect(StmUser::getUrl('my-plans').'?id='.$user_subscription->id);
			}
		}

		StmListingTemplate::load_template(
			'paypal/subscription-agreement/success',
			['data' => $data],
			true
		);

	}

	/**
	 * if subscription agreement canceled
	 */
	public static function subscription_agreement_canceled() {
		StmListingTemplate::load_template(
			'paypal/subscription-agreement/canceled',
			null,
			true
		);
	}

	/**
	 * @param $duration_type
	 *
	 * @return mixed
	 */
	public static function getFrequency($duration_type) {
		$frequencys = array(
			StmPricingPlans::DURATION_TYPE_DAY   => "DAY",
			StmPricingPlans::DURATION_TYPE_MONTH => "MONTH",
			StmPricingPlans::DURATION_TYPE_YEAR  => "YEAR",
		);
		return $frequencys[$duration_type];
	}

	/**
	 * @return paypal form template
	 */
	public function get_payment_form() {
		return StmListingTemplate::load("paypal-form", ["paypal" => $this], "paypal/", ULISTING_PATH_LIB_PAYPAL."/templates/");
	}

	/**
	 * @param $type
	 *
	 * @return string
	 */
	public function get_payment_script($type) {
		switch ($type) {
			case "selectd":
				$script = "
					if(pricing_plan_payment.payment_method == '".$this->id."'){
						
					}
				";
			break;
			case "buy":
				$script = "
					if(pricing_plan_payment.payment_method == '".$this->id."'){
						pricing_plan_payment.sendRequest();
					}
				";
				break;
			case "send_request":
				$script = "
					if(pricing_plan_payment.payment_method == '".$this->id."'){
						return {
							payment_method : pricing_plan_payment.payment_method,
							pricing_plan_id : pricing_plan_payment.pricing_plan_id
						}
					}
				";
				break;
			case "success":
				$script = "
					if(pricing_plan_payment.payment_method == '".$this->id."'){
		                window.location.replace(response.approvalUrl)
						return;
					}
				";
				break;
			default:
				$script = "";
				break;
		}
		return $script;
	}

	/**
	 * @param StmPricingPlans $pricing_plan
	 */
	public static function update_plan_or_created(StmPricingPlans $pricing_plan) {
		$data = $pricing_plan->getData();
		$state  = ($data['status'] == StmPricingPlans::STATUS_ACTIVE) ? StmPayPalPlan::STATUS_ACTIVE : StmPayPalPlan::STATUS_INACTIVE;
		if( $paypal_plan_id = self::getPayPalPlanId($pricing_plan->ID) ) {
			self::updatePlan( $paypal_plan_id, array( 'state' => $state ) );
		} else {
			$result = self::synchronizationPlan( $pricing_plan->ID );
			if($result['success'] AND $plan = $result['data'])
				self::updatePlan( $plan->getId(), array( 'state' => $state ) );
		}
	}

	/**
	 * @param StmPricingPlans $pricing_plan
	 */
	public static function delete_plan(StmPricingPlans $pricing_plan){
		if(($paypal_plan_id = self::getPayPalPlanId($pricing_plan->ID)) ) {
			self::deletePlan($paypal_plan_id);
			delete_post_meta($pricing_plan->ID, 'stm_paypal_plan_id_'.PayPal::get_active_mode());
		}
	}

	/**
	 * @param StmUserPlan $user_plan
	 */
	public static function subscription_canceld($data){
		if(isset($data['user_plan']) AND $data['user_plan'] instanceof StmUserPlan) {
			$user_plan = $data['user_plan'];
			$payment_method = $user_plan->getMeta('payment_method');
			$paypal = new PayPal();
			if($payment_method->meta_value == $paypal->id) {
				$agreement_id = $user_plan->getMeta('billing_agreement_id');
				return self::cancelAgreement($agreement_id->meta_value);
			}
		}
		return $data;
	}

	public static function paypal_modal_info() {
        $data                           = self::getData();
        $payment_modal                  = [];
        $payment_modal['modes']         = self::getMode();
        $payment_modal['mode_selected'] = (isset($data['mode'])) ? $data['mode'] : self::MODE_SANDBOX;

        if ( ! isset($data['client_id']) )
            $data['client_id'] = "";

        if (  !isset($data['client_secret']) )
            $data['client_secret'] = "";

        if ( ! isset($data['webhook_id']) )
            $data['webhook_id'] = "";

        $payment_modal['data'] = $data;
        return [
            'text'  => [
                'client_id'     => StmListingSettings::plugin_text_domain('Client ID'),
                'client_secret' => StmListingSettings::plugin_text_domain('Client Secret'),
                'web_hook_id'   => StmListingSettings::plugin_text_domain('Webhook ID'),
                'web_hook_url'  => StmListingSettings::plugin_text_domain('Webhook Url'),
                'mode'          => StmListingSettings::plugin_text_domain('Mode'),
            ],

            'data'  => [
                'modes'                  => self::getMode(),
                'mode_selected'          => (isset($data['mode'])) ? $data['mode'] : self::MODE_SANDBOX,
                'client_id'              => isset($data['client_id']) ? $data['client_id'] : '',
                'client_secret'          => isset($data['client_secret']) ? $data['client_secret'] : '',
                'web_hook_id'            => isset($data['webhook_id']) ? $data['webhook_id'] : '',
                'access_update_account'  => StmUserPlan::get_active_plans_count(),
                'web_hook_url'           => WebHook::getWebHookUrl(),
            ],
        ];
    }
}