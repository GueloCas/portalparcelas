<?php

namespace uListing\Classes;

use uListing\Classes\Vendor\StmBaseModel;

class StmAttributeTermRelationships extends StmBaseModel{

	public $id;
	public $attribute_id;
	public $term_id;

	protected $fillable = [
		'id',
		'attribute_id',
		'term_id'
	];

	public static function get_primary_key()
	{
		return 'id';
	}

	public static function get_table()
	{
		global $wpdb;
		return $wpdb->prefix . 'ulisting_attribute_term_relationships';
	}

	public function getAttribute() {
		return StmListingAttribute::find_one($this->attribute_id);
	}

	public static function get_searchable_fields()
	{
		return [
			'id',
			'attribute_id',
			'term_id'
		];
	}

	public function getTerm(){
		return get_term($this->term_id);
	}
}
