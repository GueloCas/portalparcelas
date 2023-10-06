<?php
namespace uListing\Classes;

use uListing\Classes\Vendor\StmBaseModel;

class StmListingCategory extends StmBaseModel{

	public $term_id;
	public $name;
	public $slug;
	public $term_group;

	protected $fillable = [
		'term_id',
		'name',
		'slug',
		'term_group'
	];

	public static function get_primary_key()
	{
		return 'term_id';
	}

	public static function get_table()
	{
		global $wpdb;
		return $wpdb->prefix . 'terms';
	}

	public static function get_searchable_fields()
	{
		return [
			'term_id',
			'name',
			'slug',
			'term_group'
		];
	}

	public static function init(){
		add_action( 'listing-category_add_form_fields', [self::class, 'listing_category_field'] , 10, 2 );
		add_action( 'listing-category_edit_form_fields', [self::class, 'listing_category_taxonomy_edit_field'], 10, 2 );

		add_action( 'create_listing-category', [self::class, 'listing_category_save'], 10, 2 );
		add_action( 'edited_listing-category', [self::class, 'listing_category_save'], 10, 2 );
	}

	public static  function listing_category_field() {
		ulisting_render_template(ULISTING_ADMIN_PATH . '/views/listing-category/add_fields.php', null, true);
	}

	/**
	 * @param $term
	 */
	public static  function listing_category_taxonomy_edit_field($term) {
		ulisting_render_template(ULISTING_ADMIN_PATH . '/views/listing-category/edit_fields.php', ['term' => $term], true);
	}

	/**
	 * @param $term_id
	 */
	public static  function listing_category_save( $term_id ) {
		if ( isset( $_POST['taxonomy'] ) AND  sanitize_text_field($_POST['taxonomy']) == 'listing-category' ) {
			if(isset($_POST['StmListingCategory']['listing_type'])) {
				update_term_meta( $term_id, 'stm_listing_category_type', ulisting_sanitize_array($_POST['StmListingCategory']['listing_type']) );
			}else
				delete_term_meta($term_id, 'stm_listing_category_type');
		}
	}

	/**
	 * @return array
	 */
	public static function getListDataArray(){
		$items = [];
		$categories = get_categories( array(
			'taxonomy' => 'listing-category',
			'hide_empty'   => 0,
		));
		foreach ($categories as $categorie) {
			$items[] = [
				'id' => $categorie->term_id,
				'name' => $categorie->name,
			];
		}
		return $items;
	}

	/**
	 * @return array
	 */
	public function getListingTypes(){
		$listing_types = get_term_meta($this->term_id, "stm_listing_category_type");
		return (isset($listing_types[0])) ? $listing_types[0] : array();
	}
}
