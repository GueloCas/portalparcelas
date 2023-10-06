<?php
namespace uListing\Lib\PricingPlan\Classes;

use uListing\Classes\Vendor\StmBaseModel;

class StmUserPlanMeta extends StmBaseModel{

	protected $fillable = [
		'id',
		'user_plan_id',
		'meta_key',
		'meta_value',
	];

	public $id;
	public $invoice_id;
	public $meta_key;
	public $meta_value;

	public static function get_primary_key() {
		return 'id';
	}

	public static function get_table() {
		global $wpdb;
		return $wpdb->prefix . 'ulisting_user_plan_meta';
	}

	public static function get_searchable_fields() {
		return [
			'id',
			'user_plan_id',
			'meta_key',
			'meta_value',
		];
	}
}
