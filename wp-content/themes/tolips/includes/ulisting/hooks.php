<?php
add_post_type_support( 'listing', 'comments' );
add_filter('ulisting_attribute_price_style_templates', 'tolips_listing_price_style_templates');
add_filter('ulisting_map_marker_icon', 'tolips_map_marker_icon');
add_filter('wp_get_nav_menu_items', 'tolips_nav_items', 11, 3 );
add_filter('ulisting_user_meta_data', 'tolips_ulisting_user_meta_data');
add_action('wp', 'tolips_post_views', 100, 1);
add_filter('template_include', 'tolips_account_template', 11);

add_filter('show_uListing_demo_import', 'toplip_disable_uListing_demo_import', 99);

/* ------- */
function tolips_listing_price_style_templates($style_templates) {
   $style_templates = [
      "ulisting_style_1" => [
         "icon" => ULISTING_URL."/assets/img/price.png",
         "name" => "Style 1",
         "attribute_template" => "<div class='ulisting-listing-price'> [old_price] [price] </div>",
         "price_template" => "<span class='ulisting-listing-price-new'>[price]<span class='suffix'>[suffix]</span></span>",
         "old_price_template" => "<span class='ulisting-listing-price-old'>[old_price]</span>",
      ]

   ];
   return $style_templates;
}

/* ------- */
function tolips_map_marker_icon($icon) {
   $icon['url']        = get_template_directory_uri() . "/images/map-marker.svg";
   $icon['scaledSize'] = [ "width"  => 40, "height" => 40 ];
   return $icon;
}

/* ------- */
function tolips_nav_items( $items, $menu, $args ) {
   $settings_listing_pages = get_option("stm_listing_pages");
   $listing_type_page = isset($settings_listing_pages['listing_type_page']) ? $settings_listing_pages['listing_type_page'] : array();
   
   if( is_admin() ){
      return $items;
   }
   foreach( $items as $item ) {
      if( in_array($item->object_id, $listing_type_page) ){
         if( $item->attr_title ){
            $item->url .= '?layout=' . $item->attr_title;
         }
      }
   }
   return $items;
}

/* ------- */
function tolips_ulisting_user_meta_data ($data) {
   $user_meta = array(
      'nickname'        => esc_html__( 'Nickname', 'tolips' ),
      'position'        => esc_html__( 'Position', 'tolips' ),
      'phone_mobile'    => esc_html__( 'Mobile Phone', 'tolips' ),
      'phone_office'    => esc_html__( 'Office Phone', 'tolips' ),
      'fax'             => esc_html__( 'Fax', 'tolips' ),
      'url'             => esc_html__( 'Website', 'tolips' ),
      'address'         => esc_html__( 'Address', 'tolips' ),
      'latitude'        => esc_html__( 'Location', 'tolips' ),
      'longitude'       => esc_html__( 'Longitude', 'tolips' ),
      'license'         => esc_html__( 'License', 'tolips' ),
      'tax_number'      => esc_html__( 'Tax number', 'tolips' ),
      'description'     => esc_html__( 'Description', 'tolips' ),
      'linkedin'        => esc_html__( 'Linkedin', 'tolips' )
   );

   if(isset($data['user']->ID)){
      foreach ($user_meta as $key => $name) {
         $meta = get_user_meta($data['user']->ID, $key);
         $data['data'][$key] = [
           'name'  => $name,
           'value' => (isset($meta[0])) ? $meta[0] : ''
       ];
      }
   }
   return $data;
}

/* ------- */
function tolips_post_views(){
   $post_id = get_the_ID();
   $views = intval(get_post_meta($post_id, 'stm_post_views', true));
   $now_views = (!empty($views)) ? $views + 1 : 1;
   update_post_meta($post_id, 'stm_post_views', $now_views);
}

/* ------- */
function tolips_account_template($template) {
   $stm_account_page = uListing\Classes\StmListingSettings::getPages(uListing\Classes\StmListingSettings::PAGE_ACCOUNT_PAGE);
   $stm_add_page     = uListing\Classes\StmListingSettings::getPages(uListing\Classes\StmListingSettings::PAGE_ADD_LISTING);
   if( (intval($stm_account_page) === get_the_ID() || intval($stm_add_page) === get_the_ID() ) && is_user_logged_in() ){
      return locate_template(array('ulisting/account-page.php'));
   }
   return $template;
}

/* -------- */
function toplip_disable_uListing_demo_import(){
   return false;
}


if (!function_exists('tolips_listing_action_add_agent')) {
    function tolips_listing_action_add_agent() {
        wp_enqueue_script('stm-agent-add', ULISTING_URL . '/assets/js/frontend/stm-agent-add.js', array('vue'), ULISTING_VERSION, true);
    }

    add_action('tolips_listing_action_add_agent', 'tolips_listing_action_add_agent');
}


  add_action( 'admin_init', 'tolips_listing_remove_menu_pages' );
  function tolips_listing_remove_menu_pages() {
      remove_submenu_page( 'settings-page', 'demo-import-page' );
   }