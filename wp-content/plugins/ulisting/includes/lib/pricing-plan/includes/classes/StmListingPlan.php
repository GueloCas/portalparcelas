<?php
namespace uListing\Lib\PricingPlan\Classes;

use uListing\Classes\StmListing;
use uListing\Classes\StmListingAttributeOption;
use uListing\Classes\StmListingAttributeRelationships;
use uListing\Classes\Vendor\StmBaseModel;
use uListing\Lib\PricingPlan\Classes\StmUserPlan;

class StmListingPlan extends StmBaseModel {

	protected $fillable = [
		'id',
		'listing_id',
		'user_plan_id',
		'type',
		'created_date',
		'expired_date'
	];

	public $id;
	public $type;
	public $listing_id;
	public $user_plan_id;
	public $created_date;
	public $expired_date;

	/**
	 * @return string
	 */
	public static function get_primary_key() {
		return 'id';
	}

	/**
	 * @return string
	 */
	public static function get_table() {
		global $wpdb;
		return $wpdb->prefix . 'ulisting_listing_plan';
	}

	/**
	 * @return array
	 */
	public static function get_searchable_fields() {
		return [
			'id',
			'listing_id',
			'user_plan_id',
			'type',
			'created_date',
			'expired_date',
		];
	}

	/**
	 * @return false|StmBaseModel
	 */
	public function getUserPlan() {
		return StmUserPlan::find_one($this->user_plan_id);
	}

	public static function delete_expired_listing_plan() {

		// delete feature attribute listing
		StmListingAttributeRelationships::query()
		->select(' listing_attribute ')
		->asTable('listing_attribute')
		->join(' left join '.StmListingPlan::get_table().' as listing_plan on (listing_plan.`listing_id` = listing_attribute.`listing_id`)')
		->where("listing_attribute.`attribute`", "feature")
		->where("listing_plan.`type`", "feature")
		->where_raw('listing_plan.`expired_date` != "" AND listing_plan.`expired_date` <= "'.date('Y-m-d h:i:s').'" ')
		->delete();

		// update listing status draft
	    StmListing::query()
		->asTable('listing')
		->join(' left join '.StmListingPlan::get_table().' as listing_plan on (listing_plan.`listing_id` = listing.ID)')
		->where("listing_plan.`type`", "limit_count")
		->where_raw('listing_plan.`expired_date` != "" AND listing_plan.`expired_date` <= "'.date('Y-m-d h:i:s').'" ')
		->update(array("post_status" => StmListing::STATUS_DRAFT ));

	    // delete expired listing plan
	    StmListingPlan::query()
		->where_not("expired_date", "")
		->where_raw('expired_date <= "'.date('Y-m-d h:i:s').'"')
		->delete();
	}
}
