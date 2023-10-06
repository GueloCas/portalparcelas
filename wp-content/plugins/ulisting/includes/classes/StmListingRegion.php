<?php
namespace uListing\Classes;

use uListing\Classes\Vendor\StmBaseModel;

class StmListingRegion extends StmBaseModel{

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
		add_action( 'listing-region_add_form_fields', [self::class, 'listing_region_field'] , 10, 2 );
		add_action( 'listing-region_edit_form_fields', [self::class, 'listing_region_taxonomy_edit_field'], 0, 2 );
		add_action( 'create_listing-region', [self::class, 'listing_region_save'], 10, 2 );
		add_action( 'edited_listing-region', [self::class, 'listing_region_save'], 10, 2 );
		add_shortcode("ulisting-region-list", [self::class, 'region_list_short_code']);
	}

	/**
	 * @param $params
	 *
	 * @return bool|string
	 */
	public static function region_list_short_code($params){
		global $wpdb;
		$regions      = [];
		$listing_type_id = 0;
		$listing_type = 0;

		if(isset($params["regions"]) AND !empty($params["regions"]))
			$regions = explode(",", $params["regions"]);

		if(isset($params["listing_type_id"]) AND !empty($params["listing_type_id"])){
			$listing_type_id = (int)sanitize_text_field($params["listing_type_id"]);
			if( ! ($listing_type = StmListingType::find_one($listing_type_id)) ) {
				$args = array(
					'meta_query'        => array(
						array(
							'key'       => "ulisting_import_id",
							'value'     => $listing_type_id
						)
					),
					'post_status'       => 'any',
					'post_type'         => 'listing_type',
					'posts_per_page'    => '1'
				);
				$posts = get_posts( $args );
				if(isset($posts[0]) AND isset($posts[0]->ID) AND $listing_type = StmListingType::find_one($posts[0]->ID)){
					$listing_type_id = $listing_type->ID;
				}
			}
		}

        $data = get_transient("ulisting_region_list_short_code");
		$models  = StmListingRegion::query()
		                           ->where_in("term_id", $regions)
		                           ->find();

		if( empty($data) || !is_array($data) ) {
			$_models  = StmListingRegion::query()
                        ->asTable("region")
                        ->select(" region.`term_id`, region.`name`, MAX(Convert( listing_attribute_relation.`value` , SIGNED)) as max_price, MIN(Convert( listing_attribute_relation.`value` , SIGNED)) as min_price , COUNT(listing.ID) as items_count \n")
                        ->join(" left join `".$wpdb->prefix."term_taxonomy` as taxonomy on taxonomy.`term_id` = region.`term_id` \n")
                        ->join(" left join `".$wpdb->prefix."term_relationships` as term_relation on term_relation.`term_taxonomy_id` = taxonomy.`term_taxonomy_id` \n")
                        ->join(" left join `".$wpdb->prefix."posts` as listing on listing.ID = term_relation.`object_id` AND listing.`post_type` = 'listing' \n")
                        ->join(" left join `".$wpdb->prefix."ulisting_listing_type_relationships` as listing_type_relation on listing_type_relation.`listing_id` = listing.ID \n")
                        ->join(" left join `".$wpdb->prefix."posts` as listing_type on listing_type.ID = listing_type_relation.`listing_type_id` AND listing_type.`post_type` = 'listing_type' \n")
                        ->join(" left join `".$wpdb->prefix."ulisting_listing_attribute_relationships` as listing_attribute_relation on listing_attribute_relation.`listing_id` = listing.ID AND listing_attribute_relation.`attribute` = 'price' \n")
                        ->where("taxonomy.`taxonomy`", "listing-region")
                        ->where("listing_type.ID", $listing_type_id)
                        ->where_in("region.`term_id`", $regions)
                        ->group_by("region.term_id")
                        ->find();

			foreach ($_models as $model){
				$data[$model->term_id] = [
					'items_count' => $model->items_count,
					'max_price' => $model->max_price,
					'min_price' => $model->min_price
				];
			}
			set_transient("ulisting_region_list_short_code", $data, 24 * HOUR_IN_SECONDS);
		}
		return StmListingTemplate::load_template( 'region/region-list-short-code', [ "models" => $models, "data" => $data,  "params" => $params, "listing_type" => $listing_type ]);
	}

	public static  function listing_region_field() {
		ulisting_render_template(ULISTING_ADMIN_PATH . '/views/listing-region/add_fields.php', null, true);
	}

	/**
	 * @param $term
	 */
	public static  function listing_region_taxonomy_edit_field($term) {
		ulisting_render_template(ULISTING_ADMIN_PATH . '/views/listing-region/edit_fields.php', ['term' => $term], true);
	}

	/**
	 * @param $term_id
	 */
	public static  function listing_region_save( $term_id ) {

		if ( isset( $_POST['taxonomy'] ) AND  sanitize_text_field($_POST['taxonomy']) == 'listing-region' ) {

			if(isset($_POST['StmListingRegion']['icon'])) {
				update_term_meta( $term_id, 'listing-region-icon', sanitize_text_field($_POST['StmListingRegion']['icon']) );
				delete_term_meta($term_id, 'listing-region-thumbnail');
			}else
				delete_term_meta($term_id, 'listing-region-thumbnail');

			if(isset($_POST['StmListingRegion']['thumbnail_id'])) {
				update_term_meta( $term_id, 'listing-region-thumbnail', sanitize_key($_POST['StmListingRegion']['thumbnail_id']) );
				delete_term_meta($term_id, 'listing-region-icon');
			}else
				delete_term_meta($term_id, 'listing-region-thumbnail');

			if(isset($_POST['StmListingRegion']['polygon']) AND !empty($_POST['StmListingRegion']['polygon'])) {
				update_term_meta( $term_id, 'stm_listing_region_polygon', sanitize_text_field($_POST['StmListingRegion']['polygon']));
			}else
				delete_term_meta($term_id, 'stm_listing_region_polygon');

			if(isset($_POST['StmListingRegion']['static_map_url']) AND !empty($_POST['StmListingRegion']['static_map_url']) AND $region = StmListingRegion::find_one($term_id) ) {
				$region->generation_static_map( apply_filters('ulisting_sanitize_map_url', $_POST['StmListingRegion']['static_map_url']) );
			}
		}
	}

	/**
	 * @return array
	 */
	public static function getListDataArray(){
		$items = [];
		$regions = get_categories( array(
			'taxonomy' => 'listing-region',
			'hide_empty'   => 0,
			'parent'   => 0
		));

		foreach ($regions as $region) {
			$items[] = [
				'id' => $region->term_id,
				'name' => $region->name,
			];

			$children = get_terms( array(
				'taxonomy' => 'listing-region',
				'hide_empty' => false,
				'child_of' => $region->term_id
			));

			foreach ($children as $child){
				$items[] = [
					'id' => $child->term_id,
					'name'  => ' - '.$child->name,
				];
			}
		}
		return $items;
	}

	/**
	 * @return array
	 */
	public function getListingTypes(){
		$listing_types = get_term_meta($this->term_id, "stm_listing_region_type");
		return (isset($listing_types[0])) ? $listing_types[0] : array();
	}

	/**
	 * @return array|bool
	 */
	public function getThumbnail() {
		$attachment_id = get_term_meta($this->term_id, 'listing-region-thumbnail', true);
		if( $attachment = wp_get_attachment_url($attachment_id) ){
			return ['id' => $attachment_id, 'url' => $attachment];
		}
		return false;
	}

	/**
	 * @return array|bool
	 */
	public function get_static_map() {
		$attachment_id = get_term_meta($this->term_id, 'ulisting-region-static-map', true);
		if( $attachment = wp_get_attachment_url($attachment_id) ){
			return ['id' => $attachment_id, 'url' => $attachment];
		}
		return false;
	}

	/**
	 * @param string $size
	 *
	 * @return array|false
	 */
	public function get_thumbnail_image($size = "thumbnail") {
		$thumbnail = $this->getThumbnail();
		if(isset($thumbnail['id'])){
			$thumbnail = wp_get_attachment_image_src($thumbnail['id'], $size);
			if(isset($thumbnail[0]))
				return $thumbnail[0];
		}
		return ulisting_get_placeholder_image_url();
	}

	/**
	 * @param string $size
	 *
	 * @return string
	 */
	public function get_static_map_image($size = "thumbnail") {
		$static_map = $this->get_static_map();
		if(isset($static_map['id'])){
			$static_map = wp_get_attachment_image_src($static_map['id'], $size);
			if(isset($static_map[0]))
				return $static_map[0];
		}
		return $this->get_thumbnail_image($size);
	}

	/**
	 * @return bool
	 */
	public function get_icon() {
		$icon = get_term_meta($this->term_id, 'listing-region-icon');
		if(isset($icon[0]))
			return $icon[0];
		return false;
	}

	/**
	 * @param $url
	 *
	 * @return bool
	 */
	public function generation_static_map($url){
		$url = wp_unslash($url);
		if(empty($url)){
			$static_map = $this->get_static_map();
			if(isset($static_map['id'])){
				wp_delete_attachment($static_map['id']);
			}
			return false;
		}

		$file = file_get_contents( $url );
		$filename = 'region_static_map_'.$this->term_id.'_'.rand(10, 9999).'_'.time().'.jpg';
		$upload_file = wp_upload_bits($filename, null, $file);
		$filename = $upload_file['file'];
		$wp_filetype = wp_check_filetype($filename, null );
		$attachment = array(
			'post_mime_type' => $wp_filetype['type'],
			'post_title' => "Region static map",
			'post_content' => '',
			'post_status' => 'inherit'
		);
		$attachment_id = wp_insert_attachment( $attachment, $filename );
		if (!is_wp_error($attachment_id)) {
			require_once(ABSPATH . "wp-admin" . '/includes/image.php');
			$attachment_data = wp_generate_attachment_metadata( $attachment_id, $filename );
			wp_update_attachment_metadata( $attachment_id,  apply_filters('uListing-sanitize-data', $attachment_data) );

			//delete old thumbnail if is static map
			$static_map = $this->get_static_map();
			if(isset($static_map['id'])){
				wp_delete_attachment($static_map['id']);
			}

			update_term_meta( $this->term_id, 'ulisting-region-static-map', apply_filters('uListing-sanitize-data', $attachment_id));
			add_post_meta($attachment_id, "ulisting_region_static_map", 1);
			return true;
		}
	}
}
