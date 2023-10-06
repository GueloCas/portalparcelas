<?php

namespace uListing\Classes;

use uListing\Classes\Vendor\StmBaseModel;
use uListing\Classes\StmListingType;

class StmListingTypeRelationships extends StmBaseModel{

	protected $fillable = [
		'id',
		'listing_type_id',
		'listing_id'
	];

	public $id;
	public $listing_type_id;
	public $listing_id;

	public static function get_primary_key()
	{
		return 'id';
	}

	public static function get_table()
	{
		global $wpdb;
		return $wpdb->prefix . 'ulisting_listing_type_relationships';
	}

	public static function get_searchable_fields()
	{
		return [
			'id',
			'listing_type_id',
			'listing_id'
		];
	}

	public function getType() {
		return StmListingType::find_one_by('ID', $this->listing_type_id);
	}
}
