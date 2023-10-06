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
class GVAElement_User extends GVAElement_Base {

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
		return 'gva-user';
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
		return __( 'GVA User', 'tolips-themer' );
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
		return [ 'menu', 'user', 'block' ];
	}

	public function get_all_menus(){
	   $menus = get_terms( 'nav_menu', array( 'hide_empty' => true ) ); 
	   $results = array();
	   foreach ($menus as $key => $menu) {
	   	$results[$menu->slug] = $menu->name;
	   }
	   return $results;
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
         'align',
         [
            'label' => __( 'Alignment', 'tolips-themer' ),
            'type' => Controls_Manager::CHOOSE,
            'options' => [
               'left' => [
                  'title' => __( 'Left', 'tolips-themer' ),
                  'icon' => 'fa fa-align-left',
               ],
               'center' => [
                  'title' => __( 'Center', 'tolips-themer' ),
                  'icon' => 'fa fa-align-center',
               ],
               'right' => [
                  'title' => __( 'Right', 'tolips-themer' ),
                  'icon' => 'fa fa-align-right',
               ],
            ],
            'default' => 'center',
         ]
      );

      $this->add_control(
         'text_login',
         [
            'label'        => __( 'Sign in text', 'tolips-themer' ),
            'type'         => Controls_Manager::TEXT,
            'default'      => 'Sign in',
            'label_block'  => true
         ]
      );

      $this->add_control(
         'selected_icon',
         [
            'label' => __( 'Icon', 'tolips-themer' ),
            'type' => Controls_Manager::ICONS,
            'fa4compatibility' => 'icon',
            'default' => [
               'value' => 'fas fa-user',
               'library' => 'fa-solid',
            ],
         ]
      );

		$this->add_control(
			'menu_width',
			[
				'label' => __( 'Menu Width (px)', 'tolips-themer' ),
				'type' => Controls_Manager::SLIDER,
				'default' => [
					'size' => 250,
				],
				'range' => [
					'px' => [
						'min' => 100,
						'max' => 500,
					],
				],
				'selectors' => [
					'{{WRAPPER}} .gva-user ul.gva-nav-menu' => 'min-width: {{SIZE}}{{UNIT}};',
				],
			]
		);
		
		$this->end_controls_section();

      $this->start_controls_section(
         'section_content_style',
         [
            'label' => __( 'Text & Icon', 'tolips-themer' ),
            'tab' => Controls_Manager::TAB_STYLE,
         ]
      );

      $this->add_control(
         'icon_style',
         [
            'label' => __( 'Icon Style', 'tolips-themer' ),
            'type'      => Controls_Manager::HEADING,
         ]
      );

      $this->add_responsive_control(
         'icon_size',
         [
            'label' => __( 'Icon Size', 'tolips-themer' ),
            'type' => Controls_Manager::SLIDER,
            'default' => [
               'size' => 13,
            ],
            'range' => [
               'px' => [
                  'min' => 0,
                  'max' => 500,
               ],
            ],
            'selectors' => [
               '{{WRAPPER}} .gva-user .login-register i' => 'font-size: {{SIZE}}{{UNIT}};',
               '{{WRAPPER}} .gva-user .login-register svg' => 'width: {{SIZE}}{{UNIT}};',
            ],
         ]
      );

      $this->add_control(
         'icon_color',
         [
            'label' => __( 'Color', 'tolips-themer' ),
            'type'      => Controls_Manager::COLOR,
            'selectors' => [
               '{{WRAPPER}} .gva-user .login-register i' => 'color: {{VALUE}}', 
               '{{WRAPPER}} .gva-user .login-register svg' => 'fill: {{VALUE}}', 
            ],
         ]
      );

      $this->add_control(
         'icon_color_hover',
         [
            'label' => __( 'Color Hover', 'tolips-themer' ),
            'type'      => Controls_Manager::COLOR,
            'selectors' => [
               '{{WRAPPER}} .gva-user:hover .login-register .box-icon i' => 'color: {{VALUE}}', 
               '{{WRAPPER}} .gva-user:hover .login-register .box-icon svg' => 'fill: {{VALUE}}', 
            ],
         ]
      );

      $this->add_control(
         'text_style',
         [
            'label' => __( 'Text Style', 'tolips-themer' ),
            'type'      => Controls_Manager::HEADING,
         ]
      );

      $this->add_control(
         'text_color',
         [
            'label' => __( 'Text Color', 'tolips-themer' ),
            'type'      => Controls_Manager::COLOR,
            'selectors' => [
               '{{WRAPPER}} .gva-user .user-text' => 'color: {{VALUE}}', 
               '{{WRAPPER}} .gva-user .sign-in-link' => 'color: {{VALUE}}'
            ],
         ]
      );

      $this->add_control(
         'text_color_hover',
         [
            'label' => __( 'Text Color Hover', 'tolips-themer' ),
            'type'      => Controls_Manager::COLOR,
            'selectors' => [
               '{{WRAPPER}} .gva-user:hover .user-text' => 'color: {{VALUE}}', 
               '{{WRAPPER}} .gva-user:hover .sign-in-link' => 'color: {{VALUE}}'
            ],
         ]
      );

      $this->add_group_control(
         Group_Control_Typography::get_type(),
         [
            'name' => 'text_typography',
            'selector' => '{{WRAPPER}} .gva-user .user-text',
         ]
      );

  

      $this->end_controls_section();

		$this->start_controls_section(
			'section_account_menu_style',
			[
				'label' => __( 'Account Menu', 'tolips-themer' ),
				'tab' => Controls_Manager::TAB_STYLE,
			]
		);
      $this->add_control(
         'account_menu_color',
         [
            'label'     => __('Color', 'tolips-themer'),
            'type'      => Controls_Manager::COLOR,
            'selectors' => [
                '{{WRAPPER}} .gva-user .login-account .user-account .gva-nav-menu > li > a' => 'color: {{VALUE}}',
            ],
         ]
      );
      $this->add_control(
         'account_menu_color_hover',
         [
            'label'     => __('Color Hover', 'tolips-themer'),
            'type'      => Controls_Manager::COLOR,
            'selectors' => [
                '{{WRAPPER}} .gva-user .login-account .user-account .gva-nav-menu > li > a:hover' => 'color: {{VALUE}}',
            ],
         ]
      );

      $this->add_group_control(
         Group_Control_Typography::get_type(),
         [
            'name' => 'typography',
            'selector' => '{{WRAPPER}} .gva-user .login-account .user-account .gva-nav-menu > li > a',
         ]
      );

      $this->add_responsive_control(
         'main_menu_padding',
         [
            'label' => __( 'Menu Item Padding', 'tolips-themer' ),
            'type' => Controls_Manager::DIMENSIONS,
            'size_units' => [ 'px', '%' ],
            'selectors' => [
               '{{WRAPPER}} .gva-user .login-account .user-account .gva-nav-menu > li > a' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
            ],
         ]
      );
  
      $this->end_controls_tab();

      $this->end_controls_tabs();

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

		printf( '<div class="gva-element-%s gva-element">', $this->get_name() );
        include $this->get_template('user.php');
      print '</div>';
	}
}

$widgets_manager->register(new GVAElement_User());
