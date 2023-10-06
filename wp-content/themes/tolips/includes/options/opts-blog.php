<?php
Redux::setSection( $opt_name, array(
	'title'     	=> esc_html__( 'Blog Options', 'tolips' ),
	'icon'      	=> 'el-icon-website',
	'fields'		 	=> array(
		array(
		  'id' 	 	=> 'blog_archive_info',
		  'type'  	=> 'info',
		  'icon'  	=> true,
		  'raw' 	 	=> '<h3 style="margin: 0;">' . esc_html__( 'Archive/Listing', 'tolips' ) . '</h3>',
		),
		array(
		  'id' 		=> 'blog_columns_lg',
		  'type' 	=> 'select',
		  'title' 	=> esc_html__('Display Columns for Large Screen', 'tolips'),
		  'options' => array(
			  '1'      => '1',
			  '2'      => '2',
			  '3'      => '3',
			  '4'      => '4',
			  '5'      => '5',
			  '6'      => '6',
		  ),
		  'default' => '2'
		),
		array(
		  'id' 		=> 'blog_columns_md',
		  'type' 	=> 'select',
		  'title' 	=> esc_html__('Display Columns for Medium Screen', 'tolips'),
		  'options' => array(
			  '1'      => '1',
			  '2'      => '2',
			  '3'      => '3',
			  '4'      => '4',
			  '5'      => '5',
			  '6'      => '6',
		  ),
		  'default' => '2'
		),
		array(
		  'id' 		=> 'blog_columns_sm',
		  'type' 	=> 'select',
		  'title' 	=> esc_html__('Display Columns for Small Screen', 'tolips'),
		  'options' => array(
			  '1'      => '1',
			  '2'      => '2',
			  '3'      => '3',
			  '4'      => '4',
			  '5'      => '5',
			  '6'      => '6',
		  ),
		  'default' => '1'
		),
		array(
		  'id' 		=> 'blog_columns_xs',
		  'type' 	=> 'select',
		  'title' 	=> esc_html__('Display Columns for Extra Small Screen', 'tolips'),
		  'options' => array(
			  '1'      => '1',
			  '2'      => '2',
			  '3'      => '3',
			  '4'      => '4',
			  '5'      => '5',
			  '6'      => '6',
		  ),
		  'default' => '1'
		),
		array(
		  'id' 			=> 'archive_post_sidebar',
		  'type' 		=> 'select',
		  'title' 		=> esc_html__('Default Archive Page Blog Sidebar Config', 'tolips'),
		  'subtitle' 	=> esc_html__('Choose single post layout.', 'tolips'),
		  'options' 	=> array(
			 'no-sidebars'     => esc_html__('No Sidebars', 'tolips'),
			 'left-sidebar'    => esc_html__('Left Sidebar', 'tolips'),
			 'right-sidebar'   => esc_html__('Right Sidebar', 'tolips')
		  ),
		  'default' => 'no-sidebars'
		),
	  	array(
			'id' 			=> 'archive_post_left_sidebar',
			'type' 		=> 'select',
			'title'	 	=> esc_html__('Default Archive Page Blog Left Sidebar', 'tolips'),
			'subtitle' 	=> esc_html__('Choose the default left sidebar for Single Post.', 'tolips'),
			'data'      => 'sidebars',
			'default' 	=> 'blog_sidebar'
	  	),
		array(
			'id' 			=> 'archive_post_right_sidebar',
			'type' 		=> 'select',
			'title' 		=> esc_html__('Default Archive Page Blog Right Sidebar', 'tolips'),
			'subtitle' 	=> esc_html__('Choose the default right sidebar for Single Post.', 'tolips'),
			'data'      => 'sidebars',
			'default' 	=> 'blog_sidebar'
	 	),
	 	array(
		  'id'    		=> 'blog_excerpt_limit',
		  'type'    	=> 'text',
		  'title'   	=> esc_html__( 'Blog Excerpt Limit', 'tolips' ),
		  'default' 	=> '30',
		),
		array(
			'id'  		=> 'blog_single_post_info',
			'type'   	=> 'info',
			'icon'   	=> true,
			'raw' 		=> '<h3 style="margin: 0;">' . esc_html__( 'Single Post', 'tolips' ) . '</h3>',
		),
		array(
		  	'id' 			=> 'single_post_sidebar',
		  	'type' 		=> 'select',
		  	'title' 		=> esc_html__('Default Single Sidebar Config', 'tolips'),
		  	'subtitle' 	=> esc_html__('Choose single post layout.', 'tolips'),
		  	'options' 	=> array(
			  		'no-sidebars'     => esc_html__('No Sidebars', 'tolips'),
				 	'left-sidebar'    => esc_html__('Left Sidebar', 'tolips'),
				 	'right-sidebar'   => esc_html__('Right Sidebar', 'tolips')
		  	),
		  	'default' => 'no-sidebars'
		),
		array(
		  	'id' 			=> 'single_post_left_sidebar',
		  	'type' 		=> 'select',
		  	'title' 		=> esc_html__('Default Single Left Sidebar', 'tolips'),
		  	'subtitle' 	=> esc_html__('Choose the default left sidebar for Single Post.', 'tolips'),
		  	'data'      => 'sidebars',
		  	'default' 	=> 'blog_sidebar'
		),
		 array(
		  	'id' 			=> 'single_post_right_sidebar',
		  	'type' 		=> 'select',
		  	'title' 		=> esc_html__('Default Single Right Sidebar', 'tolips'),
		  	'subtitle' 	=> esc_html__('Choose the default right sidebar for Single Post.', 'tolips'),
		  	'data'      => 'sidebars',
		  	'default' 	=> 'blog_sidebar'
		)
	)
) );