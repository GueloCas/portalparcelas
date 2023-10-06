<?php

$contact_results = array();

if( isset($_GET['page']) && $_GET['page'] == 'theme-options' ){
	$contact_post = get_posts(
		array(
			'post_type'   => 'wpcf7_contact_form',
	    	'post_status' => 'publish',
	    	'numberposts' => -1
	 	)
	);

	foreach ($contact_post as $post) {
		$contact_results[$post->ID] = $post->post_title;
	}
}

Redux::setSection( $opt_name, array(
	'title' 	=> esc_html__('Listings General', 'tolips'),
	'icon' 	=> 'el-icon-wrench',
	'fields' => array(
		array(
			'id'        => 'lt_default_business_hours',
			'type'      => 'select',
			'title'     => esc_html__('Status Open/Close when unset Business Hours', 'tolips'),
			'options'   => array(
				'open'         => esc_html__('Open', 'tolips'),
				'closed'       => esc_html__('Closed', 'tolips'),
				'hidden'       => esc_html__('Hidden Status', 'tolips')
			),
			'default' => 'open'
		),
		array(
		  'id'            => 'lt_reviews',
		  'type'          => 'multi_text',
		  'title'         => esc_html__('Listing Review Item / Title[key]', 'tolips'),
		  'subtitle'      => esc_html__('Example: Quality[quality], Format: Title[key]', 'tolips'),
		  'default'       => array('Quality[quality]', 'Hospitality[hospitality]', 'Service[service]', 'Pricing[price]')
		),
		array(
			'id'        => 'lt_review_allow_owner',
			'type'      => 'select',
			'title'     => esc_html__('Allow listing owner review', 'tolips'),
			'desc'      => esc_html__('Allow listing owners to review their own listings.', 'tolips'),
			'options'   => array(
				'enable'         => esc_html__('Enable', 'tolips'),
				'disable'        => esc_html__('Disable', 'tolips'),
			),
			'default' => 'enable'
		),
		array(
			'id'        => 'contact_form_agent',
			'type'      => 'select',
			'title'     => esc_html__('Contact Form Agent', 'tolips'),
			'options'   => $contact_results,
			'default' 	=> '295'
		),
	)
));