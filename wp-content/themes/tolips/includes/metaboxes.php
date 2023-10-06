<?php
function tolips_register_meta_boxes(){

	$prefix = 'tolips_';
	global $meta_boxes, $wp_registered_sidebars;;
	$sidebar = array();
	$sidebar[""] = ' --Default-- ';
	foreach($wp_registered_sidebars as $key => $value){
		$sidebar[$value['id']] = $value['name'];
	}
	$default_options = get_option('tolips_options');
	
	$meta_boxes = array();

	/* Thumbnail Meta Box
	================================================== */
	$meta_boxes[] = array(
		'id' 			=> 'gavias_metaboxes_post_thumbnail',
		'title' 		=> esc_html__('Thumbnail', 'tolips'),
		'pages' 		=> array('post'),
		'context' 	=> 'normal',
		'fields' 	=> array(
			
			// THUMBNAIL IMAGE
			array(
				'name'  => esc_html__('Thumbnail image', 'tolips'),
				'desc'  => esc_html__('The image that will be used as the thumbnail image.', 'tolips'),
				'id'    => "{$prefix}thumbnail_image",
				'type'  => 'image_advanced',
				'max_file_uploads' => 1
			),

			// THUMBNAIL VIDEO
			array(
				'name' 	=> esc_html__('Thumbnail video URL', 'tolips'),
				'id' 		=> $prefix . 'thumbnail_video_url',
				'desc' 	=> esc_html__('Enter the video url for the thumbnail. Only links from Vimeo & YouTube are supported.', 'tolips'),
				'clone' 	=> false,
				'type'  	=> 'oembed',
				'std' 	=> '',
			),

			// THUMBNAIL AUDIO
			array(
				'name' 	=> esc_html__('Thumbnail audio URL', 'tolips'),
				'id' 		=> "{$prefix}thumbnail_audio_url",
				'desc' 	=> esc_html__('Enter the audio url for the thumbnail.', 'tolips'),
				'clone' 	=> false,
				'type'  	=> 'oembed',
				'std' 	=> '',
			),

			// THUMBNAIL GALLERY
			array(
				'name'             => esc_html__('Thumbnail gallery', 'tolips'),
				'desc'             => esc_html__('The images that will be used in the thumbnail gallery.', 'tolips'),
				'id'               => "{$prefix}thumbnail_gallery",
				'type'             => 'image_advanced',
				'max_file_uploads' => 50,
			),

			// THUMBNAIL LINK TYPE
			array(
				'name' => esc_html__('Thumbnail link type', 'tolips'),
				'id'   => "{$prefix}thumbnail_link_type",
				'type' => 'select',
				'options' => array(
					'link_to_post'    => esc_html__('Link to item', 'tolips'),
					'link_to_url'     => esc_html__('Link to URL', 'tolips'),
					'link_to_url_nw'  => esc_html__('Link to URL (New Window)', 'tolips'),
					'lightbox_thumb'  => esc_html__('Lightbox to the thumbnail image', 'tolips'),
					'lightbox_image'  => esc_html__('Lightbox to image (select below)', 'tolips'),
					'lightbox_video'  => esc_html__('Fullscreen Video Overlay (input below)', 'tolips')
				),
				'multiple' => false,
				'std'  => 'link-to-post',
				'desc' => esc_html__('Choose what link will be used for the image(s) and title of the item.', 'tolips')
			),

			// THUMBNAIL LINK URL
			array(
				'name' 	=> esc_html__('Thumbnail link URL', 'tolips'),
				'id' 		=> $prefix . 'thumbnail_link_url',
				'desc' 	=> esc_html__('Enter the url for the thumbnail link.', 'tolips'),
				'clone' 	=> false,
				'type'  	=> 'text',
				'std' 	=> '',
			),

			// THUMBNAIL LINK LIGHTBOX IMAGE
			array(
				'name'  => esc_html__('Thumbnail link lightbox image', 'tolips'),
				'desc'  => esc_html__('The image that will be used as the lightbox image.', 'tolips'),
				'id'    => "{$prefix}thumbnail_link_image",
				'type'  => 'thickbox_image'
			),
		)
	);

	 /* Page Meta Box
	================================================== */
	$meta_boxes[] = array(
		'id'    => 'gavias_metaboxes_page',
		'title' => esc_html__('Page Meta', 'tolips'),
		'pages' => array('page', 'portfolio', 'product', 'post', 'service'),
		'priority'   => 'high',
		'fields' => array(
			// Full width
			array(
				'name' => esc_html__('Full Width( 100% Main Width )', 'tolips'),
				'id'   => "{$prefix}page_full_width",
				'type' => 'switch',
				'desc' => esc_html__('Turn on to have the main area display at 100% width according to the window size. Turn off to follow site width.', 'tolips'),
				'std'  => 0,
			),
			// Extra Page Class
			array(
				'name' 		=> esc_html__('Header', 'tolips'),
				'id' 			=> $prefix . 'page_header',
				'desc' 		=> esc_html__("You can change header for page if you like's.", 'tolips'),
				'type'  		=> 'select',
				'options'   => tolips_get_headers(),
				'std' 		=> '__default_option_theme',
			),
			array(
				'name'      => esc_html__('Footer', 'tolips'),
				'id'        => $prefix . 'page_footer',
				'desc'      => esc_html__("You can change footer for page if you like's",'tolips'),
				'type'      => 'select',
				'options'   =>  tolips_get_footer(),
				'std'       => '__default_option_theme'
			),
			// Extra Page Class
			array(
				'name' 	=> esc_html__('Extra page class', 'tolips'),
				'id' 		=> $prefix . 'extra_page_class',
				'desc' 	=> esc_html__("If you wish to add extra classes to the body class of the page (for custom css use), then please add the class(es) here.", 'tolips'),
				'clone' 	=> false,
				'type'  	=> 'text',
				'std' 	=> '',
			),
		  
		)
	);

	/* Page Title Meta Box
	================================================== */
	$meta_boxes[] = array(
		'id' 			=> 'gavias_metaboxes_page_heading',
		'title' 		=> esc_html__('Page Title & Breadcrumb', 'tolips'),
		'pages' 		=> array('post', 'page', 'product', 'portfolio', 'gallery', 'tribe_events', 'gva_team', 'service'),
		'context' 	=> 'normal',
		'priority'  => 'high',
		'fields' => array(
		  array(
			 'name' => esc_html__('Remove Title of Page', 'tolips'),   
			 'id'   => "{$prefix}disable_page_title",
			 'type' => 'switch',
			 'std'  => 1,
		  ),
		  array(
			 'name' => esc_html__('Disable Breadcrumbs', 'tolips'),
			 'id'   => "{$prefix}no_breadcrumbs",
			 'type' => 'switch',
			 'desc' => esc_html__('Disable the breadcrumbs from under the page title on this page.', 'tolips'),
			 'std'  => 0,
		  ),
		  array(
			 'name' => esc_html__('Breadcrumb Layout', 'tolips'),
			 'id'   => "{$prefix}breadcrumb_layout",
			 'type' => 'select',
			 'options' => array(
				 'theme_options'     => esc_html__('Default Options in Theme-Settings', 'tolips'),
				 'page_options'      => esc_html__('Individuals Options This Page', 'tolips')
			 ),
			 'multiple' => false,
			 'std'  => 'theme_options',
			 'desc' => esc_html__('You can use breadcrumb settings default in Theme-Settings or individuals this page', 'tolips')
		  ),
		  array(
			 'name'    => esc_html__('Show page title in the breadcrumbs', 'tolips'),   
			 'id'      => "{$prefix}page_title",
			 'type'    => 'switch',
			 'std'     => 1,
			 'class'   => 'breadcrumb_setting'
		  ),
		  array(
			 'name' 		=> esc_html__('Page Title Override', 'tolips'),
			 'id' 		=> $prefix . 'page_title_one',
			 'desc' 		=> esc_html__("Enter a custom page title if you'd like.", 'tolips'),
			 'type'  	=> 'text',
			 'std' 		=> '',
			 'class'   	=> 'breadcrumb_setting'
		  ),
		  array(
			 'name'        => esc_html__('Breadcrumb Padding Top (px)', 'tolips'),
			 'id'          => "{$prefix}breadcrumb_padding_top",
			 'type'        => 'number',
			 'prefix'      => '',
			 'class'       => 'breadcrumb_setting',
			 'desc'        => esc_html__('Option Padding Top of Breacrumb, set empty = padding default of theme', 'tolips'),
			 'std'         => tolips_get_option('breadcrumb_padding_top', '135'),
		  ),
		  array(
			 'name'       => esc_html__('Breadcrumb Padding Bottom (px)', 'tolips'),
			 'id'         => "{$prefix}breadcrumb_padding_bottom",
			 'type'       => 'number',
			 'prefix'     => 'px',
			 'class'      => 'breadcrumb_setting',
			 'desc'       => esc_html__('Option Padding Bottom of Breacrumb, set empty = padding default of theme', 'tolips'),
			 'std'        => tolips_get_option('breadcrumb_padding_bottom', '135'),
		  ),
		  array(
			 'name' 	=> esc_html__('Background Overlay Color', 'tolips'),
			 'id'   	=> "{$prefix}bg_color_title",
			 'desc' 	=> esc_html__( "Set an overlay color for hero heading image.", 'tolips'),
			 'type' 	=> 'color',
			 'class' => 'breadcrumb_setting',
			 'std'  	=> '',
		  ),
		  array(
			 'name'       => esc_html__('Overlay Opacity', 'tolips'),
			 'id'         => "{$prefix}bg_opacity_title",
			 'desc'       => esc_html__('Set the opacity level of the overlay. This will lighten or darken the image depening on the color selected.', 'tolips'),
			 'clone'      => false,
			 'type'       => 'slider',
			 'prefix'     => '',
			 'class'   => 'breadcrumb_setting',
			 'js_options' => array(
				  'min'  => 0,
				  'max'  => 100,
				  'step' => 1,
			 ),
			 'std'   => '50'
		  ),
		  array(
			 'name'    => esc_html__('Enable Breadcrumb Image', 'tolips'),
			 'id'      => "{$prefix}image_breadcrumbs",
			 'type'    => 'switch',
			 'class'   => 'breadcrumb_setting',
			 'std'     => 1,
		  ),
		  array(
			 'name'  => esc_html__('Breadcrumb Background Image', 'tolips'),
			 'id'    => "{$prefix}page_title_image",
			 'type'  => 'image_advanced',
			 'class' => 'breadcrumb_setting',
			 'max_file_uploads' => 1
		  ),
		  array(
			 'name' => esc_html__('Heading Text Style', 'tolips'),
			 'id'   => "{$prefix}page_title_text_style",
			 'type' => 'select',
			 'class'   => 'breadcrumb_setting',
			 'options' => array(
				 'text-light'     => esc_html__('Light', 'tolips'),
				 'text-dark'      => esc_html__('Dark', 'tolips')
			 ),
			 'multiple' => false,
			 'std'  => tolips_get_option('breadcrumb_text_stype', 'text-dark'),
			 'desc' => esc_html__('If you uploaded an image in the option above, choose light/dark styling for the text heading text here.', 'tolips')
		  ),
		  array(
			 'name' => esc_html__('Heading Text Align', 'tolips'),
			 'id'   => "{$prefix}page_title_text_align",
			 'type' => 'select',
			 'class'   => 'breadcrumb_setting',
			 'options' => array(
				 'text-left'      => esc_html__('Left', 'tolips'),
				 'text-center'    => esc_html__('Center', 'tolips'),
				 'text-right'     => esc_html__('Right', 'tolips')
			 ),
			 'multiple' => false,
			 'std'  => tolips_get_option('breadcrumb_text_align', 'text-center'),
			 'desc' => esc_html__('Choose the text alignment for the hero heading.', 'tolips')
		  ),
		)
	);

	/* Brands Meta Box
	================================================== */
	$meta_boxes[] = array(
		'id'    => 'gavias_metaboxes_brands_options',
		'title' => esc_html__('Brands Options', 'tolips'),
		'pages' => array('brands'),
		'priority'   => 'high',
		'fields' => array(
			// LEFT SIDEBAR
			array (
				'name'   => esc_html__('Url Link', 'tolips'),
				 'id'    => "{$prefix}url_link",
				 'type' => 'text',
				 'std'   => ''
			),
		)
	);

	/* Sidebar Meta Box Page
	================================================== */
	$meta_boxes[] = array(
		'id'    => 'gavias_metaboxes_sidebar_page',
		'title' => esc_html__('Sidebar Options', 'tolips'),
		'pages' => array('post', 'page', 'portfolio', 'gallery', 'tribe_events', 'service'),
		'priority'   => 'high',
		'fields' => array(

			// SIDEBAR CONFIG
			array(
				'name' => esc_html__('Sidebar configuration', 'tolips'),
				'id'   => "{$prefix}sidebar_config",
				'type' => 'select',
				'options' => array(
				  ''                   => esc_html__('--Default--', 'tolips'),
				  'no-sidebars'        => esc_html__('No Sidebars', 'tolips'),
				  'left-sidebar'       => esc_html__('Left Sidebar', 'tolips'),
				  'right-sidebar'      => esc_html__('Right Sidebar', 'tolips'),
				),
				'multiple' => false,
				'std'  => '',
				'desc' => esc_html__('Choose the sidebar configuration for the detail page of this page.', 'tolips'),
			),

			// LEFT SIDEBAR
			array (
				'name'   	=> esc_html__('Left Sidebar', 'tolips'),
				 'id'    	=> "{$prefix}left_sidebar",
				 'type' 		=> 'select',
				 'options'  => $sidebar,
				 'std'   	=> ''
			),

			// RIGHT SIDEBAR
			array (
				'name'   	=> esc_html__('Right Sidebar', 'tolips'),
				'id'    		=> "{$prefix}right_sidebar",
				'type' 		=> 'select',
				'options'  	=> $sidebar,
				'std'   		=> ''
			),
		)
	);

  /* Gallery Meta Box 
  ================================================== */
  $meta_boxes[] = array(
	 'id'    => 'metaboxes_portfolio_page',
	 'title' => esc_html__('Portfolio Settings', 'tolips'),
	 'pages' => array('portfolio'),
	 'priority'   => 'high',
	 'fields' => array(
		array (
		  'name'   => esc_html__('Gallery Images', 'tolips'),
		  'id'    => "{$prefix}gallery_images",
		  'type'             => 'image_advanced',
		  'max_file_uploads' => 50,
		),
	 )
  );


  $meta_boxes[] = array(
	 'id'    => 'metaboxes_gallery_page',
	 'title' => esc_html__('Gallery Settings', 'tolips'),
	 'pages' => array('gallery'),
	 'priority'   => 'high',
	 'fields' => array(
		array (
		  'name'   => esc_html__('Gallery Images', 'tolips'),
		  'id'    => "{$prefix}gallery_images",
		  'type'             => 'image_advanced',
		  'max_file_uploads' => 50,
		),
	 )
  );

  /* Service Meta Box 
	================================================== */
	$meta_boxes[] = array(
		'id'    => 'metaboxes_service_page',
		'title' => esc_html__('Service Link Settings', 'tolips'),
		'pages' => array('service'),
		'priority' => 'low',
		'fields' => array(
		  array (
			 'name'   => esc_html__('Gallery Images', 'tolips'),
			 'id'    => "{$prefix}gallery_images",
			 'type'             => 'image_advanced',
			 'max_file_uploads' => 50,
		  ),
		  array(
			 'name' => esc_html__('Show Gallery Top Service Single Page', 'tolips'),
			 'id'   => "{$prefix}show_service_gallery",
			 'type' => 'switch',
			 'std' => 0,
		  ),
		  array (
			 'name'    => esc_html__('Extra Link', 'tolips'),
			 'id'      => "{$prefix}service_extra_link",
			 'type'    => 'text'
		  ),
		)
	);

	$map_api_key = tolips_get_option('map_api_key', 'AIzaSyChkvQkXo_61RR7u-XJOj-rLF9ekk9eRYc'); 
	 /* Event Meta Box 
	================================================== */

  $meta_boxes[] = array(
	 'id'    => 'metaboxes_team_page',
	 'title' => esc_html__('Team Settings', 'tolips'),
	 'pages' => array('gva_team'),
	 'priority'   => 'high',
	 'fields' => array(
		array (
		  'name'   	=> esc_html__('Position', 'tolips'),
		  'id'    	=> "{$prefix}team_position",
		  'type' 	=> 'text',
		  'std'   	=> ''
		),
		array (
		  'name'   	=> esc_html__('Quote', 'tolips'),
		  'id'    	=> "{$prefix}team_quote",
		  'type' 	=> 'textarea',
		  'std'   	=> ''
		),
		array (
		  'name'   	=> esc_html__('Email', 'tolips'),
		  'id'    	=> "{$prefix}team_email",
		  'type' 	=> 'text',
		  'std'   	=> ''
		),
		array (
		  'name'   	=> esc_html__('Phone', 'tolips'),
		  'id'    	=> "{$prefix}team_phone",
		  'type' 	=> 'text',
		  'std'   	=> ''
		),
		array (
		  'name'   	=> esc_html__('Address', 'tolips'),
		  'id'    	=> "{$prefix}team_address",
		  'type' 	=> 'text',
		  'std'   	=> ''
		),
	 )
  );

  $meta_boxes[] = array(
	 'id'    => 'metaboxes_header_builder',
	 'title' => esc_html__('Header Builder', 'tolips'),
	 'pages' => array('gva_header'),
	 'priority' => 'high',
	 'fields' => array(
		array(
		  'name' => esc_html__('Enable Background Black', 'tolips'),
		  'id'   => "{$prefix}header_bg_black",
		  'type' => 'switch',
		  'desc' => esc_html__('It will display background black when builder header.', 'tolips'),
		  'std'  => 0,
		),
		array(
		  'name' => esc_html__('Full Width( 100% Main Width )', 'tolips'),
		  'id'   => "{$prefix}header_full_width",
		  'type' => 'switch',
		  'desc' => esc_html__('Turn on to have the main area display at 100% width according to the window size. Turn off to follow site width.', 'tolips'),
		  'std'  => 0,
	  ),
		array(
		  'name' => esc_html__('Position Styling', 'tolips'),
		  'id'   => "{$prefix}header_position",
		  'type' => 'select',
		  'options' => array(
			 'relative'      => esc_html__('Relative', 'tolips'),
			 'absolute'      => esc_html__('Absolute', 'tolips'),
		  ),
		  'std' => 'relative',
		  'multiple' => false,
		),
	 )
  );

	return $meta_boxes;
 }  
  /********************* META BOX REGISTERING ***********************/
  add_filter('rwmb_meta_boxes', 'tolips_register_meta_boxes' , 99 );

