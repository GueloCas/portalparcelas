<?php
/**
 * This file represents an example of the code that themes would use to register
 * the required plugins.
 *
 * It is expected that theme authors would copy and paste this code into their
 * functions.php file, and amend to suit.
 *
 * @see http://tgmpluginactivation.com/configuration/ for detailed documentation.
 *
 * @package    TGM-Plugin-Activation
 * @subpackage Example
 * @version    2.5.2
 * @author     Thomas Griffin, Gary Jones, Juliette Reinders Folmer
 * @copyright  Copyright (c) 2011, Thomas Griffin
 * @license    http://opensource.org/licenses/gpl-2.0.php GPL v2 or later
 * @link       https://github.com/TGMPA/TGM-Plugin-Activation
 */

/**
 * Include the TGM_Plugin_Activation class.
 */

add_action( 'tgmpa_register', 'tolips_register_required_plugins' );

function tolips_register_required_plugins() {
	$plugins = array(
		array(
			'name'                     => esc_html__('Tolips Themer', 'tolips'), 
	    	'slug'                    	=> 'tolips-themer', 
	    	'required'                	=> true, 
	    	'source'				   		=> 'http://gaviasthemes.com/plugins/tolips-themer.zip'
		),
		array(
			'name'                     => esc_html__('Slider Revolution', 'tolips'), 
	    	'slug'                    	=> 'revslider', 
	    	'required'                	=> true,
	    	'source'				   		=> 'http://gaviasthemes.com/plugins/revslider.zip'
		),
		array(
			'name'                     => esc_html__('Elementor Page Builder', 'tolips'), 
		   'slug'                     => 'elementor', 
		   'required'                 => true, 
		),
		array(
			'name'                     => esc_html__('Ulisting', 'tolips'), 
		   'slug'                     => 'ulisting', 
		   'required'                 => true,
	    	'source'				   		=> 'http://gaviasthemes.com/plugins/ulisting.zip'
		),
		array(
			'name'                     => esc_html__('Events Calendar', 'tolips'), 
		   'slug'                     => 'the-events-calendar', 
		   'required'                 => true, 
		),
		array(
			'name'                     => esc_html__('Meta Box', 'tolips'), 
		   'slug'                     => 'meta-box', 
		   'required'                 => true, 
		),
		array(
			'name'                     => esc_html__('Contact Form 7', 'tolips'), 
		   'slug'                     => 'contact-form-7', 
		   'required'                 => true, 
		),
		array(
			'name'                     => esc_html__('MailChimp', 'tolips'), 
		   'slug'                     => 'mailchimp-for-wp', 
		   'required'                 => true, 
		),
		array(
			'name'                     => esc_html__('Classic Editor', 'tolips'), 
		   'slug'                     => 'classic-editor', 
		   'required'                 => true, 
		),
		array(
			'name'                     => esc_html__('Woocommerce', 'tolips'), 
		   'slug'                     => 'woocommerce',
		   'required'                 => false,
		),
	);
	$config = array(
      'id'           => 'tolips',
		'default_path' => '', // Default absolute path to pre-packaged plugins.
		'menu' => 'tgmpa-install-plugins', // Menu slug.
		'has_notices' => true, // Show admin notices or not.
		'dismissable' => true, // If false, a user cannot dismiss the nag message.
		'dismiss_msg' => '', // If 'dismissable' is false, this message will be output at top of nag.
		'is_automatic' => false, // Automatically activate plugins after installation or not.
		'message' => '' // Message to output right before the plugins table.
	);
   tgmpa( $plugins, $config );
}