<?php
use uListing\Classes\StmAjaxAction;
use uListing\Lib\PricingPlan\Classes\StmUserPlan;
use uListing\Lib\PricingPlan\Classes\StmPricingPlans;

function stm_listing_pricing_plan_enqueue_scripts_styles() {
	$v = ULISTING_VERSION;
	wp_enqueue_script('stm-pricing-plan', ULISTING_PATH_LIB_PRICING_PLAN_URL . '/assets/js/frontend/stm-pricing-plan.js', array('vue'), $v, true);
	wp_enqueue_script('user-plan-detail', ULISTING_PATH_LIB_PRICING_PLAN_URL . '/assets/js/frontend/user-plan-detail.js', array('vue'), $v, true);
}

function stm_listing_pricing_plan_admin_enqueue_scripts_styles()
{
	$v = ULISTING_VERSION;
	wp_enqueue_script('stm-pricing-plan', ULISTING_PATH_LIB_PRICING_PLAN_URL . '/assets/js/frontend/stm-pricing-plan.js', array('vue.js'), $v, true);
	wp_enqueue_script('stm-listing-pricing-plan-form', ULISTING_PATH_LIB_PRICING_PLAN_URL . '/assets/js/admin/stm-listing-pricing-plan-form.js', array('vue.js'), $v, true);
	wp_enqueue_script('stm-user-plan-form', ULISTING_PATH_LIB_PRICING_PLAN_URL . '/assets/js/admin/stm-user-plan-form.js', array('vue.js'), $v, true);
}
add_action('wp_enqueue_scripts', 'stm_listing_pricing_plan_enqueue_scripts_styles');
add_action('admin_enqueue_scripts', 'stm_listing_pricing_plan_admin_enqueue_scripts_styles');

// Register post type Pricing Plans
function stm_plan_init() {
	$labels = array(
		'name' => esc_html__('Pricing Plans', "ulisting"),
		'singular_name' => esc_html__('stm_pricing_plans', "ulisting"),
		'add_new' => esc_html__('Add New Plan', "ulisting"),
		'add_new_item' => esc_html__('Add New Plan', "ulisting"),
		'edit_item' => esc_html__('Edit Plan', "ulisting"),
		'new_item' => esc_html__('New Plan', "ulisting"),
		'view_item' => esc_html__('View Plan', "ulisting"),
		'search_items' => esc_html__('Search Plan', "ulisting"),
		'not_found' =>  esc_html__('No Plan found', "ulisting"),
		'not_found_in_trash' => esc_html__('No Plan found in Trash', "ulisting"),
		'parent_item_colon' => ''
	);
	$args = array(
		'labels' => $labels,
		'public' => true,
		'publicly_queryable' => false,
		'show_ui' => true,
		'query_var' => 'stm_pricing_plans',
		'rewrite'   => array( 'slug' => 'stm_plan' ),
		'capability_type' => 'post',
		'has_archive' => false,
		'hierarchical' => false,
		'menu_position' => 4,
		'show_in_rest' => true,
		'supports' => array(''),
		'menu_icon' => 'dashicons-list-view',
	);

	register_post_type( 'stm_pricing_plans', $args );
}

add_action('init', 'stm_plan_init',0);
StmPricingPlans::init();
StmUserPlan::init();

