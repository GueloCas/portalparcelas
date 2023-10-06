<?php
namespace uListing\Lib\PricingPlan\Classes;

use uListing\Classes\Vendor\StmBaseModel;

class StmPaymentMeta extends StmBaseModel{

	protected $fillable = [
		'id',
		'payment_id',
		'meta_key',
		'meta_value',
	];

	public $id;
	public $payment_id;
	public $meta_key;
	public $meta_value;

	public static function get_primary_key() {
		return 'id';
	}

	public static function get_table() {
		global $wpdb;
		return $wpdb->prefix . 'ulisting_payment_meta';
	}

	public static function get_searchable_fields() {
		return [
			'id',
			'payment_id',
			'meta_key',
			'meta_value',
		];
	}
}
