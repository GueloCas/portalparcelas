<?php
function tolips_themer_path_demo_content(){
	return (__DIR__.'/demo-data/');
}
add_filter('wbc_importer_dir_path', 'tolips_themer_path_demo_content');

//Way to set menu, import revolution slider, and set home page.
function tolips_themer_import_sample( $demo_active_import , $demo_directory_path ) {
	reset( $demo_active_import );
	$current_key = key( $demo_active_import );

	if( class_exists('uListing\Classes\StmImport') ){
		tolips_themer_import_ulisting();
		tolips_themer_import_settings();
	}

	if ( class_exists( 'RevSlider' ) ) {
		$wbc_sliders_array = array( 'slider-1.zip' );
		$slider = new RevSlider();
		foreach ($wbc_sliders_array as $s) {
			if ( file_exists( tolips_themer_path_demo_content() . 'main/'. $s ) ) {
				$slider->importSliderFromPost( true, true, tolips_themer_path_demo_content().'main/'.$s );
			}
		}
	}

	//Setting Menus
	$wbc_menu_array = array( 'main' );
	if ( isset( $demo_active_import[$current_key]['directory'] ) && !empty( $demo_active_import[$current_key]['directory'] ) && in_array( $demo_active_import[$current_key]['directory'], $wbc_menu_array ) ) {
		$top_menu = get_term_by( 'name', 'Main Menu', 'nav_menu' );
		if ( isset( $top_menu->term_id ) ) {
			set_theme_mod( 'nav_menu_locations', array(
					'primary' => $top_menu->term_id
				)
			);
		}
	}

	//Set HomePage

	$wbc_home_pages = array(
		'main' => 'Home 1'
	);
	if ( isset( $demo_active_import[$current_key]['directory'] ) && !empty( $demo_active_import[$current_key]['directory'] ) && array_key_exists( $demo_active_import[$current_key]['directory'], $wbc_home_pages ) ) {
		$page = get_page_by_title( $wbc_home_pages[$demo_active_import[$current_key]['directory']] );
		if ( isset( $page->ID ) ) {
			update_option( 'page_on_front', $page->ID );
			update_option( 'show_on_front', 'page' );
		}
	}
}

add_action( 'wbc_importer_after_content_import', 'tolips_themer_import_sample', 10, 2 );

function tolips_themer_import_ulisting(){
	global $wp_filesystem;
	require_once ( ABSPATH . '/wp-admin/includes/file.php' );
   WP_Filesystem();

	$data_file = tolips_themer_path_demo_content(). 'main/ulisting_data.json';

	$import = new uListing\Classes\StmImport();
	
	if ( $wp_filesystem->exists( $data_file ) ) {
		$contents_data = json_decode( $wp_filesystem->get_contents( $data_file ), true );
		 
		//inventory
		foreach ($contents_data['inventory_layout'] as $inventory){
			$import->inventory_layout_import($inventory);
		}

		// generation css of single layout
		$layout_id = 0;
		foreach ($contents_data['single_page_layouts'] as $layout_content) {
			$layout_content = json_decode($layout_content, true);
			if( isset($layout_content['section']) ){
				$style = uListing\Classes\Builder\UListingBuilder::generation_style($layout_content['section']);
				uListing\Classes\Builder\UListingBuilder::generation_css('ulisting_single_page_layout_' . $layout_id, $style);
			}
			$layout_id++;
		}

		//attributes
		$attrs_options = [];
		foreach ( $contents_data['attributes'] as $attribute ) {
			$attr_options = tolips_themer_create_attribute($attribute);
			if( $attribute['name'] == 'amenities' && $attr_options ){
				$attrs_options['amenities'] = $attr_options;
			}
			if( $attribute['name'] == 'bedrooms' && $attr_options ){
				$attrs_options['bedrooms'] = $attr_options;
			}
			if( $attribute['name'] == 'bathrooms' && $attr_options ){
				$attrs_options['bathrooms'] = $attr_options;
			}
			if( $attribute['name'] == 'garages' && $attr_options ){
				$attrs_options['garages'] = $attr_options;
			}
		}

		//Create Agent
		$agent_id = tolips_themer_create_agent();

		//Update Category
		$all_listing_type  = get_posts(array(
		  'fields'          => 'ids',
		  'posts_per_page'  => -1,
		  'post_type' => 'listing_type'
		));

		$term_query = new WP_Term_Query( array( 
			'taxonomy' => 'listing-category',
			'hide_empty' => false,
		));

		if( !empty( $term_query->terms )  ){
			foreach ( $term_query->terms as $term) {
				update_term_meta($term->term_id, 'stm_listing_category_type', $all_listing_type);
			}
		}

		$all_listing_ids = get_posts(array(
			'fields'          => 'ids',
			'posts_per_page'  => -1,
			'post_type' => 'listing'
		));

		$listing_types = uListing_all_listing_types();
		 foreach ($listing_types as $id => $title) {
			if( $title == 'House & Villa' ){
				$listing_id_1 = $id;
			}
			if( $title == 'Townhomes' ){
				$listing_id_2 = $id;
			}
			if( $title == 'Apartment' ){
				$listing_id_3 = $id;
			}
		}

		$index = 0;
		foreach ($all_listing_ids as $listing_id) {
			if( $index < 5 ){
				$listing_type_id = $listing_id_1;
			}elseif( $index > 4 && $index < 10){
				$listing_type_id = $listing_id_2;
			}else{
				$listing_type_id = $listing_id_3;
			}
			tolips_themer_update_meta_listing_sample($listing_id, $attrs_options, $index, $listing_type_id, $agent_id[rand(0,2)] );
			$index++;
		}
	}
}

function tolips_themer_import_settings() {
	$page_settings = [
		"account_page"  => "32",
		"add_listing"   => "33",
		"pricing_plan"  => "34"
	];

	$page_settings['listing_type_page'] = [];
	$listing_types                  = uListing_all_listing_types();
	if (isset($listing_types) && !empty($listing_types)) {
		foreach ($listing_types as $id => $title) {
			$page = get_page_by_title( $title );
			if ( isset( $page->ID ) ) {
				$page_settings[$id] = $page->ID;
				$page_settings['listing_type_page'][$id] = $page->ID;
			}
		}
	}
	update_option(uListing\Classes\StmListingSettings::ULISTING_PAGES, $page_settings);
}

function tolips_themer_update_meta_listing_sample($listing_id, $_attrs_options, $index, $listing_type_id, $agent_id) {
	$listing = uListing\Classes\StmListing::find_one($listing_id);
	$listing->setUser($agent_id);

	// Set Listing Type
	$listing_type_relationships = new uListing\Classes\StmListingTypeRelationships();
	$listing_type_relationships->listing_id = $listing_id;
	$listing_type_relationships->listing_type_id = $listing_type_id;
	$listing_type_relationships->save();

	// Set Attributes
	$orienten = ['East', 'West', 'North', 'South'];
	$listing->setOption('orienten', $orienten[rand(0, 3)]);
	
	$type = ['Residential', 'Commercial', 'Community House'];
	$listing->setOption('type', $type[rand(0, 2)]);

	$listing->setOption('rooms', rand(3, 10));
	$listing->setOption('livingrooms', rand(1, 5));
	$listing->setOption('kitchens', rand(1, 5));
	$listing->setOption('year_built', rand(2000, 2019));
	$listing->setOption('plot_size', '30mx40mx60m');
	$listing->setOption('area', rand(100, 1000));
	$listing->setOption('video', 'https://www.youtube.com/watch?v=6uKHvOPhLpY');
	$listing->setOption('virtual_tour', '<iframe src="https://my.matterport.com/show/?m=amhzZJk3ehh&amp;play=1&amp;qs=1" width="100%" height="480" frameborder="0" allowfullscreen="allowfullscreen"></iframe>');
	
	$locations = array();
	$locations[] = array("277 Alexander St, Rochester NY", "43.1535054", "-77.5954478");
	$locations[] = array("16 Paul St, Rochester, NY", "43.15657789999999", "-77.6088465");
	$locations[] = array("21 Monroe Ave, Rochester NY", "43.1226551", "-77.561077");
	$locations[] = array("982 Monroe Ave, Rochester NY", "43.1406704", "-77.58278469999999");
	$locations[] = array("101 Valley Rd, Rochester NY", "43.1268118", "-77.5531128");
	$locations[] = array("112 Tryon Park, Rochester, NY", "43.1659365", "-77.54133780000001");
	$locations[] = array("2500 East Ave, Rochester, NY", "43.1379274", "-77.5388657");
	$locations[] = array("1600 East Ave, Rochester, NY", "43.1475197", "-77.5607776");
	$locations[] = array("66 Eagan Blvd, Rochester, NY", "43.070527130570746", "-77.616078672927");
	$locations[] = array("81 Gallup Rd, Spencerport, NY", "43.1950209200296", "-77.87716755943109");
	$locations[] = array("30 Buck Hill Rd, Rochester, NY", "43.20075332257385", "-77.70828637292396");
	$locations[] = array("8 Emanon St, Rochester, NY", "43.18713267733211", "-77.62069751710263");
	$locations[] = array("29 Maiden Ln, Rochester, NY", "43.22364991029099", "-77.69942920175916");
	$locations[] = array("114 Holcroft Rd, Rochester, NY", "43.22345117490063", "-77.62392348641657");
	$locations[] = array("68 Kings Hwy S, Rochester, NY", "43.208741576058344", "-77.57934866128043");
	$locations[] = array("18 Tamarack St, Rochester, NY", "43.26482767081182", "-77.62090635757998");
	$locations[] = array("29 Tioga Dr, Rochester, NY", "43.24176530148975", "-77.66293817477266");
	$locations[] = array("10 Woodward St, Rochester, NY", "43.16336079574543", "-77.59497617107529");
	$locations[] = array("25 Bay Village Dr, Rochester, NY", "43.19496416352416", "-77.53357337477377");
	$locations[] = array("22 Beverly St, Rochester, NY", "43.14632817598757", "-77.57144392874697");
	$locations[] = array("22 Chalford Rd, Rochester, NY", "43.24133192444476", "-77.63215537477274");
	$locations[] = array("20 West Ave, Rochester, NY", "43.149880882323885", "-77.64355290361041");
	$locations[] = array("11 Monroe Ave, Rochester, NY", "43.13839021469521", "-77.57854964593943");
	$locations[] = array("22 Hannahs Ter, Rochester, NY", "43.24364996413504", "-77.61969371525176");
	$locations[] = array("16 Oakview Dr, Rochester, NY", "43.21953080446419", "-77.58418410360882");

	if( isset($locations[$index]) && $locations[$index] ){
		$location = $locations[$index];
		$listing->setOption('address', $location[0]);
		$listing->setOption('latitude', $location[1]);
		$listing->setOption('longitude', $location[2]);
	}
	
	$listing->setOption('description', '<p>Nullam quis ante tiam sit amet orci eget eros faucibus tincidunt. Donec quam felis, ultricies nec, pellentesque eu, pretium quis, sem. Nulla consequat massa quis enim.</p>');

	// Set Floor Plan
	$floor_plans = '[{"id":"750_1609119527388","title":"Ground Floor","content":"","options":[{"id":"920_1609119552378","key":"Rooms","val":"2"},{"id":"860_1609119562067","key":"Baths","val":"1"},{"id":"30_1609119569387","key":"Size","val":"160 Sqft"}],"is_open":true,"shortcode":"[gv-page-content id=\"29\"]"},{"id":"820_1609119673122","title":"First Floor","content":"","options":[{"id":"1000_1609119732213","key":"Rooms","val":"2"},{"id":"910_1609119739011","key":"Baths","val":"1"},{"id":"80_1609119749551","key":"Size","val":"160 Sqft"}],"is_open":true,"shortcode":"[gv-page-content id=\"29\"]"}]';
	update_post_meta($listing_id, 'floor_plans', $floor_plans);

	// Set Price
	$price = '' . rand(10, 50) * 100;
	$listing->setOption('price', $price);
	$listing_attribute_relationships             = new uListing\Classes\StmListingAttributeRelationships();
	$listing_attribute_relationships->listing_id = $listing_id;
	$listing_attribute_relationships->attribute  = 'price';
	$listing_attribute_relationships->value      = $price;
	$listing_attribute_relationships->sort       = 1;
	$listing_attribute_relationships->save();
	$listing->getOptions('price')[0]->update_meta('genuine', $price);
	$listing_attribute_relationships->save();

	// set Amenities
	if( isset($_attrs_options['amenities']) && $_attrs_options['amenities'] ){
		$sort = 1;
		foreach ($_attrs_options['amenities'] as $key => $term_id) {
			$listing_attribute_relationships             = new uListing\Classes\StmListingAttributeRelationships();
			$listing_attribute_relationships->listing_id = $listing_id;
			$listing_attribute_relationships->attribute  = 'amenities';
			$listing_attribute_relationships->value      = $term_id;
			$listing_attribute_relationships->sort       = $sort;
			$listing_attribute_relationships->save();
			$sort++;
		}
	}

	// set bedrooms
	$listing_attribute_relationships             = new uListing\Classes\StmListingAttributeRelationships();
	$listing_attribute_relationships->listing_id = $listing_id;
	$listing_attribute_relationships->attribute  = 'bedrooms';
	$listing_attribute_relationships->value      = $_attrs_options['bedrooms'][rand(0, 5)];
	$listing_attribute_relationships->save();

	$listing_attribute_relationships             = new uListing\Classes\StmListingAttributeRelationships();
	$listing_attribute_relationships->listing_id = $listing_id;
	$listing_attribute_relationships->attribute  = 'bathrooms';
	$listing_attribute_relationships->value      = $_attrs_options['bathrooms'][rand(0, 5)];
	$listing_attribute_relationships->save();

	$listing_attribute_relationships             = new uListing\Classes\StmListingAttributeRelationships();
	$listing_attribute_relationships->listing_id = $listing_id;
	$listing_attribute_relationships->attribute  = 'garages';
	$listing_attribute_relationships->value      = $_attrs_options['garages'][rand(0, 5)];
	$listing_attribute_relationships->save();

	// set gallery
	$image_ids = ['129', '124', '125', '126', '127', '128', '255', '256', '250', '251' ];
	for ($i = 1; $i <= 6; $i++) {
		$listing_attribute_relationships             = new uListing\Classes\StmListingAttributeRelationships();
		$listing_attribute_relationships->listing_id = $listing_id;
		$listing_attribute_relationships->attribute  = 'gallery';
		$listing_attribute_relationships->value      = $image_ids[rand(0, 9)];
		$listing_attribute_relationships->sort       = $i;
		$listing_attribute_relationships->save();
	}
	$listing->save();
}

function tolips_themer_create_attribute($attribute_data){
	$upgrade  = false;
	$attr_options = [];
	if( $new_attribute = uListing\Classes\StmListingAttribute::query()->where("name", $attribute_data['name'])->findOne() )
		$upgrade = true;
	else{
		$new_attribute       = new uListing\Classes\StmListingAttribute();
		$new_attribute->name  = $attribute_data['name'];
		$new_attribute->type  = $attribute_data['type'];
	}
	$new_attribute       = new uListing\Classes\StmListingAttribute();
  	$new_attribute->name  = $attribute_data['name'];
  	$new_attribute->type  = $attribute_data['type'];
  	$new_attribute->title = $attribute_data['title'];
  	$new_attribute->thumbnail_id = $attribute_data['thumbnail_id'];
  	$new_attribute->affix = $attribute_data['affix'];
  	$new_attribute->icon  = $attribute_data['icon'];
	if($new_attribute->save()){
		if(!$upgrade AND $new_attribute AND isset($attribute_data['options']) AND $attribute_data['options'] ) {
			foreach ($attribute_data['options'] as $option){
				$term = wp_insert_term( $option['name'],
					"listing-attribute-options",
					[
						'slug' => $option['slug']
					]
				);
			  	$term = (array)$term;
			  	if(isset($term['term_id'])){
					$attribute_option_relation = new uListing\Classes\StmAttributeTermRelationships();
					$attribute_option_relation->attribute_id = $new_attribute->id;
					$attr_options[] = $term['term_id'];
					$attribute_option_relation->term_id = $term['term_id'];
					if($attribute_option_relation->save() AND $option['meta']){
					 	foreach ($option['meta'] as $key => $val){
						 	add_term_meta($attribute_option_relation->term_id, $key, $val);
					 	}
					}
			  	}
			}
		}
	}
	return $attr_options;
}


function tolips_themer_create_agent() {
	$agent_id = array();
   if (!get_role("agent")) {
   	add_role("agent", "Agent", [
       	"default"                 => 0,
       	"listing_limit"           => "0",
       	"comment"                 => "1",
       	"listing_moderation"      => "0",
       	"stm_listing_role"        => "1",
   	]);
   }
   if (!get_role("agency")) {
      add_role("agency", "Agency", [
         "default"            => 0,
         "listing_limit"      => "0",
         "comment"            => "1",
         "listing_moderation" => "1",
         "stm_listing_role"   => "1",
      ]);
   }

   $user_login = ['Tolips Agent', 'Tolips Agent 2', 'Tolips Agent 3'];
   $user_email = ['agent_demo@example.com', 'agent_demo_2@example.com', 'agent_demo_3@example.com'];
   $user_nickname = ['Jessica Brown', 'Kevin Martin', 'David Cooper'];

   for ( $i = 0; $i < 3; $i++ ){
      $user_data       = array(
         'user_login' => $user_login[$i],
         'user_pass'  => 'demo',
         'user_email' => $user_email[$i],
         'role'       => 'user'
      );
      $user_id = wp_insert_user($user_data);
      $agent_id[] = $user_id;
      $user = get_user_by('ID', $user_id);
      update_user_meta($user->ID, 'nickname', $user_nickname[$i]);
      update_user_meta($user->ID, 'phone_mobile', '888 666 000');
      update_user_meta($user->ID, 'phone_office', '92 655 788');
      update_user_meta($user->ID, 'fax', '92 655 788');
      update_user_meta($user->ID, 'license', '12688049886');
      update_user_meta($user->ID, 'tax_number', '123-45-6789');

      update_user_meta($user->ID, 'facebook', '#');
      update_user_meta($user->ID, 'twitter', '#');
      update_user_meta($user->ID, 'instagram', '#');
      update_user_meta($user->ID, 'linkedin', '#');

      update_user_meta($user->ID, 'address', '277 Alexander St, Rochester NY');
      update_user_meta($user->ID, 'latitude', '43.0912656378141');
      update_user_meta($user->ID, 'longitude', '-77.5170291594334');
      $user_avatar = wp_get_upload_dir();
      update_user_meta($user->ID, 'stm_listing_avatar', [
          'url' => $user_avatar['baseurl']. '/2020/12/team-1.jpg'
      ]);
      wp_update_user($user);
   }

   return $agent_id;
}


