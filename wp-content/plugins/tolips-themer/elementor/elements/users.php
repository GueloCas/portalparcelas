<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}
use Elementor\Controls_Manager;
use Elementor\Scheme_Color;
use Elementor\Group_Control_Typography;
use Elementor\Scheme_Typography;
use Elementor\Group_Control_Border;
use Elementor\Group_Control_Image_Size;

/**
 * Elementor heading widget.
 *
 * Elementor widget that displays an eye-catching headlines.
 *
 * @since 1.0.0
 */
class GVAElement_Users extends GVAElement_Base {

	/**
	 * Get widget name.
	 *
	 * Retrieve heading widget name.
	 *
	 * @since 1.0.0
	 * @access public
	 *
	 * @return string Widget name.
	 */
	public function get_name() {
		return 'gva-users-agents';
	}

	/**
	 * Get widget title.
	 *
	 * Retrieve heading widget title.
	 *
	 * @since 1.0.0
	 * @access public
	 *
	 * @return string Widget title.
	 */
	public function get_title() {
		return __( 'GVA Users/Agents', 'tolips-themer' );
	}

	/**
	 * Get widget icon.
	 *
	 * Retrieve heading widget icon.
	 *
	 * @since 1.0.0
	 * @access public
	 *
	 * @return string Widget icon.
	 */
	public function get_icon() {
		return 'eicon-lock-user';
	}

	/**
	 * Get widget keywords.
	 *
	 * Retrieve the list of keywords the widget belongs to.
	 *
	 * @since 2.1.0
	 * @access public
	 *
	 * @return array Widget keywords.
	 */
	public function get_keywords() {
		return [ 'agents', 'users', 'block' ];
	}

	public function get_script_depends() {
      return [
          'jquery.owl.carousel',
          'gavias.elements',
      ];
    }

    public function get_style_depends() {
      return array('owl-carousel-css');
    }

	/**
	 * Register heading widget controls.
	 *
	 * Adds different input fields to allow the user to change and customize the widget settings.
	 *
	 * @since 1.0.0
	 * @access protected
	 */
	protected function register_controls() {

		$this->start_controls_section(
			'section_content',
			[
				'label' => __( 'Content', 'tolips-themer' ),
			]
		);
		
		$this->add_control(
			'user_role',
			[
				'label'   => esc_html__('Filter by role', 'tolips-themer'),
				'type'    => Controls_Manager::SELECT,
				'options' => [
					''       => esc_html__('All', 'tolips-themer'),
					'agency' => esc_html__('Agency', 'tolips-themer'),
					'agent'  => esc_html__('Agent', 'tolips-themer'),
					'user'   => esc_html__('User', 'tolips-themer'),
				],
				'default' => 'agent',
			]
		);

		$this->add_control(
			'per_page',
			[
				'label'   => esc_html__('Per page', 'tolips-themer'),
				'type'    => \Elementor\Controls_Manager::TEXT,
				'default' => '12',
			]
		);

		$this->add_control(
			'orderby',
			[
				'label'   => esc_html__('Order By', 'tolips-themer'),
				'type'    => Controls_Manager::SELECT,
				'default' => 'registered',
				'options' => [
				  'registered'   => esc_html__('Registered date', 'tolips-themer'),
				  'nicename'     => esc_html__('Nickname', 'tolips-themer'),
				  'ID'           => esc_html__('ID', 'tolips-themer'),
				  'display_name' => esc_html__('Display name', 'tolips-themer'),
				  'post_count'   => esc_html__('Post count', 'tolips-themer'),
				],
			]
		);

		$this->add_control(
			'order',
			[
				'label'   => esc_html__('Order', 'tolips-themer'),
				'type'    => Controls_Manager::SELECT,
				'default' => 'desc',
				'options' => [
					'asc'  => esc_html__('ASC', 'tolips-themer'),
					'desc' => esc_html__('DESC', 'tolips-themer'),
				],
			]
		);

		$this->add_control(
			'show_paginate',
			[
				'label'   => esc_html__('Show Paginate', 'tolips-themer'),
				'type'    => Controls_Manager::SWITCHER,
				'default' => 'no'
			]
		 );  

		$this->end_controls_section();

		$this->start_controls_section(
			'section_team_layout',
			[
				'label' => __('Layout', 'tolips-themer'),
				'type'  => Controls_Manager::SECTION,
			]
		);
		$this->add_control(
			'layout',
			[
				'label'   => __( 'Layout Display', 'tolips-themer' ),
				'type'    => Controls_Manager::SELECT,
				'default' => 'grid',
				'options' => [
					'grid'      => __( 'Grid', 'tolips-themer' ),
					'carousel'  => __( 'Carousel', 'tolips-themer' ),
					'list'  		=> __( 'List', 'tolips-themer' ),
				]
			]
		);

		$this->end_controls_section();

		$this->add_control_carousel(false, array('layout' => 'carousel'));

		$this->add_control_grid(array('layout' => 'grid'));

	}

	/**
	 * Render heading widget output on the frontend.
	 *
	 * Written in PHP and used to generate the final HTML.
	 *
	 * @since 1.0.0
	 * @access protected
	 */
	protected function render() {
		$settings = $this->get_settings_for_display();

		$layout    = $settings['layout'];
		$user_role = $settings['user_role'];
		$per_page  = $settings['per_page'];

		  if (is_front_page()) {
				$paged = (get_query_var('page')) ? get_query_var('page') : 1;
		  } else {
				$paged = (get_query_var('paged')) ? get_query_var('paged') : 1;
		  }
		  $offset = ($paged - 1) * $per_page;

		  $args = [
				'offset'  => $offset,
				'per_page'  => $per_page,
				'role'    => $user_role,
				'orderby' => $settings['orderby'],
				'order'   => $settings['order']
		  ];

		  $users       = count_users();
		  $query       = get_users($args);
		  $total_query = count($query);
		  $total_pages = 1;
		  if (isset($users['avail_roles'][$user_role]) && !empty($users['avail_roles'][$user_role])) {
				$total_pages = intval($users['avail_roles'][$user_role] / $per_page) + 1;
		  }

		printf( '<div class="gva-element-%s gva-element">', $this->get_name() );
		if( !empty($layout) ){
			include $this->get_template('users/' . $layout . '.php');
		}
		print '</div>';
	}
}

$widgets_manager->register(new GVAElement_Users());
