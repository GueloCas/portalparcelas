<?php

global $stm_query;
global $wp_router;
$wp_router = new UlistingRouter;

add_action('init', 'stm_listing_type_init',0);
add_action('init', 'stm_listing_category_init',0);
add_action('init', 'stm_listing_region_init',0);
add_action('init', 'stm_listing_init',0);
add_action('init', [\uListing\Classes\StmListing::class, 'stm_listing_custom_post_status'] );

/**
 * add ajax action
 */
add_action('init', function (){
	\uListing\Classes\StmAjaxAction::init();
}, 9999);

/**
 * add action
 */

\uListing\Classes\UlistingSanitize::init();
\uListing\Classes\StmCron::init();
\uListing\Classes\StmListing::init();
\uListing\Classes\StmUser::init_user();
\uListing\Classes\StmListingType::init();
\uListing\Classes\StmListingRegion::init();
\uListing\Classes\StmListingCategory::init();
\uListing\Classes\StmListingSettings::init();
\uListing\Classes\StmListingAttribute::init();
\uListing\Classes\StmListingAttributeOption::init();
\uListing\Classes\StmComment::init();
\uListing\Classes\UlistingUserRole::init();
\uListing\Classes\UlistingSearch::init();

if(is_admin()){
	add_action('init', function (){
        \uListing\Classes\StmUpdates::init();
        if (empty(get_option("permalink_structure")))
			\uListing\Classes\Notices::add_admin_notices(\uListing\Classes\Notices::TYPE_ADMIN_NOTICES_ERROR, __("uListing plugin requires Permalink Structure rather than “Plain”. We  recommend to use “Post name” Permalink Structure.", "ulisting"));
	});

	\uListing\Admin\Classes\StmAdminMenu::init();
	\uListing\Admin\Classes\StmAdminNotice::init();
	add_action( 'plugins_loaded', function (){
		if (!get_option("ulisting_demo_import_redirect") && apply_filters('show_uListing_demo_import', true)) {
			add_option("ulisting_demo_import_redirect", 1);
			wp_redirect(admin_url( 'admin.php?page=demo-import-page'));
			exit();
		}
	});
}
// Register taxonomy Listings Regions
function stm_listing_region_init() {
	register_taxonomy( 'listing-region', 'listing', array(
			'labels'        => array(
				'name'          => esc_html__("Regions", "ulisting"),
				'add_new_item'  => esc_html__("Add New Region", "ulisting"),
				'new_item_name' => esc_html__("New Region", "ulisting"),
				'add_new_item' => esc_html__("Add New Region", "ulisting"),
				'all_items' => esc_html__("Regions", "ulisting"),
				'archives' => esc_html__("Regions", "ulisting"),
				'back_to_items' => esc_html__("&larr; Back to Regions", "ulisting"),
				'edit_item' => esc_html__("Edit Region", "ulisting"),
				'items_list' => esc_html__("Regions list", "ulisting"),
				'items_list_navigation' => esc_html__("Regions list navigation", "ulisting"),
				'menu_name' => esc_html__("Regions", "ulisting"),
				'most_used' => esc_html__("Most Used", "ulisting"),
				'name' => esc_html__("Regions", "ulisting"),
				'name_admin_bar' => esc_html__("Regions", "ulisting"),
				'new_item_name' => esc_html__("New Region", "ulisting"),
				'no_terms' => esc_html__("No regions", "ulisting"),
				'not_found' => esc_html__("No regions found.", "ulisting"),
				'parent_item' => esc_html__("Parent Region", "ulisting"),
				'parent_item_colon' => esc_html__("Parent Region:", "ulisting"),
//				'search_items' => esc_html__("Search Regions", "ulisting"),
				'singular_name' => esc_html__("Regions", "ulisting"),
				'update_item' => esc_html__("Update Region", "ulisting"),
				'view_item' => esc_html__("View Region", "ulisting"),
			),
			'show_ui'       => true,
			'show_tagcloud' => false,
			'hierarchical'  => true,
			'rewrite'       => array( 'slug' => 'listing-region' ),
			'query_var'     => true,
			'public'        => true,
			'show_in_rest'  => true,
		) );
}

// Register taxonomy Listings Category
function stm_listing_category_init() {
	register_taxonomy(
		'listing-category',
		'listing',
		array(
			'labels' => array(
				'name' => esc_html__("Categories", "ulisting"),
				'add_new_item' => esc_html__("Add New Category", "ulisting"),
				'new_item_name' => esc_html__("New Category", "ulisting")
			),
			'show_ui' => true,
			'show_tagcloud' => false,
			'hierarchical' => true,
			'rewrite' => array( 'slug' => 'listing-category' ),
			'query_var' => true,
			'public' => true,
			'show_in_rest' => true,
		)
	);

	register_taxonomy(
		'listing-attribute-options',
		'listing',
		array(
			'labels' => array(
				'name' => esc_html__("Listing custom field options", "ulisting"),
				'add_new_item' => esc_html__("Add New listing custom field options", "ulisting"),
				'new_item_name' => esc_html__("New listing custom field options", "ulisting")
			),
			'show_ui' => true,
			'show_tagcloud' => false,
			'show_in_menu' => false,
			'hierarchical' => false,
			'rewrite' => array( 'slug' => 'listing-attribute-options' ),
			'query_var' => true,
			'public' => false,
			'show_in_rest' => false,
		)
	);
}

// Register post type Listing Type
function stm_listing_type_init() {

	$labels = array(
		'name' => esc_html__("Listing Types", "ulisting"),
		'singular_name' => esc_html__("Listing Types", "ulisting"),
		'add_new' => esc_html__("Add New", "ulisting"),
		'add_new_item' => esc_html__("Add New List", "ulisting"),
		'edit_item' => esc_html__("Edit List", "ulisting"),
		'new_item' => esc_html__("New Listing", "ulisting"),
		'view_item' => esc_html__("View List", "ulisting"),
		'search_items' => esc_html__("Search Listing", "ulisting"),
		'not_found' =>  esc_html__("No List found", "ulisting"),
		'not_found_in_trash' => esc_html__("No List found in Trash", "ulisting"),
		'parent_item_colon' => ''
	);

	$args = array(
		'labels' => $labels,
		'public' => true,
		'publicly_queryable' => false,
		'show_ui' => true,
		'query_var' => 'listing_type',
		'rewrite'   => array( 'slug' => 'listing_type' ),
		'capability_type' => 'post',
		'has_archive' => false,
		'hierarchical' => false,
		'menu_position' => 5,
		'show_in_rest' => true,
		'supports' => array( ''),
		'menu_icon' => 'dashicons-index-card',
	);

	register_post_type( 'listing_type', $args );
}

// Register post type Listing
function stm_listing_init() {

	$labels = array(
		'name' => esc_html__("Listings", "ulisting"),
		'singular_name' => esc_html__("Listing", "ulisting"),
		'add_new' => esc_html__("Add New", "ulisting"),
		'add_new_item' => esc_html__("Add New List", "ulisting"),
		'edit_item' => esc_html__("Edit List", "ulisting"),
		'new_item' => esc_html__("New Listing", "ulisting"),
		'view_item' => esc_html__("View List", "ulisting"),
		'search_items' => esc_html__("Search Listing", "ulisting"),
		'not_found' =>  esc_html__("No List found", "ulisting"),
		'not_found_in_trash' => esc_html__("No List found in Trash", "ulisting"),
		'parent_item_colon' => ''
	);

	$args = array(
		'labels' => $labels,
		'public' => true,
		'publicly_queryable' => true,
		'show_ui' => true,
		'query_var' => 'listing',
		'rewrite'   => array( 'slug' => 'listing' ),
		'capability_type' => 'post',
		'has_archive' => false,
		'hierarchical' => false,
		'menu_position' => 6,
		'show_in_rest' => true,
		'supports' =>  array( ''),
		'menu_icon' => 'dashicons-location',
	);
	register_post_type( 'listing', $args );
}

add_filter( 'taxonomy_parent_dropdown_args', 'limit_listing_region_taxonomy', 10, 2 );

function limit_listing_region_taxonomy( $args, $taxonomy) {
	if ( 'listing-region' != $taxonomy ) return $args; // no change
	$args['depth'] = '1';
	return $args;
}
