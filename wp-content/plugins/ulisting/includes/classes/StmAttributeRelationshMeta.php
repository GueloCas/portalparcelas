<?php
namespace uListing\Classes;

use uListing\Classes\Vendor\StmBaseModel;

class StmAttributeRelationshMeta extends StmBaseModel{

	public $id;
	public $relations_id;
	public $meta_key;
	public $meta_value;

	protected $fillable = [
		'id',
		'relations_id',
		'meta_key',
		'meta_value',
	];

	public static function get_primary_key()
	{
		return 'id';
	}

	public static function get_table()
	{
		global $wpdb;
		return $wpdb->prefix . 'ulisting_attribute_relationsh_meta';
	}

	public static function get_searchable_fields()
	{
		return [
			'id',
			'relations_id',
			'meta_key',
			'meta_value',
		];
	}
}
