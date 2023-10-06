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
class GVAElement_User_Wishlist extends GVAElement_Base {

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
		return 'gva-user-wishlist';
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
		return __( 'GVA User Wishlist', 'tolips-themer' );
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
		return [ 'user', 'block', 'wishlist' ];
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
         'wishlist_text',
         [
            'label'        => __( 'Wishlist Text', 'tolips-themer' ),
            'type'         => Controls_Manager::TEXT,
            'default'      => 'Wishlist',
            'label_block'  => true
         ]
      );

      $this->add_control(
         'link',
         [
            'label' => __( 'Wishlist Link', 'tolips-themer' ),
            'type' => Controls_Manager::URL,
            'placeholder' => __( 'https://your-link.com', 'tolips-themer' ),
         ]
      );

      $this->add_control(
         'selected_icon',
         [
            'label' => __( 'Icon', 'tolips-themer' ),
            'type' => Controls_Manager::ICONS,
            'fa4compatibility' => 'icon',
            'default' => [
               'value' => 'fas fa-heart',
               'library' => 'fa-solid',
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
               '{{WRAPPER}} .user-wishlist .wishlist-icon i' => 'font-size: {{SIZE}}{{UNIT}};',
               '{{WRAPPER}} .user-wishlist .wishlist-icon svg' => 'width: {{SIZE}}{{UNIT}};',
            ],
         ]
      );

      $this->add_control(
         'icon_color',
         [
            'label' => __( 'Color', 'tolips-themer' ),
            'type'      => Controls_Manager::COLOR,
            'selectors' => [
               '{{WRAPPER}} .user-wishlist .wishlist-icon i' => 'color: {{VALUE}}', 
               '{{WRAPPER}} .user-wishlist .wishlist-icon svg' => 'fill: {{VALUE}}', 
            ],
         ]
      );

      $this->add_control(
         'icon_color_hover',
         [
            'label' => __( 'Color Hover', 'tolips-themer' ),
            'type'      => Controls_Manager::COLOR,
            'selectors' => [
               '{{WRAPPER}} .user-wishlist .wishlist-link:hover .wishlist-icon i' => 'color: {{VALUE}}!important', 
               '{{WRAPPER}} .user-wishlist .wishlist-link:hover .wishlist-icon svg' => 'fill: {{VALUE}}!important', 
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
               '{{WRAPPER}} .user-wishlist .wishlist-link' => 'color: {{VALUE}}', 
            ],
         ]
      );

      $this->add_control(
         'text_color_hover',
         [
            'label' => __( 'Text Color Hover', 'tolips-themer' ),
            'type'      => Controls_Manager::COLOR,
            'selectors' => [
               '{{WRAPPER}} .user-wishlist .wishlist-link:hover' => 'color: {{VALUE}}', 
            ],
         ]
      );

      $this->add_group_control(
         Group_Control_Typography::get_type(),
         [
            'name' => 'text_typography',
            'selector' => '{{WRAPPER}} .user-wishlist .wishlist-link',
         ]
      );

  

      $this->end_controls_section();

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
        include $this->get_template('user-wishlist.php');
      print '</div>';
	}
}

$widgets_manager->register(new GVAElement_User_Wishlist());
