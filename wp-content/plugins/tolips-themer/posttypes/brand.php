<?php

if(!function_exists('gavias_post_type_brands')  ){ 
  function gavias_post_type_brands(){
    $labels = array(
      'name' => __( 'Brand Logos', 'gaviasframework' ),
      'singular_name' => __( 'Brand', 'gaviasframework' ),
      'add_new' => __( 'Add New Brand', 'gaviasframework' ),
      'add_new_item' => __( 'Add New Brand', 'gaviasframework' ),
      'edit_item' => __( 'Edit Brand', 'gaviasframework' ),
      'new_item' => __( 'New Brand', 'gaviasframework' ),
      'view_item' => __( 'View Brand', 'gaviasframework' ),
      'search_items' => __( 'Search Brands', 'gaviasframework' ),
      'not_found' => __( 'No Brands found', 'gaviasframework' ),
      'not_found_in_trash' => __( 'No Brands found in Trash', 'gaviasframework' ),
      'parent_item_colon' => __( 'Parent Brand:', 'gaviasframework' ),
      'menu_name' => __( 'Brands', 'gaviasframework' ),
    );

    $args = array(
        'labels' => $labels,
        'hierarchical' => true,
        'description' => 'List Brand',
        'supports' => array( 'title', 'thumbnail'),
        'public' => true,
        'show_ui' => true,
        'show_in_menu' => true,
        'menu_position' => 5,
        'show_in_nav_menus' => false,
        'publicly_queryable' => true,
        'exclude_from_search' => false,
        'has_archive' => true,
        'query_var' => true,
        'can_export' => true,
        'rewrite' => true,
        'capability_type' => 'post'
    );
    register_post_type( 'brands', $args );
  }

  add_action('init','gavias_post_type_brands');
}