<?php
namespace uListing\Lib\PayPalStandard\Classes;

use uListing\Admin\Classes\StmEmailTemplateManager;
use uListing\Classes\StmListingSettings;
use uListing\Classes\StmListingTemplate;
use uListing\Classes\StmPaymentGateway;
use uListing\Classes\StmPaymentMethod;
use uListing\Classes\StmUser;
use uListing\Lib\PricingPlan\Classes\StmPayment;
use uListing\Lib\PricingPlan\Classes\StmPricingPlans;
use uListing\Lib\PricingPlan\Classes\StmUserPlan;

class PayPalStandard extends StmPaymentGateway{

	const ID = "paypal_standard";
	const MODE_SANDBOX = 'sandbox';
	const MODE_LIVE    = 'live';

	public $apiContext;
	public $client_id;
	public $client_secret;
	public $webhook_id;
	public $mode;

	public function __construct() {
		$this->id                 = self::ID;
		$this->image              = ULISTING_PATH_LIB_PAYPAL_STANDARD_URL."/assets/images/paypal.png";;
		$this->title              = __('PayPal standard', "ulisting");
		$this->description        = __('PayPal Standard payment method allows you to accept payments for Single (One Time) Pricing Packages.', "ulisting");
		$this->icon               = ULISTING_PATH_LIB_PAYPAL_STANDARD_URL."/assets/images/paypal_logo.png";
		$this->method_title       = __('PayPal standard', "ulisting");
		$this->method_description = __('PayPal Standard payment method allows you to accept payments for Single (One Time) Pricing Packages.', "ulisting");
		$this->supports           = apply_filters('ulisting_paypal_supports', array(StmPaymentMethod::SUPPORT_ONE_TIME_PAYMENT));
		$this->enabled            = apply_filters('ulisting_paypal_enabled', $this->get_option('enabled', 'no'));
		$this->mode               = apply_filters('ulisting_paypal_sandbox', $this->get_option('mode'));
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
		$paypal_standard = new PayPalStandard();
		add_action("ulisting_settings_save", [self::class, "save_settings"]);
		add_filter("ulisting_get_payment_methods", [self::class, "add_payment_method"]);
		add_filter("ulisting_payment_method_list", [self::class, "add_payment_method_list"]);
		add_action("ulisting_paypal_standard_ipn_process_payment", [self::class, "process_payment"]);
		if($paypal_standard->enabled != "yes")
			return;
		add_filter("ulisting_pricing_plan_payment_one_time", [self::class, "payment_one_time"]);
	}

	/**
	 * @param $data
	 */
	public static function process_payment( $data ) {
		$data['custom'] = str_replace("\\", "", $data['custom']);
		$custom_data = json_decode($data['custom'], true);
		if ( isset($custom_data['payment_id']) AND $payment = StmPayment::find_one($custom_data['payment_id']) ) {
			if ( $payment->status == "Completed" OR !self::validate_transaction($data, $payment) )
				return;
			$payment->transaction = $data['txn_id'];
			$payment->status = $data['payment_status'];
			$payment->save();
            $info = $payment->getMeta('user_info');
            if ( !empty( $info ) AND isset( $info->meta_value )) {
                $details = json_decode($info);
                $args = [
                    'payment_method' => 'PayPal Standard',
                    'payment'        => $payment,
                    'user_name'      => isset($details->name) ? $details->name : '',
                    'user_email'     => isset($details->email) ? $details->email : '',
                    'plan_id'        => isset($payment->user_plan_id) ? $payment->user_plan_id : null,
                ];

                StmEmailTemplateManager::uListing_send_email($args, 'payment-received', true);
            }
		}
	}

	/**
	 * @param $data
	 * @param $payment
	 *
	 * @return bool
	 */
	public static function validate_transaction($data, $payment){

		$paypal_standard = new PayPalStandard();
		$valid = true;
		/*
		 * Price Matching
		 */
		if($payment->amount != $data['payment_gross']){
			$valid = false;
		}
		/*
		 * Zero price check
		 */
		elseif($data['payment_gross'] == 0){
			$valid = false;
		}

		/*
		 * Verification of the payee
		 */
		elseif($data['receiver_email'] != $paypal_standard->getReceiverEmail()){
			$valid = false;
		}
		/*
		 * Currency check
		 */
		elseif($data['mc_currency'] != $paypal_standard->get_active_currency_code()){
			$valid = false;
		}

		return $valid;
	}


	/**
	 * @param $data
	 *
	 * @return mixed
	 */
	public static function payment_one_time($data) {
		if(isset($data['payment_method']) AND $data['payment_method'] == self::ID){
			$result     = array(
				'success' => false,
				'errors'  => false,
				'message' => "",
				'form'    => "",
			);

			$limit = 0;
			$user = new StmUser(get_current_user_id());
			$paypal_standard = new PayPalStandard();
			$pricing_plan = $data['pricing_plan'];
			$plan_data = $pricing_plan->getData();

			if($plan_data['type'] == StmPricingPlans::PRICING_PLANS_TYPE_LIMIT_COUNT)
				$limit = $plan_data['listing_limit'];

			if($plan_data['type'] == StmPricingPlans::PRICING_PLANS_TYPE_FEATURE)
				$limit = $plan_data['feature_limit'];

			if($plan_data['payment_type'] == StmPricingPlans::PRICING_PLANS_PAYMENT_TYPE_ONE_TIME) {

				$user_plan = StmUserPlan::createNew($pricing_plan,
					$user->ID,
					array(
						'payment_method' => $paypal_standard->id,
						'limit' => $limit,
					)
				);

				$user_plan->status = StmUserPlan::STATUS_PENDING;
				$user_plan->save();

				$result['user_plan_id'] = $user_plan->id;

				$payment = new StmPayment();
				$payment->user_plan_id   = $user_plan->id;
				$payment->payment_method = $paypal_standard->id;
				$payment->status         = "pending";
				$payment->amount         = $plan_data['price'];
				$payment->transaction    = "empty";
				$payment->save();

				$payment_data = [
					"item_name" => $pricing_plan->post_title,
					"quantity" => 1,
					"amount" => $plan_data['price'],
					"return_url" => $user::getUrl('my-plans')."?id=".$user_plan->id."&status=payment_pending",
					"custom_data" => ['user_id' => $user->id, "user_plan_id" => $user_plan->id, "payment_id" => $payment->id],
				];

                $payment->setMeta('user_info', [
                        'name' => isset( $data['name'] ) ? $data['name'] : '',
                        'email' => isset( $data['email'] ) ? $data['email'] : ''
                    ]
                );

				$result['success'] = true;
				$result['form'] = StmListingTemplate::load("form", ["paypal_standard" => $paypal_standard, "data" => $payment_data], "paypal-standard/", ULISTING_PATH_LIB_PAYPAL_STANDARD."/templates/");
				return $result;
			}
		}
		return $data;
	}

	public function getPayNowButtonUrl(){
		return ($this->settings_data['mode'] == self::MODE_LIVE) ? "https://www.paypal.com/cgi-bin/webscr" : "https://www.sandbox.paypal.com/cgi-bin/webscr";
	}

	public function getReceiverEmail(){
		return (isset($this->settings_data['email'])) ? $this->settings_data['email'] : null;
	}

	/**
	 * @param $data
	 */
	public static function save_settings($data) {
		if( !empty($data) ) {
			$paypal_standard = new PayPalStandard();
			foreach (apply_filters('ulisting_sanitize_array', $data) as $key => $val){
				$paypal_standard->update_option(esc_attr($key), esc_attr($val));
			}
		}
	}

	/**
	 * @param $payment_methods
	 *
	 * @return mixed
	 */
	public static function add_payment_method_list($payment_methods) {
		$payment_methods[self::ID] = esc_html__("PayPal Standard", "ulisting");
		return $payment_methods;
	}

	/**
	 * @param $payment_methods
	 *
	 * @return mixed
	 */
	public static function add_payment_method($payment_methods) {
		$payment_methods[self::ID] = new PayPalStandard();
		return $payment_methods;
	}


	/**
	 * @return string
	 */
	public function render_settings() {
		return  ulisting_render_template(ULISTING_PATH_LIB_PAYPAL_STANDARD . '/includes/admin/views/settings/index.php', ['data' => $this->settings_data]);
	}

	/**
	 * @return paypal data
	 */
	public static function getData() {
		$paypal_standard = new PayPalStandard();
		return $paypal_standard->settings_data;
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
		$paypal_standard = new PayPalStandard();
		return $paypal_standard->get_option('mode', 'sandbox');
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

        if($currency && !array_key_exists($currency, $currencies))
            return false;

		if($currency)
			return $currencies[$currency];

		return $currencies;
	}

	public static function get_active_currency_code(){
		$currency = StmListingSettings::getCurrency();
		return (isset($currency->currency)) ? $currency->currency : null;

	}

	/**
	 * @return bool
	 */
	public static function checkCurrency(){
		return ( $currency_data = StmListingSettings::getCurrency() AND isset($currency_data->currency) AND self::getCurrencies($currency_data->currency)) ? true : false;
	}

	/**
	 * @return paypal form template
	 */
	public function get_payment_form() {
		return StmListingTemplate::load("paypal-form", ["paypal_standard" => $this], "paypal-standard/", ULISTING_PATH_LIB_PAYPAL_STANDARD."/templates/");
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
					var form  = document.getElementById('ulisting-paypal-standard-from-panel');
					form.innerHTML = response.form
					document.forms['ulisting-paypal-standard-from'].submit();
					}
				";
				break;
			default:
				$script = "";
				break;
		}
		return $script;
	}

	public static function paypal_modal_info() {
        $data                           = self::getData();
        $payment_modal                  = [];
        $payment_modal['modes']         = self::getMode();
        $payment_modal['mode_selected'] = (isset($data['mode'])) ? $data['mode'] : PayPalStandard::MODE_SANDBOX;

        if ( ! isset($data['email']) )
            $data['email'] = '';

        $payment_modal['data'] = $data;
        return [
            'text'  => [
                'email'         => StmListingSettings::plugin_text_domain('PayPal email'),
                'mode'          => StmListingSettings::plugin_text_domain('Mode'),
                'ipn'           => StmListingSettings::plugin_text_domain('IPN url'),
            ],

            'data'  => [
                'modes'         => self::getMode(),
                'mode_selected' => (isset($data['mode'])) ? $data['mode'] : PayPalStandard::MODE_SANDBOX,
                'email'         => isset($data['email']) ? $data['email'] : '',
                'ipn'           => PayPalStandardIpn::get_ipn_url(),
            ],
        ];
    }
}


