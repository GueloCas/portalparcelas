<?php

namespace uListing\Lib\PricingPlan\Classes;

use uListing\Classes\StmUser;
use uListing\Classes\StmVerifyNonce;
use uListing\Classes\Vendor\StmBaseModel;
use uListing\Classes\Vendor\Validation;
use uListing\Classes\StmListingSettings;
use uListing\Classes\Vendor\ArrayHelper;
use uListing\Classes\StmListingTemplate;
use uListing\Lib\PricingPlan\Classes\StmPayment;


class StmPricingPlans extends StmBaseModel{

	const STATUS_ACTIVE                           = 'active';
	const STATUS_INACTIVE                         = 'inactive';

	const PRICING_PLANS_TYPE_LIMIT_COUNT          = 'limit_count';
	const PRICING_PLANS_TYPE_LIMIT_IMAGE_COUNT    = 'listing_image_limit';
	const PRICING_PLANS_TYPE_FEATURE              = 'feature';

	const PRICING_PLANS_PAYMENT_TYPE_SUBSCRIPTION = 'subscription';
	const PRICING_PLANS_PAYMENT_TYPE_ONE_TIME     = 'one_time';

	const DURATION_TYPE_DAY                       = "days";
	const DURATION_TYPE_MONTH                     = "month";
	const DURATION_TYPE_YEAR                      = "year";

	protected $fillable = [
		'ID',
		'post_author',
		'post_date',
		'post_date_gmt',
		'post_content',
		'post_title',
		'post_excerpt',
		'post_status',
		'comment_status',
		'ping_status',
		'post_password',
		'post_name',
		'to_ping',
		'post_modified',
		'post_modified_gmt',
		'post_content_filtered',
		'post_parent',
		'guid',
		'menu_order',
		'post_type',
		'post_mime_type',
		'comment_count'
	];
	public $ID;
	public $post_author;
	public $post_date;
	public $post_date_gmt;
	public $post_content;
	public $post_title;
	public $post_excerpt;
	public $post_status;
	public $comment_status;
	public $ping_status;
	public $post_password;
	public $post_name;
	public $to_ping;
	public $post_modified;
	public $post_modified_gmt;
	public $post_content_filtered;
	public $post_parent;
	public $guid;
	public $menu_order;
	public $post_type;
	public $post_mime_type;
	public $comment_count;
	public $post;

	public static function get_primary_key()
	{
		return 'ID';
	}

	public static function get_table()
	{
		global $wpdb;
		return $wpdb->prefix . 'posts';
	}

	public static function get_searchable_fields()
	{
		return [
			'ID',
			'post_author',
			'post_date',
			'post_date_gmt',
			'post_content',
			'post_title',
			'post_excerpt',
			'post_status',
			'comment_status',
			'ping_status',
			'post_password',
			'post_name',
			'to_ping',
			'post_modified',
			'post_modified_gmt',
			'post_content_filtered',
			'post_parent',
			'guid',
			'menu_order',
			'post_type',
			'post_mime_type',
			'comment_count',
		];
	}

	public static function init(){
		if(is_admin()) {
			add_filter( 'manage_stm_pricing_plans_posts_columns', [self::class, 'stm_pricing_plans_columns_head'] );
			add_action( 'manage_stm_pricing_plans_posts_custom_column', [self::class, 'stm_pricing_plans_columns_content'], 10, 2 );
			add_action( 'before_delete_post', [self::class, 'before_delete_post'] );
			add_action( 'add_meta_boxes', [self::class, 'edit_panel_init'] );
		}else{
			add_action('template_redirect', array(self::class, 'pricing_plan_page_redirect'));
            add_filter("ulisting_pricing_plan_payment_one_time", [self::class, "payment_one_time"]);
		}

        add_shortcode("ulisting-pricing-plan", [self::class, "pricing_plan_module"]);
	}

	/**
	 * @param $defaults
	 *
	 * @return mixed
	 */
	public static function stm_pricing_plans_columns_head($defaults)
	{
		$defaults['payment_type']   = esc_html__("Plan Payment Type", "ulisting");
		$defaults['type']           = esc_html__("Plan Type", "ulisting");
		$defaults['duration']       = esc_html__("Duration", "ulisting");
		$defaults['price']          = esc_html__("Plan Price", "ulisting");
		$defaults['status']         = esc_html__("Status", "ulisting");
		return $defaults;
	}

	/**
	 * @param $column_name
	 * @param $post_ID
	 */
	public static function stm_pricing_plans_columns_content($column_name, $post_ID)
	{
		switch ( $column_name ) {
			case 'duration':
				echo get_post_meta( $post_ID , $column_name, true ) . ' ' . get_post_meta( $post_ID , 'duration_type', true );
				break;
			default:
				echo ucfirst( str_replace( '_', ' ', get_post_meta( $post_ID , $column_name, true )) );
		}
	}

    /**
     * @param $params
     * @return bool|string
     */
	public static function pricing_plan_module($params) {
        $subscription_plans = [];
        $plans = self::get_pricing_plans();
        if ( ulisting_subscription_active() ) {
            $subscription_plans = self::get_subscription_plans();
        }

        return '<div id="stm-pricing-plan">' . StmListingTemplate::load_template('pricing-plan/list', [
                'plans' => $plans,
                'subscription_plans' => $subscription_plans
            ]) . '</div>';
    }

	public static function pricing_plan_page_redirect() {
		if($post = get_post() AND $post->ID == StmListingSettings::getPages(StmListingSettings::PAGE_PRICING_PLAN)) {
			if(!get_current_user_id()) {
				wp_redirect( StmUser::getProfileUrl() );
				exit();
			}
			add_filter('the_content', array(self::class,'pricing_plan_page'), 100);
		}
	}

	/**
	 * @param $id
	 */
	public static function before_delete_post( $id ) {
		if($pricing_plan = StmPricingPlans::query()->where('ID', $id)->where('post_type', 'stm_pricing_plans')->findOne()) {
			if($pricing_plan->checkForDelete()) {
				do_action("ulisting_pricing_plan_before_delete", $pricing_plan);
			}else{
				wp_redirect(admin_url('edit.php?post_status=trash&post_type=stm_pricing_plans'));
				exit();
			}
		}
	}

	/**
	 * @return bool
	 */
	public function checkForDelete() {
		$user_plan = StmUserPlan::query()
						->where('plan_id', $this->ID)
						->where_in('status', array(
							StmUserPlan::STATUS_PENDING,
							StmUserPlan::STATUS_ACTIVE,
							StmUserPlan::STATUS_INACTIVE))
						->find(true);
		if($user_plan)
			return false;
		return true;
	}

    /**
     * @return array|int|object|null
     */
	public static function get_pricing_plans() {
	    global $wpdb;
        return StmPricingPlans::query()
            ->asTable("plan")
            ->join(' left join `'.$wpdb->prefix.'postmeta` as meta on (meta.`post_id` = plan.ID AND meta.`meta_key` = "payment_type") ')
            ->where('plan.post_type','stm_pricing_plans')
            ->where('plan.post_status','publish')
            ->where('meta.`meta_value`',"one_time")
            ->group_by('plan.ID')
            ->find();
    }

    /**
     * @return array|int|object|null
     */
    public static function get_subscription_plans() {
	    global $wpdb;
	    return StmPricingPlans::query()
            ->asTable("plan")
            ->join(' left join `'.$wpdb->prefix.'postmeta` as meta on (meta.`post_id` = plan.ID AND meta.`meta_key` = "payment_type") ')
            ->where('plan.post_type','stm_pricing_plans')
            ->where('plan.post_status','publish')
            ->where('meta.`meta_value`',"subscription")
            ->group_by('plan.ID')
            ->find();
    }

	/**
	 * @param $content
	 *
	 * @return string content
	 */
	public static function pricing_plan_page($content) {
		$page = get_post();
		if($page->ID ==  StmListingSettings::getPages(StmListingSettings::PAGE_PRICING_PLAN)) {
			global $wpdb;

			$subscription_plans = [];
			$plans = self::get_pricing_plans();
			if ( ulisting_subscription_active() ) {
                $subscription_plans = self::get_subscription_plans();
            }

			$content.= StmListingTemplate::load_template('pricing-plan/pricing-plan',['plans' => $plans, 'subscription_plans' => $subscription_plans]);
		}
		return $content;
	}

	/**
	 * @param $meta_key string
	 * @param bool $flip boolean
	 *
	 * @return array|mixed|null
	 */
	public function getMeta($meta_key){
		if($meta_value = get_post_meta($this->ID, $meta_key, true) AND !empty($meta_value)) {
			return $meta_value;
		}
		return null;
	}

	/**
	 * @param null $key
	 *
	 * @return array|mixed|null
	 */
	public function getData($key = null) {
		$data = [];
		$data_keys = [
			"type",
			"payment_type",
			"price",
			"listing_limit",
			"listing_image_limit",
			"feature_limit",
			"duration",
			"duration_type",
			"status"
		];


		foreach ( $data_keys as $data_key )
			$data[$data_key] =  $this->getMeta($data_key);
		if($key AND !is_array($key) AND isset($data[$key]))
			return $data[$key];
		return $data;
	}

	/**
	 * @param $data
	 *
	 * @return StmPricingPlans
	 */
	public static function load($data) {
		$model = new StmPricingPlans();
		foreach ($data as $key => $val) {
			$model->$key = $val;
		}
		return $model;
	}

	public static function edit_panel_init() {
		add_meta_box('stm_pricing_plans_edit', 'Pricing plans manager',
			[self::class, 'render_edit'], 'stm_pricing_plans', 'advanced', 'high');
	}

	public static function  render_edit() {
		ulisting_render_template(ULISTING_PATH_LIB_PRICING_PLAN . '/views/pricing-plan/edit.php', [], true);
	}

	public static function action_save_post( ) {
	    $result       = [
	        'success'       => true,
            'status'        => 'error',
            'notice'        => null,
            'return_url'    => '',
            'message'       => __('Cannot save plan', 'ulisting')
        ];

        $request_data = ulisting_sanitize_array($_POST);
        $post_ID      = isset($_POST['post_id']) ? (int)sanitize_text_field($_POST['post_id']) : '';

		if ( current_user_can('manage_options') && !empty($post_ID) && isset($_POST['nonce']) && ($pricing_plan = StmPricingPlans::find_one(intval($post_ID)))) {

            StmVerifyNonce::verifyNonce(sanitize_text_field($_POST['nonce']), 'ulisting-ajax-nonce');

			$data          = $pricing_plan->getData();
			$validator     = StmPricingPlans::getValidator($data);
			$validate_date = $validator->run($data);

			if ($validate_date === false)
				$data = (isset($request_data['data'])) ? ulisting_sanitize_array($request_data['data']) : [];

			if (
			    isset($request_data['data']['listing_limit']) && intval($request_data['data']['listing_limit']) < 0 ||
                isset($request_data['data']['feature_limit']) && intval($request_data['data']['feature_limit'] ) < 0
            ) die;

			if ( isset($data['payment_type']) && $data['payment_type'] == StmPricingPlans::PRICING_PLANS_PAYMENT_TYPE_SUBSCRIPTION AND $validate_date != false ) {
				$data['status']        = sanitize_text_field($request_data['data']['status']);
                $data['listing_limit'] = sanitize_text_field($request_data['data']['listing_limit']);
                $data['listing_image_limit'] = sanitize_text_field($request_data['data']['listing_image_limit']);
                $data['feature_limit'] = sanitize_text_field($request_data['data']['feature_limit']);
			}

			if ( isset($data['payment_type']) && $data['payment_type'] == StmPricingPlans::PRICING_PLANS_PAYMENT_TYPE_ONE_TIME )
				$data = (isset($request_data['data'])) ? ulisting_sanitize_array($request_data['data']) : [];

            $validator          = self::getValidator($data);
            $validate_date      = $validator->run($data);
            $result['notice']   = self::pricing_notice($validator->get_errors_array());
            $result['validate'] = $validate_date;
            $old_status         = $pricing_plan->post_status;

            if ( $validate_date ) {
                wp_update_post(array (
                    'ID'          => $post_ID,
                    'post_type'   => 'stm_pricing_plans',
                    'post_title'  => $data['title'],
                    'post_status' => 'publish',
                ));

                foreach ($data as $key => $val)
                    update_post_meta( $post_ID, $key, esc_attr($val));

                $result['return_url'] = admin_url('post.php?post='. $post_ID .'&action=edit');
                $result['status']     = 'success';
                $result['message']    = __('Pricing plan saved successfully!', 'ulisting');
            }

            if ( $old_status === 'auto-draft' )
                $result['validate'] = false;

			$data = $pricing_plan->getData($data);
            if ($validator->run($data) != false)
				do_action("ulisting_pricing_plan_update", $pricing_plan);


        }

		wp_send_json($result);
	}

	public static function pricing_notice($errors) {
	    return \uListing\Classes\Notices::pricing_notice(\uListing\Classes\Notices::TYPE_ERROR, $errors, false);
    }

	/**
	 * @return Validation
	 */
	public static function getValidator($data = null) {
		$validator = new Validation();
		$rules = [];

		if($data != null AND $data['payment_type'] == StmPricingPlans::PRICING_PLANS_PAYMENT_TYPE_SUBSCRIPTION ) {
			$rules = [
			    'title'        => 'required',
				'payment_type' => 'required',
                'price'      => 'required',
				'duration' => 'required',
				'duration_type' => 'required',
				'status' => 'required',
			];
		}

		if($data != null AND $data['payment_type'] == StmPricingPlans::PRICING_PLANS_PAYMENT_TYPE_ONE_TIME) {
			$rules = [
                'title'        => 'required',
				'type' => 'required',
                'price'      => 'required',
                'payment_type' => 'required',
				'duration' => 'required',
				'duration_type' => 'required',
				'status' => 'required',
			];

			if($data['type'] == StmPricingPlans::PRICING_PLANS_TYPE_LIMIT_COUNT)
				$rules['listing_limit'] = 'required|integer|min_numeric,0';

			if($data['type'] == StmPricingPlans::PRICING_PLANS_TYPE_LIMIT_IMAGE_COUNT)
				$rules['listing_image_limit'] = 'required|integer|min_numeric,0';

			if($data['type'] == StmPricingPlans::PRICING_PLANS_TYPE_FEATURE)
				$rules['feature_limit'] = 'required|integer|min_numeric,0';
		}

		if($data == null OR empty($rules)) {
			$rules = [
                'title' => 'required',
				'type' => 'required',
                'price'      => 'required',
				'payment_type' => 'required',
				'duration' => 'required',
				'duration_type' => 'required',
				'status' => 'required',
			];
		}

		$validator->validation_rules($rules);
		return $validator;
	}

	/**
	 * @param string $size
	 *
	 * @return string url
	 */
	public function getfeatureImage($size = 'thumbnail') {
		$image = wp_get_attachment_image_src(get_post_thumbnail_id($this->ID), $size);
		if(isset($image[0]))
			return $image[0];
		return false;
	}

	public static function pricingPlansTypeListData($type = null) {

		$types = array(
			self::PRICING_PLANS_TYPE_LIMIT_COUNT => esc_html__("Limit count", "ulisting"),
			self::PRICING_PLANS_TYPE_FEATURE     => esc_html__("Feature plan", "ulisting")
		);
		return ($type && isset($types[$type])) ? $types[$type] : $types;
	}

	/**
	 * @return array
	 */
	public static function pricingPaymentTypeListData($payment_type = null) {
		$payment_types = array(
			self::PRICING_PLANS_PAYMENT_TYPE_ONE_TIME     => esc_html__("One-time payment", "ulisting"),
			self::PRICING_PLANS_PAYMENT_TYPE_SUBSCRIPTION => esc_html__("Subscription", "ulisting")
		);
		return ($payment_type) ? $payment_types[$payment_type] : $payment_types;
	}

	/**
	 * @return string
	 */
	public static function get_page_url() {
		return get_page_link(StmListingSettings::getPages(StmListingSettings::PAGE_PRICING_PLAN));
	}

	/**
	 * @return array
	 */
	public static function getListData() {
		$models = StmPricingPlans::query()
			     ->where('post_type','stm_pricing_plans')
			     ->where('post_status','publish')
			     ->find();
		return ArrayHelper::map($models, 'ID', 'post_title');
	}

	/**
	 * @return array
	 */
	public static function getStatus($status = null) {
		$status_list = array(
			self::STATUS_ACTIVE   => esc_html__('Active', "ulisting"),
			self::STATUS_INACTIVE => esc_html__('Inactive', "ulisting")
		);
		return ($status) ? $status_list[$status] : $status_list;
	}

	/**
	 * @param null $type
	 *
	 * @return array|mixed
	 */
	public static function getDurationType($type = null) {
		$list = array(
			self::DURATION_TYPE_DAY   => esc_html__('Day', "ulisting"),
			self::DURATION_TYPE_MONTH => esc_html__('Month', "ulisting"),
			self::DURATION_TYPE_YEAR  => esc_html__('Year', "ulisting")
		);
		return ($type) ? $list[$type] : $list;
	}

    /**
     * @param $data
     *
     * @return mixed|void
     */
	public static function payment_one_time($data) {
        if(isset($data['payment_method']) AND $data['payment_method'] == 'free') {
            $result     = array(
                'success' => false,
                'errors'  => false,
                'message' => "",
            );
            $limit = 0;
            $user = new StmUser(get_current_user_id());
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
                        'payment_method' => 'free',
                        'limit' => $limit,
                    )
                );


                $user_plan->status = StmUserPlan::STATUS_ACTIVE;
                $user_plan->save();

                $result['success'] = true;
                $result['user_plan_id'] = $user_plan->id;

                $payment = new StmPayment();
                $payment->user_plan_id   = $user_plan->id;
                $payment->payment_method = 'free';
                $payment->status         = "succeeded";
                $payment->amount         = $plan_data['price'];
                $payment->transaction    = "empty";
                $payment->save();

                return $result;
            }
        }

        return $data;
    }

	/**
	 * @throws \Exception
	 * @throws \uListing\Classes\Vendor\Exception
	 */
	public static function payment_pricing_plan() {
		$result     = array(
	        'success' => false,
	        'errors'  => false,
	        'message' => "",
		);

		$request_body = file_get_contents('php://input');
		$request_data = json_decode($request_body, true);

		$rules      = array(
		    'name'  => 'required',
			'email' => 'required',
			'payment_method' => 'required',
			'pricing_plan_id' => 'required|integer'
		);

		$validator = new Validation();
		$validator->validation_rules($rules);
		$validator->run($request_data);
		$errors = $validator->get_errors_array();

		if( !empty($errors) ) {
			$result['errors'] = $errors;
			return $result;
		}

		if( !($pricing_plan = StmPricingPlans::find_one($request_data['pricing_plan_id'])) ) {
			$result['message'] = esc_html__('Plan not found :(');
			return $result;
		}

		$pricing_plan_data = $pricing_plan->getData();

		if($pricing_plan_data['payment_type'] == self::PRICING_PLANS_PAYMENT_TYPE_ONE_TIME) {
			$request_data['pricing_plan'] = $pricing_plan;
			return apply_filters("ulisting_pricing_plan_payment_one_time", $request_data);
		}

		if($pricing_plan_data['payment_type'] == self::PRICING_PLANS_PAYMENT_TYPE_SUBSCRIPTION) {
			$request_data['pricing_plan'] = $pricing_plan;
			return apply_filters("ulisting_pricing_plan_payment_subscription", $request_data);
		}
	}

	public static function pricing_plan_data() {
        $result = [
            'success' => false,
            'errors'  => false,
            'message' => __('Access denied'),
        ];

        $request_data = ulisting_sanitize_array($_GET);

        if ( ! current_user_can('manage_options') ) {
            wp_send_json($result);
        }

        $rules        = ['pricing_plan_id' => 'required|integer'];

        $validator = new Validation();
        $validator->validation_rules($rules);
        $validator->run($request_data);
        $errors    = $validator->get_errors_array();

        if ( !empty( $errors ) ) {
            $result['errors'] = $errors;
            wp_send_json($result);
        }

        $pricing_plan               = StmPricingPlans::find_one((int)sanitize_text_field($request_data['pricing_plan_id']));
        $pricing_plan_data          = $pricing_plan->getData();
        $pricing_plan_data['title'] = $pricing_plan->post_title;
        $pricing_plan_data['price'] = isset($pricing_plan_data['price']) ? (float) $pricing_plan_data['price'] : 0;

        if (empty($pricing_plan_data['payment_type']))
            $pricing_plan_data['payment_type'] = StmPricingPlans::PRICING_PLANS_PAYMENT_TYPE_ONE_TIME;

        if (empty($pricing_plan_data['type']))
            $pricing_plan_data['type'] = StmPricingPlans::PRICING_PLANS_TYPE_LIMIT_COUNT;

        $validator     = StmPricingPlans::getValidator($pricing_plan_data);
        $validate_date = $validator->run($pricing_plan_data);

        $disabled = [
            'title'         => false,
            'type'          => false,
            'duration'      => false,
            'payment_type'  => false,
            'duration_type' => false
        ];

        if ( $validate_date AND $pricing_plan_data['payment_type'] == StmPricingPlans::PRICING_PLANS_PAYMENT_TYPE_SUBSCRIPTION) {
            $disabled['type']           = true;
            $disabled['duration']       = true;
            $disabled['payment_type']   = true;
            $disabled['duration_type']  = true;
        }

        if ( $validate_date AND $pricing_plan_data['payment_type'] == StmPricingPlans::PRICING_PLANS_PAYMENT_TYPE_ONE_TIME) {
            $disabled['type']          = true;
            $disabled['payment_type']  = true;
        }

        if ($pricing_plan->post_status == "auto-draft") {
            $validate_date = true;
        }

        $other             = [
            "subscription_active" => ulisting_subscription_active(),
            "disabled"            => $disabled,
            "validate_date"       => $validate_date,
            "text_domains"        => StmListingSettings::get_all_texts(),
            "notice"              => self::pricing_notice($validator->get_errors_array()),
        ];

        $result['success'] = true;
        $result['message'] = __('Pricing plan data loaded successfully', 'ulisting');
        $result['data']    = array_merge($other, self::render_pricing_plan_data($pricing_plan_data));
        wp_send_json($result);
    }

    static private function render_pricing_plan_data($pricing_plan_data) {
        $type_list           = [];
        $status_list         = [];
        $duration_type_list  = [];
        $payment_method_list = [];

        foreach (StmPricingPlans::pricingPaymentTypeListData() as $k => $v) {
            $disabled_payment_type = false;

            if ($k == StmPricingPlans::PRICING_PLANS_PAYMENT_TYPE_SUBSCRIPTION AND !ulisting_subscription_active())
                $disabled_payment_type = true;

            $payment_method_list[] = [
                "id"       => $k,
                "title"    => $v,
                "disabled" => $disabled_payment_type
            ];
        }

        foreach (StmPricingPlans::pricingPlansTypeListData() as $k => $v)
            $type_list[$k] = $v;

        foreach (StmPricingPlans::getDurationType() as $k => $v)
            $duration_type_list[$k] = $v;

        foreach (StmPricingPlans::getStatus() as $k => $v)
            $status_list[$k] = $v;

        return [
	        'title'         => StmListingSettings::settings_input_creator(StmListingSettings::isset_helper($pricing_plan_data, 'title'), 'Title', 'text'),
            'payment_type'  => StmListingSettings::settings_radio_creator(StmListingSettings::isset_helper($pricing_plan_data, 'payment_type', 'subscription'), '', $payment_method_list),
            'plan_type'     => StmListingSettings::settings_select_creator(StmListingSettings::isset_helper($pricing_plan_data, 'type', 'limit_count'), 'Plan Type', $type_list),
            'price'         => StmListingSettings::settings_input_creator(StmListingSettings::isset_helper($pricing_plan_data, 'price', 0), 'Plan Price', 'number', '', 0),
            'listing_limit' => StmListingSettings::settings_input_creator(StmListingSettings::isset_helper($pricing_plan_data, 'listing_limit', 0), 'Listing limit', 'number', '', 0),
            'listing_image_limit' => StmListingSettings::settings_input_creator(StmListingSettings::isset_helper($pricing_plan_data, 'listing_image_limit', 0), 'Listing image limit', 'number', '', 0),
            'feature_limit' => StmListingSettings::settings_input_creator(StmListingSettings::isset_helper($pricing_plan_data, 'feature_limit', 0), 'Feature limit', 'number', '',0),
            'status'        => StmListingSettings::settings_select_creator(StmListingSettings::isset_helper($pricing_plan_data, 'status', 'active'), 'Status', $status_list),
            'duration'      => StmListingSettings::settings_duration_creator(StmListingSettings::isset_helper($pricing_plan_data, 'duration', 0), StmListingSettings::isset_helper($pricing_plan_data, 'duration_type', 'days'), $duration_type_list, 'Duration'),
        ];
    }

    public static function get_all_plans() {
        return StmPricingPlans::query()
            ->where('post_type','stm_pricing_plans')
            ->where('post_status','publish')
            ->find();
    }
}

