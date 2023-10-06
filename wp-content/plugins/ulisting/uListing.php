<?php
/**
 * Plugin Name: Directory Listings WordPress plugin - uListing
 * Plugin URI: https://wordpress.org/plugins/ulisting/
 * Description: uListing - Universal Listing WordPress Plugin. Developing listing and classified ads websites is a lucrative business opportunity, but in the past, it could be complicated to set up and maintain such a site.
 * Author: StylemixThemes
 * Author URI: https://stylemixthemes.com/
 * Text Domain: ulisting
 * Version: 2.1.5
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

define( 'ULISTING_VERSION', '2.1.5' );
define( 'ULISTING_DB_VERSION', '2.0.8' );
define( 'ULISTING_PATH', dirname( __FILE__ ) );
define( 'ULISTING_BASE_URL', '/1/api' );
define( 'ULISTING_URL', plugins_url( '', __FILE__ ) );
define( 'ULISTING_ADMIN_PATH', ULISTING_PATH . '/includes/admin' );
define( 'ULISTING_PLUGIN_FILE', __FILE__ );

if ( function_exists( 'icl_object_id' ) ) {
	global $sitepress;
	define( 'ULISTING_DEFAULT_LANG', $sitepress->get_default_language() );
} else {
	define( 'ULISTING_DEFAULT_LANG', '' );
}

if ( ! is_textdomain_loaded( 'ulisting' ) ) {
	load_plugin_textdomain(
		'ulisting',
		false,
		'ulisting/language'
	);
}
require_once __DIR__ . '/includes/autoload.php';
register_activation_hook( __FILE__, 'uListing_plugin_activation' );
register_deactivation_hook( __FILE__, 'uListing_plugin_deactivation' );
register_uninstall_hook( __FILE__, 'uListing_plugin_uninstall' );

if ( is_admin() ) {
	require_once ULISTING_PATH . '/includes/item-announcements.php';
	require_once ULISTING_PATH . '/includes/lib/admin-notification/admin-notification.php';

	$init_data = array(
		'plugin_title' => 'uListing',
		'plugin_name'  => 'ulisting',
		'plugin_file'  => ULISTING_PLUGIN_FILE,
		'logo'         => ULISTING_URL . '/assets/images/ulisting.png',
	);

	stm_admin_notification_init( $init_data );
}
