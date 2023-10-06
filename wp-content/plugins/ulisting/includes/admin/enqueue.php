<?php
if (!defined('ABSPATH')) {
	exit; // Exit if accessed directly
}

function stm_listing_admin_enqueue()
{
	$v = ULISTING_VERSION;
    $main_pages = [
        'demo-import-page',
        'settings-page',
        'extensions-page',
        'saved-searches-page',
        'status-page',
    ];

    if ( isset($_GET['page']) || 'listing' == get_post_type() ) {
        wp_enqueue_script('tinymce_js', includes_url( 'js/tinymce/' ) . 'wp-tinymce.php', array( 'jquery' ), false, true );
        wp_enqueue_script('tinymce', ULISTING_URL . '/assets/js/vue-tinymce-2/tinymce.min.js', array('vue.js'), $v);
        wp_enqueue_script('vue-easy-tinymce', ULISTING_URL . '/assets/js/vue-tinymce-2/vue-easy-tinymce.min.js', array('vue.js'), $v);

        if ( get_post_type() === 'listing' ) {
            ulisting_enqueue_scripts_styles($v);
        }

        if ( isset($_GET['page']) && in_array($_GET['page'], $main_pages) ) {
            wp_enqueue_style('bootstrap', ULISTING_URL . '/assets/css/frontend/bootstrap.min.css', array());
            wp_enqueue_style('ulisting-settings', ULISTING_URL . '/assets/css/admin/settings.css', array());
        }
    }

    wp_enqueue_style('uListing-global', ULISTING_URL . '/assets/css/admin/global.css', []);
    wp_enqueue_script('uListing-helper', ULISTING_URL . '/assets/js/helper.js', array(), $v);
    wp_enqueue_script('bootstrap', ULISTING_URL . '/assets/js/bootstrap/bootstrap.js', array(), $v);
    wp_enqueue_style('stm-grid', ULISTING_URL . '/assets/css/stm-grid.css', array());
    wp_enqueue_style( 'jquery-ui-datepicker' );
    wp_enqueue_script('stm-listing-jquery-clock-timepicker', ULISTING_URL . '/assets/js/jquery-clock-timepicker.min.js', array(), $v);
    wp_enqueue_script('vanilla-toast', ULISTING_URL . '/assets/js/vanilla-toast.min.js', array(), $v);
    wp_enqueue_style('ulisting-bootstrap', ULISTING_URL . '/assets/css/admin/bootstrap.css', array());
	wp_enqueue_style('ulisting-icon', ULISTING_URL . '/assets/css/admin/icon.css', array());

    wp_enqueue_style('uListing-admin-fonts', 'https://fonts.googleapis.com/css2?family=Kumbh+Sans:wght@400;700&display=swap');
    wp_enqueue_style('select2', ULISTING_URL . '/assets/css/select2.min.css', array());
	wp_enqueue_style('ulisting-admin', ULISTING_URL . '/assets/css/admin/main.css', array(), $v);


	wp_enqueue_script("jquery");
	wp_enqueue_style( 'jquery-ui' );
	wp_enqueue_script( 'jquery-ui-datepicker' );
    wp_enqueue_script( 'jquery-ui-autocomplete' );
    wp_enqueue_media();

	wp_enqueue_script('moment', ULISTING_URL . '/assets/js/moment.js', array(), $v);
    wp_enqueue_script('select2', ULISTING_URL . '/assets/js/select2.full.js', array(), $v);
    wp_enqueue_script('popper', ULISTING_URL . '/assets/js/popper.min.js', array(), $v);
    wp_enqueue_script('sortable', ULISTING_URL . '/assets/js/Sortable.min.js', array(), $v);

	/*----------------------- vue -----------------------*/
    wp_enqueue_script('vue.js', ULISTING_URL . '/assets/js/vue/vue.js', array(), $v);
    wp_enqueue_script('vue2-datepicker', ULISTING_URL . '/assets/js/vue/vue2-datepicker.js', array('vue.js'), $v);
    wp_add_inline_script('vue.js', "window.UlistingEventBus = new Vue();");
    wp_add_inline_script('vue.js', "window.EventBus = new Vue();");
    wp_enqueue_script('stm-listing-vuedraggable', ULISTING_URL . '/assets/js/vue/vuedraggable.min.js', array('vue.js'), $v);
	wp_enqueue_script('stm-listing-vue-google-maps', ULISTING_URL . '/assets/js/vue/vue-google-maps.js', array('vue.js'), $v);
    wp_enqueue_script('stm-google-map', ULISTING_URL . '/assets/js/frontend/stm-google-map.js', array('vue.js'), $v);
	wp_enqueue_script('vue-select', ULISTING_URL . '/assets/js/vue/vue-select.js', array('vue.js'), $v);
    wp_enqueue_script('vue-color', ULISTING_URL . '/assets/js/vue/vue-color.min.js', array('vue.js'), $v);

    wp_enqueue_script('stm-carousel', ULISTING_URL . '/assets/js/owl.carousel.min.js', array('vue.js'), $v, true);

	/**
	 * Custom Post Types
	 */
	$screen     = get_current_screen();
	$post_types = [
		'listing',
		'listing_type',
		'stm_pricing_plans'
	];

	$pages = [
	    'inventory-list',
	    'listing_attribute',
	    'listing_attribute_edit'
    ];

	$taxonomies = [
	    'listing-attribute-options'
    ];

    if (
        in_array( $screen->post_type, $post_types )
        || (isset($_GET['page']) && in_array($_GET['page'], $pages))
        || (isset($_GET['taxonomy']) && in_array($_GET['taxonomy'], $taxonomies))
    ) {

        wp_enqueue_style('custom-posts', ULISTING_URL . '/assets/css/admin/custom-posts.css', []);
        wp_enqueue_style('user-plan', ULISTING_URL . '/assets/css/admin/user-plan.css', []);
        wp_enqueue_style('pricing-plan', ULISTING_URL . '/assets/css/admin/pricing-plan.css', []);
        wp_enqueue_style('font-awesome-min', ULISTING_URL . '/assets/css/font-awesome.min.css', []);
        wp_enqueue_style('bootstrap', ULISTING_URL . '/assets/css/frontend/bootstrap.min.css', array());

        wp_enqueue_script('custom-posts', ULISTING_URL . '/assets/js/admin-custom-posts.js', []);

        /**
         * Icon and Image picker
         */
        wp_enqueue_script('stm-thumbnail', ULISTING_URL . '/assets/js/vue/stm-thumbnail-field.js', array('vue.js'), $v);
        wp_enqueue_script('stm-icon-picker', ULISTING_URL . '/assets/js/vue/stm-icon-picker.js', array('vue.js'), $v);
        wp_enqueue_script('stm-modal', ULISTING_URL . '/assets/js/vue/stm-modal.js', array('vue.js'), $v);

        $data = [
            'apiUrl'            => site_url().'/1/api/',
        ];

        wp_add_inline_script('stm-icon-picker', "var icon_data = json_parse('". ulisting_convert_content(json_encode($data)) ."');", 'before');
    }

    if ( ! uListing_subscription_active() && ! uListing_user_role_active() && ! uListing_social_login_active() )
        wp_enqueue_style('ulisting-go-pro-css', ULISTING_URL . '/assets/css/admin/gopro.css', array());
}

add_action('admin_enqueue_scripts', 'stm_listing_admin_enqueue', 1);