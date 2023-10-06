<?php
use uListing\Classes\StmListingTemplate;
use uListing\Classes\StmListing;
use uListing\Classes\StmListingType;

function toplips_themer_ulisting_latest($params) {
	global $wpdb;
	$listing_type = null;
		if (isset($params["listing_type_id"])) {
			$listing_type = StmListingType::find_one($params["listing_type_id"]);
			if (!($listing_type = StmListingType::find_one($params["listing_type_id"]))) {
				 $args = array(
					  'meta_query' => array(
							array(
								 'key' => "ulisting_import_id",
								 'value' => $params["listing_type_id"]
							)
					  ),
					  'post_status' => 'any',
					  'post_type' => 'listing_type',
					  'posts_per_page' => '1'
				 );
				 $posts = get_posts($args);
				 if (isset($posts[0]) AND isset($posts[0]->ID) AND $listing_type = StmListingType::find_one($posts[0]->ID)) {
					  $listing_type = $listing_type->ID;
				 }
			} else{
				$listing_type = $listing_type->ID;
			}
		}

		$view_type     = "grid";
		$item_class    = "ulisting-latest-item";
		$item_template = isset($params['item_template']) ? $params['item_template'] : '';
		$limit         = (isset($params['limit'])) ? $params['limit'] : 6;
		$page = (get_query_var(stm_page_endpoint())) ? get_query_var(stm_page_endpoint()) : 0;

		$listings      = StmListing::get_listing([
		  'listing_type' => $listing_type,
		  "sort"         => $wpdb->prefix . "posts.post_date",
		  "order_type"   => "DESC"
		],$limit, 1
	 );
	 return StmListingTemplate::load_template(
		  'listing/ulisting-latest',
		  [
				"listings"      => $listings['models'],
				"view_type"     => $view_type,
				"item_class"    => $item_class,
				"item_template"    => $item_template,
		  ]
	 );
}

add_shortcode('ulisting-latest', 'toplips_themer_ulisting_latest');



function toplips_themer_ulisting_popular($params) {
	$listing_type = null;
		if (isset($params["listing_type_id"])) {
			$listing_type = StmListingType::find_one($params["listing_type_id"]);
			if (!($listing_type = StmListingType::find_one($params["listing_type_id"]))) {
				 $args = array(
					  'meta_query' => array(
							array(
								 'key' => "ulisting_import_id",
								 'value' => $params["listing_type_id"]
							)
					  ),
					  'post_status' => 'any',
					  'post_type' => 'listing_type',
					  'posts_per_page' => '1'
				 );
				 $posts = get_posts($args);
				 if (isset($posts[0]) AND isset($posts[0]->ID) AND $listing_type = StmListingType::find_one($posts[0]->ID)) {
					  $listing_type = $listing_type->ID;
				 }
			} else{
				$listing_type = $listing_type->ID;
			}
		}
	 $view_type     = "grid";
	 $item_class    = "ulisting-popular-item";
	 $item_template = isset($params['item_template']) ? $params['item_template'] : '';
	 $limit         = $params["limit"];
	 $listings      = StmListing::get_listing([
		  'listing_type' => $listing_type,
		  'meta'         => [
				[
					 'key'        => '_ulisting_post_views',
					 'sort'       => 1,
					 'order_type' => "DESC"
				]
		  ],
	 ],
		  $limit,
		  1
	 );
	 return StmListingTemplate::load_template(
		  'listing/ulisting-popular',
		  [
				"listings"      => $listings['models'],
				"view_type"     => $view_type,
				"item_class"    => $item_class,
				"item_template" => $item_template,
		  ]
	 );
}

add_shortcode('ulisting-popular', 'toplips_themer_ulisting_popular');
