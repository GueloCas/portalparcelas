<?php
/**
	* $Desc
	*
	* @author     Gaviasthemes Team     
	* @copyright  Copyright (C) 2021 gaviasthemes. All Rights Reserved.
	* @license    GNU/GPL v2 or later http://www.gnu.org/licenses/gpl-2.0.html
	* 
*/

define('TOLIPS_THEME_DIR', get_template_directory());
define('TOLIPS_THEME_URL', get_template_directory_uri());

/*
	* Include list of files from Gavias Framework.
*/
require_once(TOLIPS_THEME_DIR . '/includes/theme-functions.php'); 
require_once(TOLIPS_THEME_DIR . '/includes/template.php'); 
require_once(TOLIPS_THEME_DIR . '/includes/theme-hook.php'); 
require_once(TOLIPS_THEME_DIR . '/includes/theme-layout.php'); 
require_once(TOLIPS_THEME_DIR . '/includes/comment.php'); 
require_once(TOLIPS_THEME_DIR . '/includes/metaboxes.php'); 
require_once(TOLIPS_THEME_DIR . '/includes/menu/megamenu.php'); 
require_once(TOLIPS_THEME_DIR . '/includes/elementor/hooks.php');
require_once(TOLIPS_THEME_DIR . '/includes/customize/custom-typo.php'); 
require_once(TOLIPS_THEME_DIR . '/includes/customize/custom-color.php'); 

// Load Ulising plugin
if( in_array('ulisting/uListing.php', apply_filters('active_plugins', get_option('active_plugins'))) ){
	require_once(TOLIPS_THEME_DIR . '/includes/ulisting/base.php');
	require_once(TOLIPS_THEME_DIR . '/includes/ulisting/attributes.php');
	require_once(TOLIPS_THEME_DIR . '/includes/ulisting/elements.php');
	require_once(TOLIPS_THEME_DIR . '/includes/ulisting/data.php');
	require_once(TOLIPS_THEME_DIR . '/includes/ulisting/filter.php');
	require_once(TOLIPS_THEME_DIR . '/includes/ulisting/functions.php');
	require_once(TOLIPS_THEME_DIR . '/includes/ulisting/hooks.php');
	require_once(TOLIPS_THEME_DIR . '/includes/ulisting/icons.php');
	require_once(TOLIPS_THEME_DIR . '/includes/ulisting/preview.php');
}

//Load Woocommerce plugin
if(tolips_woocommerce_activated()){
	add_theme_support( "woocommerce" );
	require_once(TOLIPS_THEME_DIR . '/includes/woocommerce/functions.php'); 
	require_once(TOLIPS_THEME_DIR . '/includes/woocommerce/hooks.php'); 
}

// Load Redux - Theme options framework
if(class_exists('Redux')){
	require_once( TOLIPS_THEME_DIR . '/includes/options/init.php');
	require_once(TOLIPS_THEME_DIR . '/includes/options/opts-general.php'); 
	require_once(TOLIPS_THEME_DIR . '/includes/options/opts-header.php'); 
	require_once(TOLIPS_THEME_DIR . '/includes/options/opts-footer.php'); 
	require_once(TOLIPS_THEME_DIR . '/includes/options/opts-breadcrumb.php'); 
	require_once(TOLIPS_THEME_DIR . '/includes/options/opts-styling.php'); 
	require_once(TOLIPS_THEME_DIR . '/includes/options/opts-typography.php'); 
	require_once(TOLIPS_THEME_DIR . '/includes/options/opts-blog.php'); 
	require_once(TOLIPS_THEME_DIR . '/includes/options/opts-lt-general.php'); 
	require_once(TOLIPS_THEME_DIR . '/includes/options/opts-login-register.php'); 
	require_once(TOLIPS_THEME_DIR . '/includes/options/opts-page.php'); 
	require_once(TOLIPS_THEME_DIR . '/includes/options/opts-event.php'); 
	 if(tolips_woocommerce_activated() ){
		require_once(TOLIPS_THEME_DIR . '/includes/options/opts-woo.php'); 
	 }
} 

// TGM plugin activation
if ( is_admin() ) {
	require_once(TOLIPS_THEME_DIR . '/includes/tgmpa/class-tgm-plugin-activation.php');
	require_once(TOLIPS_THEME_DIR . '/includes/tgmpa/config.php');
}
load_theme_textdomain('tolips', get_template_directory() . '/languages');

//-------- Register sidebar default in theme -----------
//------------------------------------------------------
function tolips_widgets_init() {
	register_sidebar( array(
		'name' => esc_html__('Default Sidebar', 'tolips'),
		'id' => 'default_sidebar',
		'description' => esc_html__('Appears in the Default Sidebar section of the site.', 'tolips'),
		'before_widget' => '<aside id="%1$s" class="widget clearfix %2$s">',
		'after_widget' => '</aside>',
		'before_title' => '<h3 class="widget-title"><span>',
		'after_title' => '</span></h3>',
	) );

	if(tolips_woocommerce_activated()){
		register_sidebar( array(
			'name' => esc_html__('WooCommerce Sidebar', 'tolips'),
			'id' 	 => 'woocommerce_sidebar',
			'description' 		=> esc_html__('Appears in the Plugin WooCommerce section of the site.', 'tolips'),
			'before_widget' 	=> '<aside id="%1$s" class="widget clearfix %2$s">',
			'after_widget' 	=> '</aside>',
			'before_title' 	=> '<h3 class="widget-title"><span>',
			'after_title' 		=> '</span></h3>',
		) );
		register_sidebar( array(
			'name' => esc_html__('WooCommerce Single', 'tolips'),
			'id' 	 => 'woocommerce_single_summary',
			'description' 		=> esc_html__('Appears in the WooCommerce Single Page like social, description text ...', 'tolips'),
			'before_widget' 	=> '<aside id="%1$s" class="widget clearfix %2$s">',
			'after_widget' 	=> '</aside>',
			'before_title' 	=> '<h3 class="widget-title"><span>',
			'after_title' 		=> '</span></h3>',
		) );
	}

	register_sidebar( array(
		'name' => esc_html__('After Offcanvas Mobile', 'tolips'),
		'id' 	 => 'offcanvas_sidebar_mobile',
		'description' 		=> esc_html__('Appears in the Offcanvas section of the site.', 'tolips'),
		'before_widget' 	=> '<aside id="%1$s" class="widget clearfix %2$s">',
		'after_widget' 	=> '</aside>',
		'before_title' 	=> '<h3 class="widget-title"><span>',
		'after_title' 		=> '</span></h3>',
	) );
	register_sidebar( array(
		'name' => esc_html__('Blog Sidebar', 'tolips'),
		'id' 	 => 'blog_sidebar',
		'description'	 	=> esc_html__('Appears in the Blog sidebar section of the site.', 'tolips'),
		'before_widget' 	=> '<aside id="%1$s" class="widget clearfix %2$s">',
		'after_widget' 	=> '</aside>',
		'before_title' 	=> '<h3 class="widget-title"><span>',
		'after_title' 		=> '</span></h3>',
	) );
	register_sidebar( array(
		'name' => esc_html__('Page Sidebar', 'tolips'),
		'id' 	 => 'other_sidebar',
		'description' 		=> esc_html__('Appears in the Page Sidebar section of the site.', 'tolips'),
		'before_widget' 	=> '<aside id="%1$s" class="widget clearfix %2$s">',
		'after_widget' 	=> '</aside>',
		'before_title' 	=> '<h3 class="widget-title"><span>',
		'after_title' 		=> '</span></h3>',
	) );
	register_sidebar( array(
		'name' => esc_html__('Filter Sidebar', 'tolips'),
		'id' 	 => 'lt_filter_sidebar',
		'description' 		=> esc_html__('Appears in the Filter Listing sidebar section of the site, Only support Filter sidebar type.', 'tolips'),
		'before_widget' 	=> '<aside id="%1$s" class="widget clearfix %2$s">',
		'after_widget' 	=> '</aside>',
		'before_title' 	=> '<h3 class="widget-title"><span>',
		'after_title' 		=> '</span></h3>',
	) );
	register_sidebar( array(
		'name' => esc_html__('Footer first', 'tolips'),
		'id' 	 => 'footer-sidebar-1',
		'description' 		=> esc_html__('Appears in the Footer first section of the site.', 'tolips'),
		'before_widget' 	=> '<aside id="%1$s" class="widget clearfix %2$s">',
		'after_widget' 	=> '</aside>',
		'before_title' 	=> '<h3 class="widget-title"><span>',
		'after_title' 		=> '</span></h3>',
	) );
	register_sidebar( array(
		'name' => esc_html__('Footer second', 'tolips'),
		'id' 	 => 'footer-sidebar-2',
		'description' 		=> esc_html__('Appears in the Footer second section of the site.', 'tolips'),
		'before_widget' 	=> '<aside id="%1$s" class="widget clearfix %2$s">',
		'after_widget' 	=> '</aside>',
		'before_title' 	=> '<h3 class="widget-title"><span>',
		'after_title' 		=> '</span></h3>',
	) );
	register_sidebar( array(
		'name' => esc_html__('Footer third', 'tolips'),
		'id' 	 => 'footer-sidebar-3',
		'description' 		=> esc_html__('Appears in the Footer third section of the site.', 'tolips'),
		'before_widget' 	=> '<aside id="%1$s" class="widget clearfix %2$s">',
		'after_widget' 	=> '</aside>',
		'before_title' 	=> '<h3 class="widget-title"><span>',
		'after_title' 		=> '</span></h3>',
	) );
	register_sidebar( array(
		'name' => esc_html__('Footer four', 'tolips'),
		'id' 	 => 'footer-sidebar-4',
		'description' 		=> esc_html__('Appears in the Footer four section of the site.', 'tolips'),
		'before_widget' 	=> '<aside id="%1$s" class="widget clearfix %2$s">',
		'after_widget' 	=> '</aside>',
		'before_title' 	=> '<h3 class="widget-title"><span>',
		'after_title' 		=> '</span></h3>',
	) );
}
add_action('widgets_init', 'tolips_widgets_init');


if ( ! function_exists('tolips_fonts_url') ) :
/**
 *
 * @return string Google fonts URL for the theme.
 */
function tolips_fonts_url() { 
	$fonts_url = '';
	$fonts     = array();
	$subsets   = '';
	$protocol = is_ssl() ? 'https' : 'http';
	/*
		* Translators: If there are characters in your language that are not supported
		* by Noto Sans, translate this to 'off'. Do not translate into your own language.
	*/
	if ( 'off' !== _x('on', 'Inter font: on or off', 'tolips') ) {
		$fonts[] = 'Inter:wght@400;500;600;700;800;900';
	}
	if ( $fonts ) {
		$fonts_url = add_query_arg(array(
			'family' => ( implode('&family=', $fonts) ),
			'display' => 'swap',
		),  $protocol.'://fonts.googleapis.com/css2');
	}

	return $fonts_url;
}

endif;

function tolips_custom_styles() {
	$custom_css = get_option('tolips_theme_custom_styles');
	if($custom_css){
		wp_enqueue_style(
			'tolips-custom-style',
			TOLIPS_THEME_URL . '/css/custom_script.css'
		);
		wp_add_inline_style('tolips-custom-style', $custom_css );
	}
}
add_action('wp_enqueue_scripts', 'tolips_custom_styles', 9999 );

function tolips_init_scripts(){
	global $post;
	$protocol = is_ssl() ? 'https' : 'http';
	if ( is_singular() && comments_open() && get_option('thread_comments') ){
		wp_enqueue_script('comment-reply');
	}
	$skin = tolips_get_option('skin_color', '');
	$skin = !empty($skin) ? 'skins/' . $skin . '/' : ''; 
	$theme = wp_get_theme('tolips');
	$theme_version = $theme['Version'];
	
	//JQuery
	wp_enqueue_script('bootstrap', TOLIPS_THEME_URL . '/js/bootstrap.min.js', array('jquery'));
	wp_enqueue_script('perfect-scrollbar', TOLIPS_THEME_URL . '/js/perfect-scrollbar.min.js');
	wp_enqueue_script('lightgallery', TOLIPS_THEME_URL . '/js/lightgallery/js/lightgallery.min.js');
	wp_enqueue_script('sticky-kit', TOLIPS_THEME_URL . '/js/sticky-kit.js');
	wp_enqueue_script('owl-carousel', TOLIPS_THEME_URL . '/js/owl-carousel/owl.carousel.min.js');
	
	wp_enqueue_script('jquery-magnific-popup', TOLIPS_THEME_URL . '/js/magnific/jquery.magnific-popup.min.js');
	wp_enqueue_script('jquery-cookie', TOLIPS_THEME_URL . '/js/jquery.cookie.js', array('jquery'));
	wp_enqueue_script('jquery-appear', TOLIPS_THEME_URL . '/js/jquery.appear.js');
	
	wp_enqueue_script('tolips-main', TOLIPS_THEME_URL . '/js/main.js', array('imagesloaded', 'jquery-masonry'));
	wp_register_script('tolips-mortgage-calc', TOLIPS_THEME_URL . '/js/mortgage_calc.js');

	//CSS
	wp_enqueue_style('tolips-fonts', tolips_fonts_url(), array(), null );
	wp_enqueue_style('dashicons');
	wp_enqueue_style('lightgallery', TOLIPS_THEME_URL . '/js/lightgallery/css/lg-transitions.min.css');
	wp_enqueue_style('owl-carousel', TOLIPS_THEME_URL .'/js/owl-carousel/assets/owl.carousel.css');
	wp_enqueue_style('magnific', TOLIPS_THEME_URL .'/js/magnific/magnific-popup.css');
	wp_enqueue_style('fontawesome', TOLIPS_THEME_URL . '/css/fontawesome/css/all.css');
	
	wp_enqueue_style('tolips-line-awesome', TOLIPS_THEME_URL . '/css/line-awesome/css/line-awesome.min.css');
	wp_enqueue_style('tolips-style', TOLIPS_THEME_URL . '/style.css');
	
	wp_enqueue_style('bootstrap', TOLIPS_THEME_URL . '/css/' . $skin . 'bootstrap.css', array(), $theme_version, 'all'); 
	wp_enqueue_style('ulisting-style', TOLIPS_THEME_URL . '/css/' . $skin . 'ulisting.css', array(), $theme_version , 'all');
	wp_enqueue_style('tolips-template', TOLIPS_THEME_URL . '/css/' . $skin . 'template.css', array(), $theme_version , 'all');
	
	//Woocommerce
	if(tolips_woocommerce_activated() ){
		wp_enqueue_script('tolips-woocommerce', TOLIPS_THEME_URL . '/js/woocommerce.js');
		wp_dequeue_script('wc-add-to-cart');
		wp_register_script('wc-add-to-cart', TOLIPS_THEME_URL . '/js/add-to-cart.js' , array('jquery') );
		wp_enqueue_script('wc-add-to-cart');
		wp_enqueue_style('tolips-woocoomerce', TOLIPS_THEME_URL . '/css/' . $skin . 'woocommerce.css', array(), $theme_version , 'all'); 
	}
}
add_action('wp_enqueue_scripts', 'tolips_init_scripts', 9);

// remove_role("agent");
// if (!get_role("agent")) {
// 	add_role("agent", "Agent", [
//     	"default"                 => 0,
//     	"listing_limit"           => "0",
//     	"comment"                 => "1",
//     	"listing_moderation"      => "0",
//     	"stm_listing_role"        => "1"
// 	]);
// }
