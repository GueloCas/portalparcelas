<?php 
/**
 * Plugin Name: Tolips Themer
 * Description: Open Setting, Post Type, Shortcode ... for theme 
 * Version: 1.1.5
 * Author: Gaviasthemes Team
 */

define( 'GAVIAS_TOLIPS_PLUGIN_URL', plugin_dir_url( __FILE__ ) );
define( 'GAVIAS_TOLIPS_PLUGIN_DIR', plugin_dir_path( __FILE__ )  );

class Gavias_Tolips_Themer{

   private static $instance = null;
   public static function instance() {
      if ( is_null( self::$instance ) ) {
         self::$instance = new self();
      }
      return self::$instance;
   }

	public function __construct(){
		$this->include_files();
		$this->include_post_types();
      add_action('wp_head', array($this, 'gaviasthemer_ajax_url'));
      add_action('wp_enqueue_scripts', array($this, 'register_scripts'));
      add_action('admin_enqueue_scripts', array($this, 'register_scripts_admin'));
      load_plugin_textdomain('tolips-themer', false, 'tolips-themer/languages/');
      $this->gavias_plugin_update();
	}
   
   public function gaviasthemer_ajax_url(){
     echo '<script> var ajaxurl = "' . admin_url('admin-ajax.php') . '";</script>';
   }


	public function include_files(){
      require_once('redux/admin-init.php');
      require_once('includes/functions.php');
		require_once('includes/hook.php');
      require_once('elementor/init.php');   
      require_once('add-ons/form-ajax/init.php'); 
      require_once('widgets/recent_posts.php'); 
      require_once('sample/init.php'); 
      if( in_array( 'ulisting/uListing.php', apply_filters( 'active_plugins', get_option( 'active_plugins' ) ) ) ){
        require_once('listings/init.php');
      }
	}

	public function include_post_types(){
      require_once('posttypes/footer.php');
      require_once('posttypes/header.php');
      require_once('posttypes/team.php');
	}

   public function register_scripts(){
      $js_dir = plugin_dir_url( __FILE__ ).'assets/js';
      wp_register_script('gavias-themer', $js_dir.'/main.js', array('jquery'), null, true);
      wp_enqueue_script('gavias-themer');
   }


   public function register_scripts_admin() {
      $css_dir = plugin_dir_url( __FILE__ ).'assets/css';
      wp_enqueue_style('tolips-icons-custom', GAVIAS_TOLIPS_PLUGIN_URL . 'assets/icons/flaticon.css' );
   }

   public function gavias_plugin_update() {
      require 'plugin-update/plugin-update-checker.php';
      Puc_v4_Factory::buildUpdateChecker(
         'https://gaviasthemes.com/plugins/dummy_data/tolips-themer-update-plugin.json',
         __FILE__,
         'tolips-themer'
      );
   }

}

new Gavias_Tolips_Themer();
