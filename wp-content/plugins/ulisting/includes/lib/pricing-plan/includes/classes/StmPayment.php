<?php
namespace uListing\Lib\PricingPlan\Classes;

use uListing\Classes\StmUser;
use uListing\Classes\Vendor\StmBaseModel;
use uListing\Lib\PricingPlan\Classes\StmPaymentMeta;

class StmPayment extends StmBaseModel{

	const PAYMENT_METHOD_PAYPAL_STANDARD = "paypal_standard";
	const PAYMENT_METHOD_PAYPAL = "paypal";
	const PAYMENT_METHOD_CARD   = "card";
	const STATUS_UNCAPTURED       = "uncaptured";
	const STATUS_REFUNDED       = "refunded";
	const STATUS_REVERSED       = "reversed";
	const STATUS_COMPLETED      = "completed";
	const STATUS_SUCCEEDED      = "succeeded";
	const STATUS_PENDING        = "pending";

	protected $fillable = [
		'id',
		'user_plan_id',
		'payment_method',
		'status',
		'transaction',
		'amount',
		'created_date',
		'updated_date',
	];

	public $id;
	public $user_plan_id;
	public $payment_method;
	public $status;
	public $transaction;
	public $amount;
	public $created_date;
	public $updated_date;

	public static function get_primary_key() {
		return 'id';
	}

	public static function get_table() {
		global $wpdb;
		return $wpdb->prefix . 'ulisting_payment';
	}

	public static function get_searchable_fields() {
		return [
			'id',
			'user_plan_id',
			'payment_method',
			'status',
			'transaction',
			'amount',
			'created_date',
			'updated_date',
		];
	}

	public static function init() {

	}

	/**
	 * @param null $type
	 *
	 * @return array|mixed
	 */
	public static function getTypeList($type = null) {
		$types = array(
			self::TYPE_FREE => esc_html__('Free', "ulisting"),
			self::TYPE_PAID => esc_html__('Paid', "ulisting")
		);
		return ($type) ? $types[$type] : $types;
	}

	public function before_save() {
		if(!$this->id)
			$this->created_date  = date('Y-m-d H:i:s');
		$this->updated_date  = date('Y-m-d H:i:s');
		if(isset($this->old_properties->status) AND $this->old_properties->status == 'pending') {
			if( $this->payment_method == "paypal_standard" AND $this->status == "Completed"){
					do_action("ulisting_payment_completed", $this);
			}
		}
	}

	/**
	 * @param null $method
	 *
	 * @return array|mixed
	 */
	public static function getPaymentMethodList($method = null){
		$payment_methods = array(
			 self::PAYMENT_METHOD_PAYPAL => esc_html__("Paypal", "ulisting"),
			 self::PAYMENT_METHOD_CARD   => esc_html__("Card", "ulisting"),
			 self::PAYMENT_METHOD_PAYPAL_STANDARD   => esc_html__("Paypal standard", "ulisting"),
		);
		$payment_methods = apply_filters("ulisting_payment_method_list", []);
		if($method)
			return ($method AND isset($payment_methods[$method])) ? $payment_methods[$method] : $method;
		return $payment_methods;
	}


    /**
     * @return array
     * @param $status
     */
    public static function getStatus($status = null) {
        $statuses = array (
            self::STATUS_PENDING       => __('Pending',    "ulisting"),
            self::STATUS_REFUNDED      => __('Refunded',   "ulisting"),
            self::STATUS_REVERSED      => __('Reversed',   "ulisting"),
            self::STATUS_COMPLETED     => __('Completed',  "ulisting"),
            self::STATUS_SUCCEEDED     => __('Succeeded',  "ulisting"),
            self::STATUS_UNCAPTURED    => __('Uncaptured', "ulisting"),
        );
        return ($status) ? $statuses[$status] : $statuses;
    }

	/**
	 * @param $key
	 *
	 * @return array
	 */
	public function getMeta($key){
		return StmPaymentMeta::query()
		          ->where('payment_id', $this->id)
		          ->where('meta_key', $key)
		          ->findOne();
	}

	/**
	 * @param $key
	 * @param $value
	 *
	 * @return array|\uListing\Lib\PricingPlan\Classes\StmPaymentMeta
	 */
	public function setMeta($key, $value){
		$payment_meta = StmPaymentMeta::query()
	                     ->where('payment_id', $this->id)
	                     ->where('meta_key', $key)
	                     ->findOne();
		if(!$payment_meta)
			$payment_meta = new StmPaymentMeta();

		$payment_meta->payment_id = $this->id;
		$payment_meta->meta_key   = $key;
		$payment_meta->meta_value = (is_array($value)) ? json_encode($value) : $value;
		$payment_meta->save();
		return $payment_meta;
	}

	/**
	 * @param $key
	 *
	 * @return mixed
	 */
	public function getDate($key) {
		$data = $this->getMeta('data_completed');
		if(!$data)
			$data = $this->getMeta('data_pending');

		if($data AND $this->payment_method == self::PAYMENT_METHOD_PAYPAL) {
			$data = json_decode($data->meta_value);

			if($key == 'currency' AND isset($data->resource->amount->currency))
				return $data->resource->amount->currency;
		}

		if($data AND $this->payment_method == self::PAYMENT_METHOD_CARD) {
			$data = json_decode($data->meta_value);

			if($key == 'currency' AND isset($data->data->object->currency))
				return $data->data->object->currency;
		}

		return null;
	}

    /**
     * @param $user_plan_id
     * @return mixed
     */
	public static function getByUserPlanId ( $user_plan_id ) {
	    return static::find_one_by('user_plan_id', (int)$user_plan_id);
    }

	/**
	 * @param null $limit
	 * @param null $page
	 * @param array $filter
	 * @param null $only_count
	 *
	 * @return array|int|null|object
	 */
	public static function getPayments($limit = null, $offset = null, $filter = array(), $only_count = null) {
		$payments = StmPayment::query()
		                      ->select(' payment.* ')
		                      ->asTable('payment');

		foreach ($filter as $key => $val) {

			if($key == 'user_id') {
				$payments->join(" LEFT JOIN ".StmUserPlan::get_table()." as user_plan on (user_plan.id = payment.user_plan_id) ");
				$payments->where('user_plan.user_id',$val);
				continue;
			}

			if(is_array($val))
				$payments->where_in("payment.".$key, $val);
			else
				$payments->where("payment.".$key, $val);
		}

		if($limit != null)
			$payments->limit($limit);

		if($offset != null)
			$payments->offset($offset);

		if(!$only_count)
			$payments->group_by('payment.id');

		$payments->sort_by(" id ");
		$payments->order(" DESC ");

		return $payments->find($only_count);
	}
}
