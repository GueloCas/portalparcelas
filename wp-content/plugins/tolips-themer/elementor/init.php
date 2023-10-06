<?php
if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly.
}

require 'includes/overrides.php';
require 'includes/icons.php';

if(!class_exists('Tolips_Elementor_Addons')){
  class Tolips_Elementor_Addons {

    public function __construct() {
      add_action('elementor/init', array($this, 'add_category'));
      add_action('elementor/widgets/register', array($this, 'include_elements'));
      
      add_action( 'elementor/frontend/after_register_scripts', [ $this, 'enqueue_frontend_scripts' ] );
      add_action( 'elementor/frontend/after_enqueue_styles', [ $this, 'enqueue_frontend_styles' ] );
      add_action( 'elementor/preview/enqueue_styles', function() {
        wp_enqueue_style( 'owl-carousel-css' );
      } );
    }

    public function include_addons(){
      
    }
    public function include_elements($widgets_manager) {
    
      require 'elements/base.php';
      require 'elements/logo.php';
      require 'elements/navigation-menu.php';
      require 'elements/search-box.php';
      require 'elements/brand.php';
      require 'elements/counter.php';
      require 'elements/icon-box-group.php';
      require 'elements/icon-box-classic.php';
      require 'elements/icon-box-styles.php';
      require 'elements/heading-block.php';
      require 'elements/image-content.php';
      require 'elements/posts.php';
      require 'elements/teams.php';
      require 'elements/testimonial.php';
      require 'elements/video-box.php';
      require 'elements/gallery.php';
      require 'elements/pricing-block.php';
      require 'elements/call-to-action.php';
      require 'elements/tabs-content.php';
      require 'elements/socials.php';
      require 'elements/career-block.php';
      require 'elements/countdown.php';
      require 'elements/box-hover.php';
      require 'elements/video-carousel.php';
      require 'elements/list-number.php';
      require 'elements/work-process.php';
      require 'elements/user.php';
      require 'elements/users.php';
      
      if( in_array( 'ulisting/uListing.php', apply_filters( 'active_plugins', get_option( 'active_plugins' ) ) ) ){
        require 'elements/user-wishlist.php';
        require 'elements/listing-banner-group.php';
        require 'elements/listing-banner.php';
        require 'elements/listings.php';
        require 'elements/listing-search-form.php';
        require 'elements/listing-pricing-plan.php';
      }
      
      if(class_exists('Tribe__Events__Main')){
        require 'elements/events.php';
      }

      if(class_exists('RevSlider')){
        require 'elements/rev-slider.php';
      }

    }

    public function add_category() {
      Elementor\Plugin::instance()->elements_manager->add_category(
      'gavias_elements',
      array(
        'title' => __('Gavias Elements', 'tolips-themer'),
        'icon'  => 'fa fa-plug',
      ),
      9);
    }
         
    public function enqueue_frontend_scripts() {
      wp_register_script('jquery.owl.carousel', GAVIAS_TOLIPS_PLUGIN_URL . 'elementor/assets/libs/owl-carousel/owl.carousel.js' , array(), '1.0.0', true);
      wp_register_script('slick', GAVIAS_TOLIPS_PLUGIN_URL . 'elementor/assets/libs/slick/slick.min.js' , array(), '1.0.0', true);
      wp_register_script('jquery.appear', GAVIAS_TOLIPS_PLUGIN_URL . 'elementor/assets/libs/jquery.appear.js' , array(), '1.0.0', true);
      wp_register_script('jquery.count_to', GAVIAS_TOLIPS_PLUGIN_URL . 'elementor/assets/libs/count-to.js' , array(), '1.0.0', true);
      wp_register_script('isotope', GAVIAS_TOLIPS_PLUGIN_URL . 'elementor/assets/libs/isotope.pkgd.min.js' , array(), '1.0.0', true);
      wp_register_script('countdown', GAVIAS_TOLIPS_PLUGIN_URL . 'elementor/assets/libs/countdown.js' , array(), '1.0.0', true);
      wp_register_script('gavias.elements', GAVIAS_TOLIPS_PLUGIN_URL . 'elementor/assets/main.js' , array(), '1.0.0', true);
      
      wp_register_script('map-ui', GAVIAS_TOLIPS_PLUGIN_URL . '/elementor/assets/libs/jquery.ui.map.min.js');
      $google_api_key = apply_filters('gavias-elements/map-api', '');
      wp_register_script(
        'google-maps-api',
        add_query_arg( array( 'key' => $google_api_key ), 'https://maps.googleapis.com/maps/api/js' ), false, false, true
      );

      wp_register_script('gmap3', GAVIAS_TOLIPS_PLUGIN_URL . '/elementor/assets/libs/gmap3.min.js'); 
      
    }

    public function enqueue_frontend_styles() {
      wp_register_style('owl-carousel-css', GAVIAS_TOLIPS_PLUGIN_URL . 'elementor/assets/libs/owl-carousel/assets/owl.carousel.css', false, '1.0.0');
      wp_register_style('slick', GAVIAS_TOLIPS_PLUGIN_URL . 'elementor/assets/libs/slick/slick.css', false, '1.0.0');
      wp_enqueue_style('gva-element-base', GAVIAS_TOLIPS_PLUGIN_URL . 'elementor/assets/css/base.css');
    }
  }     
}

$addons = new Tolips_Elementor_Addons();

