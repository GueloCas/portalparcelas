<?php
if (!defined('ABSPATH')) {
	 exit; // Exit if accessed directly.
}

if(!class_exists('Tolips_Listings_Addons')){
  	class Tolips_Listings_Addons {

		public function __construct(){
			$this->include_files();
		}

		public function include_files(){
			require_once('includes/functions.php');
			require_once('includes/shortcode.php');
			
			require_once('comment/base.php');
			require_once('comment/backend.php');
			require_once('comment/frontend.php');
		}
  	}
}

new Tolips_Listings_Addons();

