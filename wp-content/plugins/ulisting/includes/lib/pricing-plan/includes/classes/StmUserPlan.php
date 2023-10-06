<?php
namespace uListing\Lib\PricingPlan\Classes;

use uListing\Admin\Classes\StmEmailTemplateManager;
use uListing\Classes\StmListing;
use uListing\Classes\StmListingAttributeRelationships;
use uListing\Classes\StmListingSettings;
use uListing\Classes\StmUser;
use uListing\Classes\StmVerifyNonce;
use uListing\Classes\Vendor\Validation;
use uListing\Classes\Vendor\StmBaseModel;
use uListing\Lib\PayPal\Classes\PayPal;
use uListing\Lib\PricingPlan\Classes\StmListingPlan;
use uListing\Lib\PricingPlan\Classes\StmUserPlanListTable;

class StmUserPlan extends StmBaseModel {

	const STATUS_ACTIVE   = 'active';
	const STATUS_PENDING  = 'pending';
	const STATUS_INACTIVE = 'inactive';
	const STATUS_CANCELED = 'canceled';

	protected $fillable = [
		'id',
		'user_id',
		'plan_id',
		'status',
		'type',
		'payment_type',
		'expired_date',
		'created_date',
		'updated_date',
	];

    public $id;
    public $user_id;
    public $plan_id;
    public $status;
    public $type;
    public $payment_type;
    public $expired_date;
    public $created_date;

	public static function get_primary_key() {
		return 'id';
	}

	public static function get_table() {
		global $wpdb;
		return $wpdb->prefix . 'ulisting_user_plan';
	}

	public static function get_searchable_fields() {
		return [
			'id',
			'user_id',
			'plan_id',
			'status',
			'type',
			'payment_type',
			'expired_date',
			'created_date',
			'updated_date',
		];
	}

	public static function init() {
		$model = new StmUserPlan();
		add_action( 'admin_menu', [ $model, 'add_menu' ]);
		add_action( 'ulisting_payment_completed', [ self::class, 'payment_completed' ] );
	}

	/**
	 * @param $payment StmPayment
	 */
	public static function payment_completed($payment){
		if($user_plan = StmUserPlan::find_one($payment->user_plan_id)) {
			$user_plan->status = self::STATUS_ACTIVE;
			$user_plan->save();
		}
	}

	public function add_menu() {
		$hook = add_submenu_page(
			'edit.php?post_type=stm_pricing_plans',
			esc_html__("User Plans", "ulisting"),
			esc_html__("User Plans", "ulisting"),
			'manage_options',
			'stm_user_plans',
			array($this, 'render_index')
		);

		add_action( "load-$hook", [ $this, 'stm_user_plans_screen_option' ] );

		add_submenu_page(
			null,
			null,
			false,
			'manage_options',
			'stm_user_plans_add',
			array($this, 'render_index_add')
		);

		add_submenu_page(
			null,
			null,
			false,
			'manage_options',
			'stm_user_plans_edit',
			array($this, 'render_index_edit')
		);

		add_submenu_page(
			null,
			null,
			false,
			'manage_options',
			'stm_user_plans_view',
			array($this, 'render_index_view')
		);

	}

    public function get_user_plan_meta() {
        $user_plan_meta = StmUserPlanMeta::query()
            ->where('user_plan_id', $this->id)
            ->where('meta_key', 'limit');
        return $user_plan_meta->findOne();
    }

	public function stm_user_plans_screen_option() {
		$option = 'per_page';
		$args   = [
			'label'   => 'User plans',
			'default' => 5,
			'option'  => 'stm_user_plans_per_page'
		];

		add_screen_option( $option, $args );
		$this->object = new StmUserPlanListTable();
	}

	public function render_index() {
		ulisting_render_template(ULISTING_PATH_LIB_PRICING_PLAN . '/views/admin/user_plan/index.php', [], true);
	}

	public function render_index_add() {
		ulisting_render_template(ULISTING_PATH_LIB_PRICING_PLAN . '/views/admin/user_plan/add.php', [], true);
	}

	public function render_index_edit() {
		ulisting_render_template(ULISTING_PATH_LIB_PRICING_PLAN . '/views/admin/user_plan/add.php', [], true);
	}

	public function render_index_view() {

		if(isset($_GET['id']))
			$user_plan = StmUserPlan::find_one(sanitize_text_field($_GET['id']));

		ulisting_render_template(ULISTING_PATH_LIB_PRICING_PLAN . '/views/admin/user_plan/view.php', ['user_plan' => $user_plan], true);
	}

	/**
	 * @return array
	 */
	public static function getTypeList($type) {
		$types = array(
			self::TYPE_FREE => esc_html__('Free', "ulisting"),
			self::TYPE_PAID => esc_html__('Paid', "ulisting")
		);
		return ($type) ? $types[$type] : $types;
	}

	/**
	 * @return array
	 */
	public static function getStatus($status = null) {
		$statuses = array (
			self::STATUS_PENDING  => esc_html__('Pending', "ulisting"),
			self::STATUS_ACTIVE   => esc_html__('Active', "ulisting"),
			self::STATUS_INACTIVE => esc_html__('Inactive', "ulisting"),
			self::STATUS_CANCELED => esc_html__('Canceled', "ulisting")
		);
		return ($status) ? $statuses[$status] : $statuses;
	}

	public function setActiveStatus() {
		$pricing_plan       = $this->getPricingPlan();
		$pricing_plan_data  = $pricing_plan->getData();
		$this->expired_date = date('Y-m-d', strtotime("".date('Y-m-d', strtotime($this->expired_date))." +".$pricing_plan_data['duration']." ".$pricing_plan_data['duration_type'].""));
		if($this->getMeta('first_pay')){
			$this->expired_date = date('Y-m-d', strtotime("+".$pricing_plan_data['duration']." ".$pricing_plan_data['duration_type'].""));
			$this->deleteMeta('first_pay');
		}

		if($this->checkExpired())
			$this->status = StmUserPlan::STATUS_ACTIVE;

		$this->save();
	}

	public static function get_user_plan_data() {
	    $result = [
	        'success' => false,
            'message' => __('Access denied', 'ulisting'),
            'status'  => 'error',
            'data'    => [],
        ];

	    if ( current_user_can('manage_options') ) {

	        $user_plan_id   = StmListingSettings::isset_helper($_GET, 'id');
            $user_plan      = self::find_one($user_plan_id);
            $user           = null;

            if ( !empty($user_plan) )
                $user = $user_plan->getUser();

            $plan_options   = StmPricingPlans::getListData();
            $expired_value  = StmListingSettings::isset_helper($user_plan, 'expired_date');
            $status_value   = StmListingSettings::isset_helper($user_plan, 'status',  self::get_first_index(StmUserPlan::getStatus()));
            $plan_value     = StmListingSettings::isset_helper($user_plan, 'plan_id', self::get_first_index($plan_options));

            if ( empty( $plan_options ) ) {
                $plan_options = [null => __('No Pricing plans', 'ulisting')];
                $plan_value   = null;
            }


            $user_data      = [
                'id'    =>  !empty( $user->ID )                 ? $user->ID                 : null,
                'name'  =>  !empty( $user->data->display_name ) ? $user->data->display_name : __("Type to name or email for search user", "ulisting"),
                'email' =>  !empty( $user->data->user_email )   ? $user->data->user_email   : null,
            ];

	        $data = [
                'user'         => self::generate_user_plan_data('User', $user_data),
                'plan'         => StmListingSettings::settings_select_creator($plan_value, 'Plan', $plan_options),
                'date'         => StmListingSettings::settings_date_creator($expired_value, 'Expired date'),
                'status'       => StmListingSettings::settings_select_creator($status_value, 'Status', StmUserPlan::getStatus()),
                'text_domains' => StmListingSettings::get_all_texts(),
            ];

	        $result['data']    = $data;
            $result['status']  = 'success';
            $result['success'] = true;
	        $result['message'] = __('User Plan data got successfully.', 'ulisting');
        }

	    wp_send_json($result);
    }

    private static function generate_user_plan_data($title, $data) {
	    return [
	        'title' => $title,
            'name'  => 'user-search',
            'user'  => $data
        ];
    }

    private static function get_first_index($options) {
        if ( !empty( $options ) ) {
            $keys  = array_keys($options);
            return array_shift($keys);
        }

        return '';
    }

	/**
	 * @throws \Exception
	 * @throws \uListing\Classes\Vendor\Exception
	 */
	public static function create_action() {

		$data = ulisting_sanitize_array($_POST);
		$validator = new Validation();
		// status changed
		$result = array(
			'success' => false,
			'message' => __('Cannot create User Plan.', 'ulisting'),
			'status'  => 'error',
            'notice'  => null,

            'validate_date' => true
		);

		$validation_rules = array(
			'user'    => 'required',
			'plan'    => 'required',
			'status'  => 'required',
		);

		$validator->validation_rules($validation_rules);
		$validated_data = $validator->run($data);

		if ($validated_data === false) {
		    $result['validate_date'] = $validated_data;
			$result['notice']        = StmPricingPlans::pricing_notice($validator->get_errors_array());
			wp_send_json($result);
			die;
		}

		if ( !current_user_can('manage_options') && !isset($data['nonce']) ) {
		    wp_send_json($result);
		    die();
        }

        StmVerifyNonce::verifyNonce(sanitize_text_field($data['nonce']), 'ulisting-ajax-nonce');

		if ( !($plan = StmPricingPlans::find_one($data['plan'])) ) {
			$result['message'] = esc_html__('Plan not found', "ulisting");
			wp_send_json($result);
			die;
		}

		$plan_data = $plan->getData();
        $id        = null;

		if (isset($data['id']) AND $user_plan = StmUserPlan::find_one($data['id'])) {
		    $payment                  =  StmPayment::getByUserPlanId($data['id']);
            $archive_status           = $user_plan->status;
			$user_plan->user_id       = $data['user'];
			$user_plan->status        = $data['status'];
			$user_plan->expired_date  = date('Y-m-d', strtotime($data['expired_date']));
            $id                       = $data['id'];

            if ( !empty($payment) && !($info = $payment->getMeta('user_info')) AND isset( $info->meta_value ) && $archive_status !== $data['status']) {
                $details = json_decode($info->meta_value);
                $args = [
                    'date'           => date('Y-m-d H:i:s'),
                    'payment'        => $payment,
                    'user_name'      => isset($details->name) ? $details->name : '',
                    'user_email'     => isset($details->email) ? $details->email : '',
                    'plan_id'        => isset($payment->user_plan_id) ? $payment->user_plan_id : null,

                    'payment_status_before' => $archive_status,
                    'payment_status_after'  => $data['status'],
                ];

                // payment status changed
                StmEmailTemplateManager::uListing_send_email($args, 'payment-status-changed', true);
            }
		} else {
			$user_plan                = new StmUserPlan();
            $id                       = $user_plan->user_id;
			$user_plan->user_id       = $data['user'];
			$user_plan->plan_id       = $data['plan'];
			$user_plan->status        = $data['status'];
			$user_plan->type          = $plan_data['type'];
			$user_plan->payment_type  = $plan_data['payment_type'];
			$user_plan->expired_date  = date('Y-m-d', strtotime("+".$plan_data['duration']." ".$plan_data['duration_type'].""));
			$user_plan->created_date  = date('Y-m-d H:i:s');
			$user_plan->updated_date  = date('Y-m-d H:i:s');
		}

		if ( $user_plan->save() ) {
			$result['success']    = true;
			$result['status']     = 'success';
			$result['user_plan']  = $user_plan;
			$result['return_url'] = admin_url('edit.php?post_type=stm_pricing_plans&page=stm_user_plans&id='. $id);
			$result['message']    = esc_html__('User plan save completed successfully.', "ulisting");
		}

		wp_send_json($result);
		die;
	}

    /**
     * @param $info
     * @param $payment
     * @param $status
     * @return bool|mixed
     */
	public static function user_plan_email_template($info, $payment, $status = '') {
        return \uListing\Classes\StmListingTemplate::load('email', [
            'info' => $info,
            'status' => $status,
            'payment' => $payment
        ], 'pricing-plan', ULISTING_PATH_LIB_PRICING_PLAN.'/templates/');
    }

	/**
	 * @param $pricing_plan StmPricingPlans
	 * @param $user_id
	 * @param array $meta
	 *
	 * @return StmUserPlan
	 */
	public static function createNew($pricing_plan, $user_id, $meta = array()) {
		$plan_data                = $pricing_plan->getData();
		$user_plan                = new StmUserPlan();
		$user_plan->user_id       = $user_id;
		$user_plan->plan_id       = $pricing_plan->ID;
		$user_plan->status        = StmUserPlan::STATUS_INACTIVE;
		$user_plan->type          = (isset($plan_data['type'])) ? $plan_data['type'] : "------";
		$user_plan->payment_type  = $plan_data['payment_type'];
		$user_plan->expired_date  = date('Y-m-d', strtotime("+".$plan_data['duration']." ".$plan_data['duration_type']."")); // duration_type (days, month, year)
		$user_plan->created_date  = date('Y-m-d H:i:s');
		$user_plan->save();

		foreach ($meta as $key => $value)
			$user_plan->setMeta($key, $value);

		return $user_plan;
	}

	/**
	 * @return StmUser
	 */
	public function getUser() {
		return new StmUser($this->user_id);
	}

	/**
	 * @return false|StmBaseModel
	 */
	public function getPricingPlan() {
		return StmPricingPlans::find_one($this->plan_id);
	}

	/**
	 * @param $type
	 *
	 * @return bool
	 */
	public function checkLimitForAdd($type) {
		$pricing_plan = $this->getPricingPlan();
		$meta_data = $pricing_plan->getData();

		if($this->payment_type == StmPricingPlans::PRICING_PLANS_PAYMENT_TYPE_ONE_TIME) {
			if(	$meta_data = $this->getMeta('limit') AND $meta_data->meta_value > 0)
				return true;
			return false;
		}

		$user_plan_listing_count = StmListingPlan::query()
	                                 ->where('user_plan_id', $this->id)
	                                 ->where('type', $type)
	                                 ->find(true);

		if($type == StmPricingPlans::PRICING_PLANS_TYPE_LIMIT_COUNT AND $meta_data['listing_limit'] > $user_plan_listing_count)
			return true;

		if($type == StmPricingPlans::PRICING_PLANS_TYPE_FEATURE AND $meta_data['listing_limit'] > $user_plan_listing_count)
			return true;

		return false;
	}

	/**
	 * @return bool
	 */
	public function checkExpired() {
		if($this->expired_date >= date('Y-m-d'))
			return true;
		return false;
	}

	public function before_save() {

		if(!$this->id)
			$this->created_date  = date('Y-m-d H:i:s');

		$listing = StmListing::query()
							   ->asTable('listing')
							   ->select('listing.*')
		                      ->join(" left join ".StmListingPlan::get_table()." as listing_plan on listing_plan.`listing_id` = listing.ID ")
		                      ->join(" left join ".StmUserPlan::get_table()." as user_plan on user_plan.id = listing_plan.`user_plan_id` ")
		                      ->where('listing.post_type','listing')
		                      ->where('user_plan.id', $this->id)
		                      ->find();

		if ($this->status == self::STATUS_ACTIVE) {
			foreach ($listing as $listing) {
				$listing->post_status = StmListing::STATUS_PUBLISH;
				$listing->save();
			}
		}else{
			foreach ($listing as $listing) {
				$listing->post_status = StmListing::STATUS_DRAFT;
				$listing->save();
			}
		}
		$this->updated_date  = date('Y-m-d H:i:s');

		if($this->status == StmUserPlan::STATUS_CANCELED AND $this->status != $this->old_properties->status AND !($canceled = $this->getMeta('canceled'))) {
			$this->cancel();
			$this->status = $this->old_properties->status;
		}
	}

	/**
	 * @param $key
	 *
	 * @return array
	 */
	public function getMeta($key){
		return StmUserPlanMeta::query()
	                         ->where('user_plan_id', $this->id)
	                         ->where('meta_key', $key)
	                         ->findOne();
	}

	/**
	 * @param $key
	 * @param $value
	 *
	 * @return array|StmUserPlanMeta
	 */
	public function setMeta($key, $value){
		$user_plan_meta = StmUserPlanMeta::query()
	                         ->where('user_plan_id', $this->id)
	                         ->where('meta_key', $key)
	                         ->findOne();
		if(!$user_plan_meta)
			$user_plan_meta = new StmUserPlanMeta();

		$user_plan_meta->user_plan_id = $this->id;
		$user_plan_meta->meta_key     = $key;
		$user_plan_meta->meta_value   = $value;
		$user_plan_meta->save();
		return $user_plan_meta;
	}

	/**
	 * @param $key
	 *
	 * @return array|null|object
	 */
	public function deleteMeta($key){
		return StmUserPlanMeta::query()
		                      ->where('user_plan_id', $this->id)
		                      ->where('meta_key', $key)
		                      ->delete();
	}

	/**
	 * @param $agreement_id
	 *
	 * @return array
	 */
	public static function getUserPlanByAgreementId($agreement_id) {
		return  StmUserPlan::query()
			        ->asTable('user_plan')
			        ->select(' user_plan.* ')
			        ->join(" left join ".StmUserPlanMeta::get_table()." as user_plan_meta on user_plan_meta.`user_plan_id` = user_plan.`id` and user_plan_meta.`meta_key` = 'billing_agreement_id' ")
			        ->where('user_plan_meta.`meta_value`', $agreement_id)
			        ->findOne();
	}

	/**
	 * @param $plan_id
	 *
	 * @return array
	 */
	public static function getUserPlanByStripeId($plan_id) {
		return  StmUserPlan::query()
		                   ->asTable('user_plan')
		                   ->select(' user_plan.* ')
		                   ->join(" left join ".StmUserPlanMeta::get_table()." as user_plan_meta on user_plan_meta.`user_plan_id` = user_plan.`id` and user_plan_meta.`meta_key` = 'stripe_subscription_id' ")
		                   ->where('user_plan_meta.`meta_value`', $plan_id)
		                   ->findOne();
	}

	public static function updateStatusPlanForExpired() {
	    StmUserPlan::query()
			->asTable('user_plan')
			->where_raw(' DATE(user_plan.`expired_date`) < "'.date('Y-m-d').'" ')
			->where_in("user_plan.`status`", array(StmUserPlan::STATUS_ACTIVE, StmUserPlan::STATUS_PENDING))
			->where("user_plan.`payment_type`", StmPricingPlans::PRICING_PLANS_PAYMENT_TYPE_SUBSCRIPTION)
			->update(array( "status" => StmUserPlan::STATUS_INACTIVE ));

        // subscription cancelled plan notifications
		StmUserPlan::query()
			->asTable('user_plan')
			->join(' left join '.StmUserPlanMeta::get_table().' as user_plan_meta on user_plan_meta.`user_plan_id` = user_plan.id ')
			->where_raw(' DATE(user_plan.`expired_date`) < "'.date('Y-m-d').'" ')
			->where_raw(" ( user_plan_meta.`meta_key` = 'canceled' AND user_plan_meta.`meta_value` = 1) ")
			->where_in("user_plan.`status`", array(StmUserPlan::STATUS_ACTIVE, StmUserPlan::STATUS_PENDING, StmUserPlan::STATUS_INACTIVE))
			->where("user_plan.`payment_type`", StmPricingPlans::PRICING_PLANS_PAYMENT_TYPE_SUBSCRIPTION)
			->update(array( "status" => StmUserPlan::STATUS_CANCELED ));
	}

	/**
	 * @param array $status active, pending, inactive, canceled
	 *
	 * @return array|null|object
	 */
	public static function updateStatusListingForExpired($status = ['inactive']) {
		return StmListing::query()
				->asTable('listing')
				->join('left join '.StmListingPlan::get_table().' as listing_plan on ( listing_plan.`listing_id` = listing.ID)')
				->join('left join '.StmUserPlan::get_table().' as user_plan on (user_plan.id = listing_plan.`user_plan_id` AND user_plan.`payment_type` = "'.StmPricingPlans::PRICING_PLANS_PAYMENT_TYPE_SUBSCRIPTION.'")')
				->where_raw(' ( listing.`post_type` = "listing" AND listing.`post_status` = "publish" ) ')
				->where_raw(' ( listing_plan.`listing_id` = listing.ID ) ')
				->where_raw(' ( DATE(user_plan.`expired_date`) < "'.date("Y-m-d").'" ) ')
				->where_in("user_plan.`status`", $status)
			 	->update(array( "post_status" => StmListing::STATUS_DRAFT ));
	}

	/**
	 * @param array $status active, pending, inactive, canceled
	 *
	 * @return array|null|object
	 */
	public static function removeFeatureListingForExpired($status = ['inactive']){
		return StmListingAttributeRelationships::query()
				->select('listing_attribute_relationships')
				->asTable('listing_attribute_relationships')
				->join('left join '.StmListing::get_table().' as listing on ( listing.ID = listing_attribute_relationships.`listing_id`)')
				->join('left join '.StmListingPlan::get_table().' as listing_plan on ( listing_plan.`listing_id` = listing.ID)')
				->join('left join '.StmUserPlan::get_table().' as user_plan on (user_plan.id = listing_plan.`user_plan_id` AND user_plan.`payment_type` = "'.StmPricingPlans::PRICING_PLANS_PAYMENT_TYPE_SUBSCRIPTION.'" )')
				->where_raw(' ( listing_attribute_relationships.`attribute` = "feature" )')
				->where_raw(' ( listing.`post_type` = "listing" ) ')
				->where_raw(' ( listing_plan.`listing_id` = listing.ID ) ')
				->where_raw(' ( DATE(user_plan.`expired_date`) < "'.date("Y-m-d").'" ) ')
				->where_in("user_plan.`status`", $status)
				->group_by('listing_attribute_relationships.id')
				->delete();
	}

	/**
	 * Check plans expired date
	 */
	public static function checkPlansExpired(){
		self::updateStatusPlanForExpired();
		self::updateStatusListingForExpired(['inactive', 'canceled']);
		self::removeFeatureListingForExpired(['inactive', 'canceled']);
	}

	/**
	 * @param null $limit
	 * @param null $offset
	 * @param array $filter
	 * @param null $only_count
	 *
	 * @return array|int|null|object
	 */
	public static function getList($limit = null, $offset = null, $filter = array(), $only_count = null) {
		$payments = StmUserPlan::query()
		                      ->select(' user_plan.* ')
		                      ->asTable('user_plan');

		foreach ($filter as $key => $val) {
			if(is_array($val))
				$payments->where_in("user_plan.".$key, $val);
			else
				$payments->where("user_plan.".$key, $val);
		}
		if($limit != null)
			$payments->limit($limit);

		if($offset != null)
			$payments->offset($offset);

		if(!$only_count)
			$payments->group_by('user_plan.id');

		$payments->sort_by(" id ");
		$payments->order(" DESC ");

		return $payments->find($only_count);
	}

	/**
	 * @return array
	 */
	public function cancel(){
		$result = apply_filters("ulisting_user_plan_canceld", ['user_plan' => $this]);
		if( (isset($result['success']) AND $result['success']) OR (isset($result['user_plan'])) AND $result['user_plan'] instanceof StmUserPlan) {
            $archive_status = $this->status;
            $this->status = self::STATUS_CANCELED;
			$this->setMeta('canceled', 1);
			$this->save();
			$payment = StmPayment::find_one_by('user_plan_id', $this->id);
            $info = $payment->getMeta('user_info');

            if ( !empty( $info ) AND isset( $info->meta_value )) {
                $details = json_decode($info->meta_value);
                $args = [
                    'date'           => date('Y-m-d H:i:s'),
                    'payment'        => $payment,
                    'user_name'      => isset($details->name) ? $details->name : '',
                    'user_email'     => isset($details->email) ? $details->email : '',
                    'plan_id'        => isset($payment->user_plan_id) ? $payment->user_plan_id : null,

                    'payment_status_before' => $archive_status,
                    'payment_status_after'  => self::STATUS_CANCELED,
                ];
                StmEmailTemplateManager::uListing_send_email($args, 'payment-status-changed', true);
            }
		}
		return $result;
	}

	/**
	 * @throws \Exception
	 * @throws \uListing\Classes\Vendor\Exception
	 */
	public static function api_cancel() {
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

		if ($validated_data === false) {
			$result['errors'] = $validator->get_errors_array();
			return $result;
		}

		$user_plan = StmUserPlan::query()
	                        ->where('id', $validated_data['id'])
	                        ->where('user_id', $validated_data['user_id'])
	                        ->findOne();
		if (!$user_plan) {
			$result['message'] = __('Plan not found :(', 'ulisting');
			return $result;
		}
		$result = $user_plan->cancel();
		return $result;
	}

	/**
	 *
	 * @param bool $only_count
	 *
	 * @return array|int|null|object
	 */
	public static function  check_not_canceled_plan($payment_method = null) {
		$user_plan = StmUserPlan::query()
								->asTable("user_plan")
								->join(" left join ".StmUserPlanMeta::get_table()." as user_plan_meta on user_plan_meta.`user_plan_id` = user_plan.id AND user_plan_meta.`meta_key` = 'canceled' ")
								->where_in("user_plan.`status`", array(StmUserPlan::STATUS_PENDING, StmUserPlan::STATUS_ACTIVE, StmUserPlan::STATUS_INACTIVE))
								->where_raw(" IF(user_plan_meta.`meta_value`, 1, 0) = 0 ");
		if($payment_method != null){
			$user_plan->join(" left join ".StmUserPlanMeta::get_table()." as user_plan_meta_payment_method on user_plan_meta_payment_method.`user_plan_id` = user_plan.id AND user_plan_meta_payment_method.`meta_key` = 'payment_method'  ")
				->where('user_plan_meta_payment_method.`meta_value`', $payment_method)
				->group_by("user_plan.id");
		}
		return $user_plan->find();
	}

    /**
     * @param null $payment_method
     *
     * @return int
     */
    public static function  get_active_plans_count($payment_method = null) {
        return StmUserPlan::query()
            ->asTable("user_plan")
            ->where("user_plan.`status`", StmUserPlan::STATUS_ACTIVE)
            ->total_count();
    }

	public function removeLimit($count = 1) {
		$mete_data = $this->getMeta('limit');
		if($mete_data->meta_value == 0)
			return false;
		$mete_data->meta_value -= $count;
		$mete_data->save();
		return true;
	}
}


