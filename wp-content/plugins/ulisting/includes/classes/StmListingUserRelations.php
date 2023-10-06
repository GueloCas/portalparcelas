<?php
namespace uListing\Classes;

use uListing\Classes\Vendor\StmBaseModel;

class StmListingUserRelations extends StmBaseModel{

	const TYPE_NONE = 'none';
	const TYPE_FREE = 'free';
	const TYPE_PAID = 'paid';

	protected $fillable = [
		'id',
		'user_id',
		'listing_id',
		'type',
	];

	public $id;
	public $user_id;
	public $listing_id;
	public $type;

	public static function get_primary_key()
	{
		return 'id';
	}

	public static function get_table()
	{
		global $wpdb;
		return $wpdb->prefix . 'ulisting_listing_user_relations';
	}

	public static function get_searchable_fields()
	{
		return [
			'id',
			'user_id',
			'listing_id',
			'type',
		];
	}

	public static function init() {

	}

	public static function getTypeList() {
		return array(
			self::TYPE_FREE => esc_html__('Free', "ulisting"),
	        self::TYPE_PAID => esc_html__('Paid', "ulisting")
		);
	}
}
