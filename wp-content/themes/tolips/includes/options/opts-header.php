<?php
  	Redux::setSection( $opt_name, array(
	 	'title' 	=> esc_html__('Header Options', 'tolips'),
	 	'icon' 	=> 'el-icon-compass',
	 	'fields' => array(
			array(
			  'id' 			=> 'header_layout',
			  'type' 		=> 'select',
			  'title' 		=> esc_html__('Header Layout', 'tolips'),
			  'subtitle' 	=> esc_html__('Select a header layout option from the examples.', 'tolips'),
			  'options' 	=> tolips_get_headers(false),
			  'default' 	=> 'header-1'
			),
			array(
			  'id' 			=> 'header_logo', 
			  'type' 		=> 'media',
			  'url' 			=> true,
			  'title' 		=> esc_html__('Logo in header default', 'tolips'), 
			  'default' 	=> ''
			),  
			array(
			  'id'  			=> 'header_mobile_settings',
			  'type'  		=> 'info',
			  'raw' 			=> '<h3 style="margin: 0;">' . esc_html__( 'Header Mobile settings', 'tolips' ) . '</h3>'
			),
			array(
			  'id' 			=> 'hm_logo',
			  'type' 		=> 'media',
			  'url' 			=> true,
			  'title' 		=> esc_html__('Header Mobile | Logo', 'tolips'),
			  'default' 	=> ''
			),
			array(
			  	'id' 			=> 'hm_show_user',
			  	'type' 		=> 'button_set',
			  	'title' 		=> esc_html__('Show User', 'tolips'),
			  	'options' 	=> array(
			  		'yes' 	=> esc_html__('Enable', 'tolips'),
        			'no' 		=> esc_html__('Disable', 'tolips')
			  	),
			  'default' 	=> 'yes'
			),
			//-- Socials --
			array(
			  'id'  			=> 'header_mobile_socials_settings',
			  'type'  		=> 'info',
			  'raw'		 	=> '<h3 style="margin: 0;">' . esc_html__( 'Social Header Mobile Settings', 'tolips' ) . '</h3>'
			),
			array(
				'id'			=> 'hm_social_facebook',
				'type' 		=> 'text',
				'title' 		=> esc_html__( 'Facebook', 'tolips' ),
				'desc'		=> esc_html__( 'Enter your Facebook profile URL.', 'tolips' ),
				'default'	=> ''
			),
			array(
				'id'			=> 'hm_social_instagram',
				'type'		=> 'text',
				'title'		=> esc_html__( 'Instagram', 'tolips' ),
				'desc'		=> esc_html__( 'Enter your Instagram profile URL.', 'tolips' ),
				'default'	=> ''
			),
			array(
				'id'			=> 'hm_social_twitter',
				'type'		=> 'text',
				'title'		=> esc_html__( 'Twitter', 'tolips' ),
				'desc'		=> esc_html__( 'Enter your Twitter profile URL.', 'tolips' ),
				'default'	=> ''
			),
			array(
				'id'			=> 'hm_social_linkedin',
				'type'		=> 'text',
				'title'		=> esc_html__( 'LinedIn', 'tolips' ),
				'desc'		=> esc_html__( 'Enter your LinkedIn profile URL.', 'tolips' ),
				'default'	=> ''
			),
			array(
				'id'			=> 'hm_social_pinterest',
				'type'		=> 'text',
				'title'		=> esc_html__( 'Pinterest', 'tolips' ),
				'desc'		=> esc_html__( 'Enter your Pinterest profile URL.', 'tolips' ),
				'default'	=> ''
			),
			array(
				'id'			=> 'hm_social_tumblr',
				'type'		=> 'text',
				'title'		=> esc_html__( 'Tumblr', 'tolips' ),
				'desc'		=> esc_html__( 'Enter your Tumblr profile URL.', 'tolips' ),
				'default'	=> ''
			),
			array(
				'id'			=> 'hm_social_vimeo',
				'type'		=> 'text',
				'title'		=> esc_html__( 'Vimeo', 'tolips' ),
				'desc'		=> esc_html__( 'Enter your Vimeo profile URL.', 'tolips' ),
				'default'	=> ''
			),
			array(
				'id'			=> 'hm_social_youtube',
				'type'		=> 'text',
				'title'		=> esc_html__( 'YouTube', 'tolips' ),
				'desc'		=> esc_html__( 'Enter your YouTube profile URL.', 'tolips' ),
				'default'	=> ''
			)
	 	)
  	));