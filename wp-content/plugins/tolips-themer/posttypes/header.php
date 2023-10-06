<?php
class Gavias_Themer_Header{
  public static $post_type = 'gva_header';
  
  public static $instance;

  public static function getInstance() {
    if (!isset(self::$instance) && !(self::$instance instanceof Gavias_Themer_Header)) {
      self::$instance = new Gavias_Themer_Header();
    }
    return self::$instance;
  }

  public function __construct(){ 
    
  }

  public function register_post_type_header(){
    add_action('init', array($this, 'args_post_type_header'), 10);
  }

  public function args_post_type_header(){
    $labels = array(
      'name' => __( 'Header Builder', 'gaviasframework' ),
      'singular_name' => __( 'Header Builder', 'gaviasframework' ),
      'add_new' => __( 'Add Header Builder', 'gaviasframework' ),
      'add_new_item' => __( 'Add Header Builder', 'gaviasframework' ),
      'edit_item' => __( 'Edit Header', 'gaviasframework' ),
      'new_item' => __( 'New Header Builder', 'gaviasframework' ),
      'view_item' => __( 'View Header Builder', 'gaviasframework' ),
      'search_items' => __( 'Search Header Profiles', 'gaviasframework' ),
      'not_found' => __( 'No Header Profiles found', 'gaviasframework' ),
      'not_found_in_trash' => __( 'No Header Profiles found in Trash', 'gaviasframework' ),
      'parent_item_colon' => __( 'Parent Header:', 'gaviasframework' ),
      'menu_name' => __( 'Header Builder', 'gaviasframework' ),
    );

    $args = array(
        'labels'              => $labels,
        'hierarchical'        => true,
        'description'         => __('List Header', "gaviasthemer"),
        'public'              => true,
        'show_ui'             => true,
        'show_in_menu'        => true,
        'menu_position'       => 5,
        'show_in_nav_menus'   => false,
        'publicly_queryable'  => true,
        'exclude_from_search' => true,
        'has_archive'         => true,
        'query_var'           => true,
        'can_export'          => true,
        'rewrite'             => true,
        'capability_type'     => 'post'
    );
    register_post_type( self::$post_type, $args );
  }


  public function get_headers( $default = true ){
    $args = array(
      'post_type'         => 'gva_header',
      'posts_per_page'    => -1,
      'post_status'       => 'publish',
      'orderby'           => 'title',
      'order'             => 'asc'

    );
    $posts = new WP_Query($args);
    $headers = array();
    if($default){
      $headers['__default_option_theme'] = esc_html__('Default Option Theme', 'tolips-themer');
    }
    
    $wp_query = new WP_Query($args);
    $posts = $wp_query->get_posts();
    foreach( $posts as $post ){
      $headers[$post->post_name] = $post->post_title;
    }

    wp_reset_postdata();
    return apply_filters('gaviasthemes_list_header', $headers );
  }

  public function render_header_builder($header_slug) {
    $header = get_page_by_path($header_slug, OBJECT, 'gva_header');
    if ($header && $header instanceof WP_Post) {
      return \Elementor\Plugin::instance()->frontend->get_builder_content_for_display( $header->ID );
    }
  }
}

Gavias_Themer_Header::getInstance()->register_post_type_header();