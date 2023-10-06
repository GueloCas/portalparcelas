<?php

namespace uListing\Classes;

use uListing\Classes\Vendor\StmBaseModel;
use uListing\Classes\StmListingAttribute;
use uListing\Classes\StmAttributeRelationshMeta;

class StmListingAttributeRelationships extends StmBaseModel{

	protected $fillable = [
		'id',
		'listing_id',
		'attribute',
		'value',
		'sort'
	];

	public $id;
	public $listing_id;
	public $attribute;
	public $value;
	public $sort;

	public static function get_primary_key()
	{
		return 'id';
	}

	public static function get_table()
	{
		global $wpdb;
		return $wpdb->prefix . 'ulisting_listing_attribute_relationships';
	}

	public static function get_searchable_fields()
	{
		return [
			'id',
			'listing_id',
			'attribute',
			'value',
			'sort'
		];
	}

	public function getAttribute() {
		return StmListingAttribute::query()->where('name', $this->attribute)->findOne();
	}

	public function getOption() {
		return StmListingAttributeOption::query()->where('term_id',$this->value)->findOne();
	}

	public function update_meta($key, $value) {
		$stmAttributeRelationshMeta = StmAttributeRelationshMeta::query()
		                                                        ->where('relations_id', $this->id)
		                                                        ->where('meta_key', $key)
		                                                       ->findOne();
		if(!$stmAttributeRelationshMeta) {
			$stmAttributeRelationshMeta = new StmAttributeRelationshMeta();
			$stmAttributeRelationshMeta->relations_id = $this->id;
			$stmAttributeRelationshMeta->meta_key = $key;
		}
		$stmAttributeRelationshMeta->meta_value = $value;
		$stmAttributeRelationshMeta->save();
	}

	public function get_meta(){
		$meta = [];
		$models = StmAttributeRelationshMeta::query()
                                            ->where('relations_id', $this->id)
                                            ->find();
		foreach ($models as $model) {
			$meta[$model->meta_key] = $model->meta_value;
		}
		return $meta;
	}

}