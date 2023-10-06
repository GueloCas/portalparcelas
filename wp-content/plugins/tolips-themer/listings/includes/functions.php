<?php
class Tolips_Listing_Function{

   public function __construct(){
      add_filter('show_uListing_demo_import', array($this, 'disable_uListing_demo_import'), 11);

      add_action( 'admin_menu', array($this, 'wpse_136058_remove_menu_pages'), 999);
      
   }

   function disable_uListing_demo_import(){
      return false;
   }
  public function wpse_136058_remove_menu_pages() {
      remove_submenu_page( 'settings-page', 'demo-import-page' );
   }
}

new Tolips_Listing_Function();