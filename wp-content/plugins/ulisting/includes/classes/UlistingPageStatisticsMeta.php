<?php

namespace uListing\Classes;

use uListing\Classes\Vendor\StmBaseModel;

class UlistingPageStatisticsMeta extends StmBaseModel {

	public $id;
	public $page_statistics_id;
	public $meta_key;
	public $meta_value;

	protected $fillable = [
		'id',
		'page_statistics_id',
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
		return $wpdb->prefix . 'ulisting_page_statistics_meta';
	}

	public static function get_searchable_fields()
	{
		return [
			'id',
			'page_statistics_id',
			'meta_key',
			'meta_value',
		];
	}
}