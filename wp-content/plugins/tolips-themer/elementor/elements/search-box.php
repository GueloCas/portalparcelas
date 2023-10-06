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
 * Elementor icon box widget.
 *
 * Elementor widget that displays an icon, a headline and a text.
 *
 * @since 1.0.0
 */
class GVAElement_Search_Box extends GVAElement_Base {  

	/**
	 * Get widget name.
	 *
	 * Retrieve icon box widget name.
	 *
	 * @since 1.0.0
	 * @access public
	 *
	 * @return string Widget name.
	 */
	public function get_name() {
		return 'gva-search-box';
	}

	/**
	 * Get widget title.
	 *
	 * Retrieve icon box widget title.
	 *
	 * @since 1.0.0
	 * @access public
	 *
	 * @return string Widget title.
	 */
	public function get_title() {
		return __( 'GVA Search Box', 'tolips-themer' );
	}

	/**
	 * Get widget icon.
	 *
	 * Retrieve icon box widget icon.
	 *
	 * @since 1.0.0
	 * @access public
	 *
	 * @return string Widget icon.
	 */
	public function get_icon() {
		return 'eicon-icon-box';
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
		return [ 'search' ];
	}

	/**
	 * Register icon box widget controls.
	 *
	 * Adds different input fields to allow the user to change and customize the widget settings.
	 *
	 * @since 1.0.0
	 * @access protected
	 */
	protected function register_controls() {

		$this->start_controls_section(
			'section_layout',
			[
				'label' => __( 'Layout', 'tolips-themer' ),
			]
		);

		$this->add_control(
			'style',
			[
				'label' => __( 'Style', 'tolips-themer' ),
				'type' => Controls_Manager::SELECT,
				'options' => [
					'style-1' 		=> __( 'Style I', 'tolips-themer' ),
				],
				'default' => 'style-1',
			]
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'section_icon',
			[
				'label' => __( 'Icon', 'tolips-themer' ),
				'style' => ['style-1']
			]
		);
		
		$this->add_control(
			'selected_icon',
			[
				'label' => __( 'Icon', 'tolips-themer' ),
				'type' => Controls_Manager::ICONS,
				'fa4compatibility' => 'icon',
				'default' => [
					'value' => 'fa fa-search',
					'library' => 'fa-solid',
				],
			]
		);
		
		$this->end_controls_section();

		$this->start_controls_section(
			'section_icon_style',
			[
				'label' => __( 'Icon', 'tolips-themer' ),
				'tab' => Controls_Manager::TAB_STYLE,
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
				'selectors' => [
					'{{WRAPPER}} .gsc-search-box' => 'text-align: {{VALUE}};',
				],
				'default' => 'center',
				'condition' => [
					'style' => 'style-1'
				]
			]
		);

		$this->add_responsive_control(
			'icon_size',
			[
				'label' => __( 'Icon Size', 'tolips-themer' ),
				'type' => Controls_Manager::SLIDER,
				'default' => [
					'size' => 20,
				],
				'range' => [
					'px' => [
						'min' => 0,
						'max' => 500,
					],
				],
				'selectors' => [
					'{{WRAPPER}} .gsc-search-box .control-search i' => 'font-size: {{SIZE}}{{UNIT}};',
					'{{WRAPPER}} .gsc-search-box .control-search svg' => 'width: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->add_control(
			'icon_color',
			[
				'label' => __( 'Color', 'tolips-themer' ),
            'type'      => Controls_Manager::COLOR,
            'selectors' => [
               '{{WRAPPER}} .gsc-search-box .control-search i' => 'color: {{VALUE}}', 
               '{{WRAPPER}} .gsc-search-box .control-search svg' => 'fill: {{VALUE}}', 
            ],
			]
		);

		$this->add_control(
			'icon_color_hover',
			[
				'label' => __( 'Color Hover', 'tolips-themer' ),
            'type'      => Controls_Manager::COLOR,
            'selectors' => [
               '{{WRAPPER}} .gsc-search-box .control-search:hover i' => 'color: {{VALUE}}', 
               '{{WRAPPER}} .gsc-search-box .control-search:hover svg' => 'fill: {{VALUE}}', 
            ],
			]
		);

		$this->add_control(
			'icon_background',
			[
				'label' => __( 'Background', 'tolips-themer' ),
            'type'      => Controls_Manager::COLOR,
            'selectors' => [
               '{{WRAPPER}} .gsc-search-box .control-search' => 'background: {{VALUE}}', 
            ],
			]
		);

		$this->add_control(
			'icon_background_hover',
			[
				'label' => __( 'Background Hover', 'tolips-themer' ),
            'type'      => Controls_Manager::COLOR,
            'selectors' => [
               '{{WRAPPER}} .gsc-search-box .control-search:hover' => 'background: {{VALUE}}', 
            ],
			]
		);

		$this->add_responsive_control(
			'icon_padding',
			[
				'label' => __( 'Padding', 'tolips-themer' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%' ],
				'selectors' => [
					'{{WRAPPER}} .gsc-search-box .control-search' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_control(
			'border_radius',
			[
				'label' => __( 'Border Radius', 'tolips-themer' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%' ],
				'selectors' => [
					'{{WRAPPER}} .gsc-icon-box .icon' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'section_content_style',
			[
				'label' => __( 'Content', 'tolips-themer' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			]
		);

		$this->add_responsive_control(
			'content_padding',
			[
				'label' => __( 'Padding', 'tolips-themer' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%' ],
				'default' => [
					'top' 		=> 15,
					'right' 		=> 15,
					'left'		=> 15,
					'bottom'		=> 15,
					'unit'		=> 'px'
				],
				'selectors' => [
					'{{WRAPPER}} .gsc-search-box .gva-search .gva-search-content' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);


		$this->add_responsive_control(
			'content_size',
			[
				'label' => __( 'Content Width', 'tolips-themer' ),
				'type' => Controls_Manager::SLIDER,
				'default' => [
					'size' => 200,
				],
				'range' => [
					'px' => [
						'min' => 150,
						'max' => 500,
					],
				],
				'selectors' => [
					'{{WRAPPER}} .gsc-search-box .gva-search-content' => 'width: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->end_controls_section();

	}

	/**
	 * Render icon box widget output on the frontend.
	 *
	 * Written in PHP and used to generate the final HTML.
	 *
	 * @since 1.0.0
	 * @access protected
	 */
	protected function render() {
		$settings = $this->get_settings_for_display();
		printf( '<div class="gva-element-%s gva-element">', $this->get_name() );
         include $this->get_template('search-box.php');
      print '</div>';
	}
}

$widgets_manager->register(new GVAElement_Search_Box());
