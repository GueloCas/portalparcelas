<?php
namespace uListing\Lib\Stripe\Classes;

use uListing\Admin\Classes\StmEmailTemplateManager;
use uListing\Classes\StmListingSettings;
use uListing\Classes\StmListingTemplate;
use uListing\Classes\StmPaymentMethod;
use uListing\Classes\StmUser;
use Stripe\Stripe as BaseStripe;
use uListing\Lib\PricingPlan\Classes\StmPayment;
use uListing\Lib\Stripe\Classes\Plan;
use uListing\Lib\Stripe\Classes\Charge;
use uListing\Classes\Vendor\Validation;
use uListing\Lib\Stripe\Classes\Balance;
use uListing\Lib\Stripe\Classes\Customer;
use uListing\Lib\Stripe\Classes\Subscription;
use uListing\Lib\PricingPlan\Classes\StmUserPlan;
use uListing\Lib\PricingPlan\Classes\StmPricingPlans;
use uListing\Classes\StmPaymentGateway;

class Stripe extends StmPaymentGateway{

	const ID = "stripe";
	const MODE_LIVE = 'live';
	const MODE_TEST = 'test';

	const PAYMENT_STATUS_SUCCEEDED = 'succeeded';
	const PAYMENT_STATUS_PENDING   = 'pending';
	const PAYMENT_STATUS_FAILED    = 'failed';

	public function __construct() {
		$this->id                 = self::ID;
		$this->image              = ULISTING_PATH_LIB_STRIPE_URL."/assets/images/stripe.png";
		$this->title              = __('Stripe', "ulisting");
		$this->description        = __('Stripe payment method allows you to accept payments directly for web and mobile.', "ulisting");
		$this->icon               = ULISTING_PATH_LIB_STRIPE_URL."/assets/images/credit-card.jpg";
		$this->method_title       = __('Stripe', "ulisting");
		$this->method_description = __('Stripe payment method allows you to accept payments directly for web and mobile.', "ulisting");
		$this->supports           = apply_filters('ulisting_stripe_supports', array(StmPaymentMethod::SUPPORT_ONE_TIME_PAYMENT, StmPaymentMethod::SUPPORT_SUBSCRIPTION));
		$this->enabled            = apply_filters('ulisting_stripe_enabled', $this->get_option('enabled', 'no'));

		if(isset($this->settings_data['secret_key']))
			BaseStripe::setApiKey($this->settings_data['secret_key']);
	}

	public static function init() {
		$stripe = new Stripe();
		add_filter("ulisting_get_payment_methods", [self::class, "add_payment_method"]);
		if($stripe->enabled == "yes") {
			add_action("ulisting_settings_save", [self::class, "save_settings"]);
			add_filter("ulisting_account_endpoint", [self::class, "add_account_endpoint"]);
			add_filter("ulisting_pricing_plan_payment_one_time", [self::class, "payment_one_time"]);
			add_filter("ulisting_pricing_plan_payment_method_data", [self::class, "payment_method_data"]);

			if(ulisting_subscription_active()) {
				add_action("ulisting_pricing_plan_subscription_update", [self::class, "update_plan_or_created"]);
				add_action("ulisting_pricing_plan_subscription_delete", [self::class, "delete_plan"]);
				add_filter("ulisting_payment_method_list", [self::class, "add_payment_method_list"]);
				add_filter("ulisting_payment_subscription", [self::class, "payment_subscription"]);
				add_filter("ulisting_subscription_canceld", [self::class, "subscription_canceld"]);
			}
		}
	}

	public static function add_account_endpoint($data) {
		$pages = get_option( StmListingSettings::ULISTING_PAGES);
		if(isset($pages['account_endpoint']))
			$account_endpoint_val = $pages['account_endpoint'];

        $data[] = [
			"title" => __( 'My card', "ulisting"),
			"var"   => "my-card",
			"value" => (isset($account_endpoint_val["my-card"])) ? $account_endpoint_val["my-card"] : "my-card",
			"template" => "stripe/my-card",
			"template_path" => ULISTING_PATH_LIB_STRIPE."/templates/",
			"menu" => [
				"account-panel",
			]
		];
		return $data;
	}

	/**
	 * @param $payment_data for plan buy form
	 *
	 * @return mixed
	 */
	public static function payment_method_data($payment_data){
		$stripe_data    = self::getData();
		$customer_cards = self::customer_get_card_list(get_current_user_id());

		$payment_data['stripe'] = array(
			'publishable_key' => isset($stripe_data['publishable_key']) ? $stripe_data['publishable_key'] : '',
			'cards'           => $customer_cards,
			'token'           => 0,
		);
		return $payment_data;
	}
	/**
	 * Stripe install payment method
	 */
	public function install(){

	}

	/**
	 * Stripe uninstall payment method
	 */
	public function uninstall(){

	}

	/**
	 * @return string
	 */
	public function render_settings() {
		return ulisting_render_template(ULISTING_PATH_LIB_STRIPE . '/includes/admin/views/settings/index.php', ['data' => $this->settings_data]);
	}

	/**
	 * @throws \Exception
	 * @throws \uListing\Classes\Vendor\Exception
	 */
	public static function api_card_add(){
		$result = array(
			'success'  => false,
			'message' => '',
		);

		$validator = new Validation();
		$data_for_validate = $validator->sanitize($_POST);
		$validator->validation_rules(array(
			'token'   => 'required',
			'user_id' => 'required'
		));

		$validated_data = $validator->run($data_for_validate);

		if($validated_data === false) {
			$result['errors'] = $validator->get_errors_array();
			wp_send_json($result);
			die;
		}

		// if customer not found create new customer
		$stripe_customer = self::customer_retrieve($validated_data['user_id']);
		if($stripe_customer['success'] AND $stripe_customer['customer']->deleted == 1) {
			$result = self::customer_create($validated_data['user_id'], $validated_data['token']);
			if($result['success'])
				$result['cards'] = self::customer_get_card_list($validated_data['user_id']);
			wp_send_json($result);
			die;
		}

		// if customer exisit add  card
		$result = self::customer_add_card($validated_data['user_id'], $validated_data['token']);
		if($result['success'])
			$result['cards'] = self::customer_get_card_list($validated_data['user_id']);

		wp_send_json($result);
		die;
	}

	/**
	 * @throws \Exception
	 * @throws \uListing\Classes\Vendor\Exception
	 */
	public static function api_card_make_default(){
		$result = array(
			'success'  => false,
			'message' => '',
		);

		$validator = new Validation();
		$data_for_validate = $validator->sanitize($_POST);
		$validator->validation_rules(array(
			'id'      => 'required',
			'user_id' => 'required'
		));

		$validated_data = $validator->run($data_for_validate);

		if($validated_data === false) {
			$result['errors'] = $validator->get_errors_array();
			wp_send_json($result);
			die;
		}

		$result = self::customer_set_default_card($validated_data['user_id'], $validated_data['id']);

		if($result['success']){
			$result['cards'] = self::customer_get_card_list($validated_data['user_id']);
		}

		wp_send_json($result);
		die;
	}

	/**
	 * @throws \Exception
	 * @throws \uListing\Classes\Vendor\Exception
	 */
	public static function api_card_delete(){
		$result = array(
			'success'  => false,
			'message' => '',
		);

		$validator = new Validation();
		$data_for_validate = $validator->sanitize($_POST);
		$validator->validation_rules(array(
			'id'      => 'required',
			'user_id' => 'required'
		));

		$validated_data = $validator->run($data_for_validate);

		if($validated_data === false) {
			$result['errors'] = $validator->get_errors_array();
			wp_send_json($result);
			die;
		}

		$result = self::customer_delete_card($validated_data['user_id'], $validated_data['id']);
		if($result['success']){
			$result['cards'] = self::customer_get_card_list($validated_data['user_id']);
		}

		wp_send_json($result);
		die;
	}

	/**
	 * @param null $status
	 *
	 * @return mixed|null
	 */
	public static function get_payment_status($status = null){
		$payment_status = array(
			self::PAYMENT_STATUS_SUCCEEDED => __("Succeeded", "ulisting"),
			self::PAYMENT_STATUS_PENDING   => __("Pending", "ulisting"),
			self::PAYMENT_STATUS_FAILED    => __("Failed", "ulisting"),
		);
		return ($status) ? $payment_status[$status] : $status;
	}

	/**
	 * @param null $mode
	 *
	 * @return array|mixed
	 */
	public static function get_mode($mode = null){
		$modes = array(
			self::MODE_LIVE => __("Live", "ulisting"),
			self::MODE_TEST => __("Test", "ulisting"),
		);
		return ($mode) ? $modes[$mode] : $modes;
	}

	/**
	 * @return string
	 */
	public static function get_active_mode(){
		$data = self::getData();

		if(isset($data['publishable_key']) AND strpos($data['publishable_key'], 'test') !== false)
			return Stripe::MODE_TEST;

		if(isset($data['publishable_key']) AND strpos($data['publishable_key'], 'live') !== false)
			return Stripe::MODE_LIVE;
	}

    /**
     *  Stripe billing synchronization by ajax
     */
    public static function stripe_synchronization_ajax(){
        $result = array(
            'success' => false,
            'message' => "Error !!!",
        );

        if ( isset($_POST['plan_id']) AND $plan = StmPricingPlans::find_one(intval($_POST['plan_id'])) ) {

            if ( $stripe_plan_id = self::get_stripe_plan_id($plan->ID) )
                $result = array(
                    'success' => true,
                    'stripe_plan_id' => $plan->ID,
                );
            else
                $result = self::plan_create($plan);
        }

        if (isset($result['success']) AND isset($result['plan']))
            $result['stripe_plan_id'] = $result['plan']->id;

        wp_send_json($result);
        die;
    }

    /**
     *  Stripe billing synchronization
     * @param $plan_id
     * @return null|mixed
     */
    public static function stripe_synchronization($plan_id){
        if ( empty($plan_id) )
            return null;

        $result = [
            'success' => false,
        ];

        if ( $plan = StmPricingPlans::find_one(intval($plan_id)) ) {
            if ( $stripe_plan_id = self::get_stripe_plan_id( $plan->ID) )
                $result = array(
                    'success' => true,
                    'stripe_plan_id' => $plan->ID,
                );
            else
                $result = self::plan_create($plan);
        }

        if (isset($result['success']) AND isset($result['plan']))
            $result['stripe_plan_id'] = $result['plan']->id;
        return $result;
    }

	/**
	 * @return bool
	 */
	public static function checkCurrency(){
		return true;
	}

	/**
	 * @return Stripe data (client_id, client_secret)
	 */
	public static function getData() {
		$stripe = new Stripe();
		return $stripe->settings_data;
	}

	/**
	 * @param $duration_type stm pricing plan duration type
	 *
	 * @return plan interval for stripe
	 */
	public static function getPlanInterval($duration_type) {

		$interval = array(
			StmPricingPlans::DURATION_TYPE_DAY   => "day",
			StmPricingPlans::DURATION_TYPE_MONTH => "month",
			StmPricingPlans::DURATION_TYPE_YEAR  => "year"
		);

		return $interval[$duration_type];
	}

	/**
	 * @param int $plan_id  plan id in listing system
	 *
	 * @return null| plan id string
	 */
	public static function get_stripe_plan_id($plan_id) {
		if( ($data = get_post_meta($plan_id, 'stm_stripe_plan_id_'.Stripe::get_active_mode())) AND isset($data[0]))
			return $data[0];
		return null;
	}

	/**
	 * @param $user_id
	 * @param $token obtained with Stripe.js
	 *
	 * @return array
	 */
	public static function customer_create($user_id, $token) {
		$customer = new Customer($user_id);
		return $customer->create($token);
	}

	/**
	 * @param $user_id
	 *
	 * @return array
	 */
	public static function customer_retrieve($user_id) {
		$customer = new Customer($user_id);
		return $customer->get_customer();
	}

	/**
	 * @param $user_id
	 *
	 * @return array
	 */
	public static function customer_delete($user_id) {
		$customer = new Customer($user_id);
		return $customer->delete();
	}

	/**
	 * @param $user_id
	 * @param $token obtained with Stripe.js
	 *
	 * @return array
	 */
	public static function customer_add_card($user_id, $token) {
		$customer = new Customer($user_id);
		return $customer->add_card($token);
	}

	/**
	 * @param $user_id
	 *
	 * @return array
	 */
	public static function customer_get_card($user_id) {
		$customer = new Customer($user_id);
		return $customer->get_card();
	}

	/**
	 * @param $user_id
	 *
	 * @return array
	 */
	public static function customer_get_card_list($user_id) {
		$cards = [];
		$customer_card = self::customer_get_card($user_id);
		if($customer_card['success'] AND !empty($customer_card['cards'])) {
			foreach ($customer_card['cards']->data as $card) {
				$cards[] = array(
					'id'          => $card->id,
					'brand'       => $card->brand,
					'last4'        => $card->last4,
					'exp_month'    => $card->exp_month,
					'exp_year'     => $card->exp_year,
					'country'      => $card->country,
					'funding'      => $card->funding,
					'address_city' => $card->address_city,
					'default'     => ($card->id == $customer_card['default_card']) ? 1 : 0
				);
			}
		}
		return $cards;
	}

	/**
	 * @param $user_id
	 * @param $card_id card id in stripe system
	 *
	 * @return array
	 */
	public static function customer_set_default_card($user_id, $card_id) {
		$customer = new Customer($user_id);
		return $customer->set_default_card($card_id);
	}

	/**
	 * @param $user_id
	 * @param $card_id
	 *
	 * @return array
	 */
	public static function customer_delete_card($user_id, $card_id) {
		$customer = new Customer($user_id);
		return $customer->delete_card($card_id);
	}

	/**
	 * @param $amount
	 * @param $data
	 *
	 * @return array
	 */
	public static function charge($amount, $data) {
		$charge = new Charge();
		return $charge->create($amount, $data);
	}

	/**
	 * @param StmPricingPlans $plan
	 *
	 * @return array
	 */
	public static function plan_create(StmPricingPlans $plan) {
		$stripe_plan = new Plan($plan);
		return $stripe_plan->create();
	}

	/**
	 * @param StmPricingPlans $plan
	 *
	 * @return array
	 */
	public static function plan_update(StmPricingPlans $plan) {
		$stripe_plan = new Plan($plan);
		return $stripe_plan->update();
	}

	/**
	 * @param StmPricingPlans $plan
	 *
	 * @return array
	 */
	public static function plan_delete(StmPricingPlans $plan) {
		$stripe_plan = new Plan($plan);
		return $stripe_plan->delete();
	}

	/**
	 * @param StmPricingPlans $plan
	 * @param StmUser $user
	 *
	 * @return array
	 */
	public static function subscription_create(StmPricingPlans $plan, StmUser $user){
		$subscription = new Subscription();
		return $subscription->create($plan, $user);
	}

	/**
	 * @param StmUserPlan $user_plan
	 * @param $params
	 *
	 * @return array
	 */
	public static function subscription_update(StmUserPlan $user_plan, $params){
		$subscription_id = "";//$user_plan
		$subscription = new Subscription();
		return $subscription->update($subscription_id, $params);
	}

	/**
	 * @param StmUserPlan $user_plan
	 *
	 * @return array
	 */
	public static function subscription_cancel(StmUserPlan $user_plan){
		$subscription_id = null;
		if( ($subscription_id =  $user_plan->getMeta('stripe_subscription_id')) AND  isset($subscription_id->meta_value))
			$subscription_id = $subscription_id->meta_value;
		$subscription = new Subscription();
		return $subscription->cancel($subscription_id);
	}

	/**
	 * @return balance object | array
	 */
	public static function get_balance(){
		$balance = new Balance();
		return $balance->get_balance();
	}

	/**
	 * @param $invoice_id
	 *
	 * @return array
	 */
	public static function invoice_retrieve($invoice_id){
		$invoice = new Invoice();
		return $invoice->get_invice($invoice_id);
	}

	/**
	 * @param $payment_methods
	 *
	 * @return mixed
	 */
	public static function add_payment_method_list($payment_methods) {
		$payment_methods['stripe'] = esc_html__("Stripe", "ulisting");
		return $payment_methods;
	}

	/**
	 * @param $payment_methods
	 *
	 * @return mixed
	 */
	public static function add_payment_method($payment_methods) {
		$payment_methods['stripe'] = new Stripe();
		return $payment_methods;
	}

	/**
	 * @param $data
	 */
	public static function save_settings($data) {
		if( !empty($data) ) {
			$stripe = new Stripe();
            foreach ($data as $key => $val){
				$stripe->update_option(esc_attr($key), esc_attr($val));
			}
		}
	}

	/**
	 * @param StmPricingPlans $pricing_plan
	 */
	public static function update_plan_or_created(StmPricingPlans $pricing_plan) {
		$data = $pricing_plan->getData();
		if( $stripe_plan_id = self::get_stripe_plan_id($pricing_plan->ID))
			self::plan_update($pricing_plan);
		else
			self::plan_create($pricing_plan);
	}

	/**
	 * @param StmPricingPlans $pricing_plan
	 */
	public static function delete_plan(StmPricingPlans $pricing_plan){
		if( $stripe_plan_id = self::get_stripe_plan_id($pricing_plan->ID) ) {
			self::plan_delete($pricing_plan);
			delete_post_meta($pricing_plan->ID, 'stm_stripe_plan_id_'.self::get_active_mode());
		}
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
				$script = '
					if (pricing_plan_payment.payment_method == "'.$this->id.'"){
					  if (!pricing_plan_payment.payment_data.stripe.cards.length) {
						 	var stripe_card = pricing_plan_payment.$refs.stripe_card;
						 	stripe_card.get_token();
						 	return;
					   }
					   pricing_plan_payment.sendRequest();
					}
				';
				break;
			case "send_request":
				$script = "
					if (pricing_plan_payment.payment_method == '".$this->id."'){
						var data = {
							payment_method : pricing_plan_payment.payment_method,
							pricing_plan_id : pricing_plan_payment.pricing_plan_id,
							token : 0,
						}
						if (pricing_plan_payment.payment_data.stripe.token) 
							data.token = pricing_plan_payment.payment_data.stripe.token;
						return data;
					}
				";
				break;
			case "success":
				$script = "
					if (pricing_plan_payment.payment_method == '".$this->id."') {
						if (response.success){
							window.location.replace(pricing_plan_payment.my_plans_url+'?action=created&id='+response.user_plan_id)
							return;
						}
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
	 * @return mixed Stripe form template
	 */
	public function get_payment_form() {
		return StmListingTemplate::load("stripe/stripe-form", ["stripe" => $this], "", ULISTING_PATH_LIB_STRIPE."/templates/");
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

			if ( $stripe_plan_id = self::get_stripe_plan_id($pricing_plan->ID) ) {
				if (isset($data['token']) AND $data['token']){
					$result = self::customer_create($user->ID, $data['token']);
					if (!$result['success'])
						return $result;
				}

				$result = self::subscription_create($pricing_plan, $user);
				if($result['success']) {
					$user_plan = StmUserPlan::createNew($pricing_plan,
						$user->ID,
						array(
							'stripe_subscription_id'  => $result['subscription']->id,
							'first_pay'               => 1,
							'payment_method'          => self::ID
						)
					);
					$result['user_plan_id'] = $user_plan->id;
				}
				return $result;
			}
		}
		return $data;
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
			);
			$limit = 0;
			$user = new StmUser(get_current_user_id());
			$stripe = new Stripe();
			$pricing_plan = $data['pricing_plan'];
			$plan_data = $pricing_plan->getData();

			if ($plan_data['type'] == StmPricingPlans::PRICING_PLANS_TYPE_LIMIT_COUNT)
				$limit = $plan_data['listing_limit'];

			if ($plan_data['type'] == StmPricingPlans::PRICING_PLANS_TYPE_FEATURE)
				$limit = $plan_data['feature_limit'];

			if ($plan_data['payment_type'] == StmPricingPlans::PRICING_PLANS_PAYMENT_TYPE_ONE_TIME) {
				if(isset($data['token']) AND $data['token']){
					$result = self::customer_create($user->ID, $data['token']);
					if(!$result['success'])
						return $result;
				}

				if($stripe_customer = get_user_meta($user->ID, 'stripe_customer') AND isset($stripe_customer[0]))
					$stripe_customer = $stripe_customer[0];
				else{
					$result['message'] = __("Customer not found :(", "ulisting");
					return $result;
				}

				$result = self::charge(( $plan_data['price'] * 100 ), [
					"description" => "Charge for ".$pricing_plan->post_title,
					"customer_id" => $stripe_customer,
					"metadata" => [
						"user_id" => $user->ID,
						"pricing_plan_id" => $pricing_plan->ID,
					],
				]);

				if($result['success'] AND isset($result['charge'])) {
					$charge = $result['charge'];
					$user_plan = StmUserPlan::createNew($pricing_plan,
						$user->ID,
						array(
							'payment_method' => $stripe->id,
							'limit' => $limit
						)
					);

					if($charge->status == "succeeded") {
						$user_plan->status = StmUserPlan::STATUS_ACTIVE;
						$user_plan->save();
					}

					$result['user_plan_id'] = $user_plan->id;

					$payment = new StmPayment();
					$payment->user_plan_id   = $user_plan->id;
					$payment->payment_method = $stripe->id;
					$payment->status         = $charge->status;
					$payment->amount         = $plan_data['price'];
					$payment->transaction    = $charge->id;
					$payment->save();

                    $payment->setMeta('user_info', [
                            'name' => isset( $data['name'] ) ? $data['name'] : '',
                            'email' => isset( $data['email'] ) ? $data['email'] : ''
                        ]
                    );

                    if ( $charge->status === "succeeded" ) {
                        $info = $payment->getMeta('user_info');
                        $details = json_decode($info->meta_value);
                        $args = [
                            'payment_method' => 'Stripe',
                            'payment'        => $payment,
                            'user_name'      => isset($details->name) ? $details->name : '',
                            'user_email'     => isset($details->email) ? $details->email : '',
                            'plan_id'        => isset($payment->user_plan_id) ? $payment->user_plan_id : null,
                        ];
                        StmEmailTemplateManager::uListing_send_email($args, 'payment-received', true);
                    }
				}
				return $result;
			}
		}
		return $data;
	}

	/**
	 * @param StmUserPlan $user_plan
	 */
	public static function subscription_canceld($data){
		if(isset($data['user_plan']) AND $data['user_plan'] instanceof StmUserPlan) {
			$user_plan = $data['user_plan'];
			$stripe = new Stripe();
			$payment_method = $user_plan->getMeta('payment_method');
			if($payment_method->meta_value == $stripe->id)
				return self::subscription_cancel($user_plan);
		}
		return $data;
	}

	public static function stripe_modal_info() {
        $data          = self::getData();
        $pricing_plans = StmPricingPlans::query()
            ->where('post_type','stm_pricing_plans')
            ->where('post_status','publish')
            ->find();

        $plans = [];
        foreach ($pricing_plans as $plan) {
            $plan_data          = $plan->getData();
            $plan->plan_data    = $plan;
            $plan->post_content = '';
            if ($plan_data['payment_type'] == StmPricingPlans::PRICING_PLANS_PAYMENT_TYPE_SUBSCRIPTION AND $plan_data['status'] == StmPricingPlans::STATUS_ACTIVE) {
                $plans[] = array(
                    'id'             => $plan->ID,
                    'name'           => $plan->post_title,
                    'stripe_plan_id' => Stripe::get_stripe_plan_id($plan->ID),
                );
            }
        }

        $is_http          = '';
        $is_empty         = '';
        $currency_support = '';

        if (!ulisting_is_https())
            $is_http = StmListingSettings::plugin_text_domain('Subscriptio Stripe payment gateway requires full SSL support and enforcement during Checkout. Only test mode will work until this is solved.');

        if ( !Stripe::checkCurrency() )
            $currency_support = StmListingSettings::plugin_text_domain('Payment method will be available only after filling all fields');

        if ( empty($data['publishable_key']) || empty($data['secret_key']) )
            $is_empty = StmListingSettings::plugin_text_domain('Payment method will be available only after filling all fields');


        return [
            'text'  => [
                'web_hook_url'      => StmListingSettings::plugin_text_domain('Web Hook Url'),
                'publishable_key'   => StmListingSettings::plugin_text_domain('Publishable Key'),
                'secret_key'        => StmListingSettings::plugin_text_domain('Secret Key'),
                'whsec'             => StmListingSettings::plugin_text_domain('Signing Secret'),
                'is_http'           => $is_http,
                'currency_support'  => $currency_support,
                'is_empty'          => $is_empty,
            ],

            'data'  => [
                'plans'                 => $plans,
                'access_update_account' => StmUserPlan::get_active_plans_count(),
                'publishable_key'       => isset($data['publishable_key']) ? $data['publishable_key'] : '',
                'secret_key'            => isset($data['secret_key']) ? $data['secret_key'] : '',
                'whsec'                 => isset($data['whsec']) ? $data['whsec'] : '',
                'web_hook_url'          => WebHook::getWebHookUrl(),
                'subscription_active'   => uListing_subscription_active(),
            ]
        ];
    }

}
