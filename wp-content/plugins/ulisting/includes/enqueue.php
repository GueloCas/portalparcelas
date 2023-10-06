<?php
function stm_listing_enqueue_scripts_styles()
{
	$v = ULISTING_VERSION;

	wp_enqueue_style('bootstrap', ULISTING_URL . '/assets/css/frontend/bootstrap.min.css');
	wp_enqueue_style('ulisting-style', ULISTING_URL . '/assets/css/frontend/ulisting-style.css');
	wp_enqueue_style('stm-grid-css', ULISTING_URL . '/assets/css/stm-grid.css');
	wp_enqueue_style('font-awesome', ULISTING_URL . '/assets/css/font-awesome.min.css');
	wp_enqueue_style('bootstrap-datepicker', ULISTING_URL . '/assets/bootstrap-datepicker/css/bootstrap-datepicker.css');
	wp_enqueue_style('select2', ULISTING_URL . '/assets/css/select2.min.css', array());
	wp_enqueue_style('rangeSlider', ULISTING_URL . '/assets/css/ion.rangeSlider.min.css', array());
	wp_enqueue_style('toastr', ULISTING_URL . '/assets/css/toastr.css', array());

	wp_enqueue_script('jquery-ui-core');
	wp_enqueue_script('jquery-ui-tabs');
    wp_enqueue_script( 'jquery-ui-autocomplete' );

	wp_enqueue_script('vue', ULISTING_URL . '/assets/js/vue/vue.js', array(), $v);
	wp_enqueue_script('bootstrap', ULISTING_URL . '/assets/js/bootstrap/bootstrap.js',  array(), $v);
	wp_enqueue_script('select2', ULISTING_URL . '/assets/js/select2.full.js', array(), $v);
	wp_enqueue_script('moment', ULISTING_URL . '/assets/js/moment.js', array(), $v);
	wp_enqueue_script('toastr', ULISTING_URL . '/assets/js/toastr.js', [], $v);
	wp_enqueue_script('js-cookie-ulisting', ULISTING_URL . '/assets/js/js.cookie.js', array(), $v, false);
	wp_enqueue_script('bootstrap-datepicker', ULISTING_URL . '/assets/bootstrap-datepicker/js/bootstrap-datepicker.js', array('jquery'), $v);
    wp_enqueue_script('stm-listing', ULISTING_URL . '/assets/js/frontend/stm-listing.js',  array(), $v);
    wp_enqueue_script('ion-rangeSlider', ULISTING_URL . '/assets/js/ion.rangeSlider.min.js',  array(), $v);
    wp_enqueue_script('Sortable-min', ULISTING_URL . '/assets/js/Sortable.min.js',  array(), $v);
    wp_enqueue_script('vue-w3c-valid', ULISTING_URL . '/assets/js/vue/vue-w3c-valid.js',  array(), $v);
    wp_enqueue_script('vuedraggable', ULISTING_URL . '/assets/js/vue/vuedraggable.min.js',  array(), $v);
    wp_enqueue_script('animated-scroll-to', ULISTING_URL . '/assets/js/animated-scroll-to.js',  array(), $v);
    wp_enqueue_script('ulisting-main', ULISTING_URL . '/assets/js/frontend/dist/ulisting-main.js',  array(), $v);
	wp_enqueue_script('tinymce', ULISTING_URL . '/assets/js/vue-tinymce-2/tinymce.min.js', array('vue'), $v);
	wp_enqueue_script('vue-easy-tinymce', ULISTING_URL . '/assets/js/vue-tinymce-2/vue-easy-tinymce.min.js', array('vue'), $v);
	wp_enqueue_script('vue2-datepicker', ULISTING_URL . '/assets/js/vue/vue2-datepicker.js', array('vue'), $v);
    wp_enqueue_script('vue-resource', ULISTING_URL . '/assets/js/vue/vue-resource.js', array('vue'), $v);
    wp_enqueue_script('vuejs-paginate', ULISTING_URL . '/assets/js/vue/vuejs-paginate.js', array('vue'), $v);
	wp_add_inline_script('vue-resource', "Vue.http.options.root = '".site_url()."/1/api';");
	wp_add_inline_script('vue', "var ulistingAjaxNonce = '".\uListing\Classes\StmVerifyNonce::createAjaxNonce()."'", 'before');
	wp_add_inline_script('vue', "var ulistingUrl = '".ULISTING_URL."'", 'before');

	ulisting_enqueue_scripts_styles($v, 'vue');
}

function gioga_add_async_defer_attribute($tag, $handle) {
	if ( 'google-maps' !== $handle )
		return $tag;
	return str_replace( ' src', ' async defer src', $tag );
}

add_filter('script_loader_tag', 'gioga_add_async_defer_attribute', 10, 2);
add_action('wp_enqueue_scripts', 'stm_listing_enqueue_scripts_styles');

function ulisting_field_components_enqueue_scripts_styles(){
	$v = ULISTING_VERSION;
	wp_enqueue_script('ulisting-main-field', ULISTING_URL . '/assets/js/frontend/dist/ulisting-main-field.js', array('vue'), $v, true);
}