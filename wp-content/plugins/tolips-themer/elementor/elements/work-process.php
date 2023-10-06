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
use Elementor\Icons_Manager;
/**
 * Elementor icon box widget.
 *
 * Elementor widget that displays an icon, a headline and a text.
 *
 * @since 1.0.0
 */
class GVAElement_Work_Process extends GVAElement_Base {  

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
		return 'gva-work-process';
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
		return __( 'GVA Work Process', 'tolips-themer' );
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
		return [ 'work', 'process', 'step' ];
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
			'section_icon',
			[
				'label' => __( 'GVA Work Process', 'tolips-themer' ),
			]
		);
		
		$this->add_control(
			'selected_icon',
			[
				'label' => __( 'Icon', 'tolips-themer' ),
				'type' => Controls_Manager::ICONS,
				'fa4compatibility' => 'icon',
				'default' => [
					'value' => 'fas fa-home',
					'library' => 'fa-solid',
				],
			]
		);

		$this->add_control(
			'box_background_color',
			[
				'label' => __( 'Background Color', 'tolips-themer' ),
				'type' => Controls_Manager::COLOR,
				'default'   => '',
				'selectors' => [
					'{{WRAPPER}} .gsc-work-process .box-content .box-background' => 'background-color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'box_background_image',
			[
				'label' => __( 'Background Image', 'tolips-themer' ),
				'type' => Controls_Manager::MEDIA,
				'label_block' => true,
				'default' => [
					'url' => GAVIAS_TOLIPS_PLUGIN_URL . 'elementor/assets/images/image-1.jpg'
				],

				'selectors' => [
					'{{WRAPPER}} .gsc-work-process .box-content .box-background' => 'background-image:url("{{URL}}");',
				],
			]
		);

		$this->add_responsive_control(
			'box_size',
			[
				'label' => __( 'Box Size', 'tolips-themer' ),
				'type' => Controls_Manager::SLIDER,
				'default' => [
					'size' => 190
				],
				'range' => [
					'px' => [
						'min' => 120,
						'max' => 600,
					],
				],
				'selectors' => [
					'{{WRAPPER}} .gsc-work-process .box-content' => 'width: {{SIZE}}{{UNIT}};height: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->add_responsive_control(
			'box_border_radius',
			[
				'label' => __( 'Border Radius', 'tolips-themer' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%' ],
				'default' => [
					'top' 		=> 50,
					'right' 		=> 50,
					'left'		=> 50,
					'bottom'		=> 50,
					'unit'		=> '%'
				],
				'selectors' => [
					'{{WRAPPER}} .gsc-work-process .box-content, {{WRAPPER}} .gsc-work-process .box-content .box-background' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_responsive_control(
			'box_padding',
			[
				'label' => __( 'Box Padding', 'tolips-themer' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%' ],
				'default' => [
					'top' 		=> 50,
					'right' 		=> 50,
					'left'		=> 50,
					'bottom'		=> 50,
					'unit'		=> 'px'
				],
				'selectors' => [
					'{{WRAPPER}} .gsc-work-process .box-content, {{WRAPPER}} .gsc-work-process .box-content' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);


		$this->end_controls_section();

		$this->start_controls_section(
			'section_content',
			[
				'label' => __( 'Content', 'tolips-themer' ),
			]
		);
		
		$this->add_control(
			'title_text',
			[
				'label' => __( 'Title & Description', 'tolips-themer' ),
				'type' => Controls_Manager::TEXT,
				'dynamic' => [
					'active' => true,
				],
				'default' => __( 'This is the heading', 'tolips-themer' ),
				'placeholder' => __( 'Enter your title', 'tolips-themer' ),
				'label_block' => true,
			]
		);

		$this->add_control(
			'number_text',
			[
				'label' => 'Number text',
				'type' => Controls_Manager::TEXT,
				'default' => __( '01', 'tolips-themer' ),
				'placeholder' => __( 'Enter your number', 'tolips-themer' ),
			]
		);

		$this->add_control(
			'header_tag',
			[
				'label' => __( 'Title HTML Tag', 'tolips-themer' ),
				'type' => Controls_Manager::SELECT,
				'options' => [
					'h1' => 'H1',
					'h2' => 'H2',
					'h3' => 'H3',
					'h4' => 'H4',
					'h5' => 'H5',
					'h6' => 'H6',
					'div' => 'div',
					'span' => 'span',
					'p' => 'p',
				],
				'default' => 'h3',
			]
		);

		$this->end_controls_section();

		$this->start_controls_section( //** Section Button
			'section_line',
			[
				'label' => __( 'Line', 'tolips-themer' ),
			]
		);
		$this->add_control(
			'line_left',
			[
				'label' => __( 'Display Line Left', 'tolips-themer' ),
				'type'      => Controls_Manager::SWITCHER,
            'default'   => 'yes',
			]
		);
		$this->add_control(
			'line_right',
			[
				'label' => __( 'Display Line Right', 'tolips-themer' ),
				'type'      => Controls_Manager::SWITCHER,
            'default'   => 'yes',
			]
		);
		$this->add_control(
			'line_background',
			[
				'label' => __( 'Line Background', 'tolips-themer' ),
				'type' => Controls_Manager::COLOR,
				'default'   => '',
				'selectors' => [
					'{{WRAPPER}} .gsc-work-process .box-content .box-background' => 'background-color: {{VALUE}};',
				],
			]
		);

		$this->end_controls_section();

		$this->start_controls_section( //** Section Button
			'section_button',
			[
				'label' => __( 'Link', 'tolips-themer' ),
			]
		);
		$this->add_control(
			'button_url',
			[
				'label' => __( 'Link', 'tolips-themer' ),
				'type' => Controls_Manager::URL,
			]
		);

		$this->end_controls_section();




		$this->start_controls_section(
			'section_style_icon',
			[
				'label' => __( 'Icon', 'tolips-themer' ),
				'tab'   => Controls_Manager::TAB_STYLE,
				'condition' => [
					'selected_icon[value]!' => ''
				],
			]
		);

		$this->add_control(
			'icon_color',
			[
				'label' => __( 'Icon Color', 'tolips-themer' ),
				'type' => Controls_Manager::COLOR,
				'default' => '',
				'selectors' => [
					'{{WRAPPER}} .gsc-icon-box-styles .icon-inner .box-icon i' => 'color: {{VALUE}};',
					'{{WRAPPER}} .gsc-icon-box-styles .icon-inner .box-icon svg' => 'fill: {{VALUE}};'
				],
			]
		);

		$this->add_control(
			'icon_background',
			[
				'label' => __( 'Icon Background', 'tolips-themer' ),
				'type' => Controls_Manager::COLOR,
				'default' => '',
				'selectors' => [
					'{{WRAPPER}} .gsc-icon-box-styles .icon-inner .box-icon' => 'background: {{VALUE}};',
					'{{WRAPPER}} .gsc-icon-box-styles:hover .icon-inner .box-icon:before' => 'background: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'icon_color_hover',
			[
				'label' => __( 'Hover | Icon Color', 'tolips-themer' ),
				'type' => Controls_Manager::COLOR,
				'default' => '',
				'selectors' => [
					'{{WRAPPER}} .gsc-icon-box-styles:hover .icon-inner .box-icon i' => 'color: {{VALUE}};',
					'{{WRAPPER}} .gsc-icon-box-styles:hover .icon-inner .box-icon svg' => 'fill: {{VALUE}};'
				],
			]
		);

		$this->add_control(
			'icon_background_hover',
			[
				'label' => __( 'Hover | Icon Background', 'tolips-themer' ),
				'type' => Controls_Manager::COLOR,
				'default' => '',
				'selectors' => [
					'{{WRAPPER}} .gsc-icon-box-styles:hover .icon-inner .box-icon:after' => 'background: {{VALUE}};',
					'{{WRAPPER}} .gsc-icon-box-styles .icon-inner .box-icon:before' => 'background: {{VALUE}};'
				],
			]
		);

		$this->add_responsive_control(
			'icon_size',
			[
				'label' => __( 'Size', 'tolips-themer' ),
				'type' => Controls_Manager::SLIDER,
				'default' => [
					'size' => 64
				],
				'range' => [
					'px' => [
						'min' => 20,
						'max' => 80,
					],
				],
				'selectors' => [
					'{{WRAPPER}} .gsc-icon-box-styles .icon-inner .box-icon i' => 'font-size: {{SIZE}}{{UNIT}};',
					'{{WRAPPER}} .gsc-icon-box-styles .icon-inner .box-icon svg' => 'width: {{SIZE}}{{UNIT}};'
				],
			]
		);

		$this->add_responsive_control(
			'icon_box_width',
			[
				'label' => __( 'Icon Box Width', 'tolips-themer' ),
				'type' => Controls_Manager::SLIDER,
				'default' => [
					'size' => 105,
				],
				'range' => [
					'px' => [
						'min' => 100,
						'max' => 500,
					],
				],
				'selectors' => [
					'{{WRAPPER}} .gsc-icon-box-styles .icon-inner .box-icon' => 'width: {{SIZE}}{{UNIT}};height:{{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->add_responsive_control(
			'icon_padding',
			[
				'label' => __( 'Padding', 'tolips-themer' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%' ],
				'default' => [
					'top' 		=> 30,
					'right' 		=> 10,
					'left'		=> 10,
					'bottom'		=> 10,
					'unit'		=> 'px'
				],
				'selectors' => [
					'{{WRAPPER}} .gsc-icon-box-styles .icon-inner .box-icon' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_responsive_control(
			'icon_border_radius',
			[
				'label' => __( 'Border Radius', 'tolips-themer' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%' ],
				'default' => [
					'top' 		=> 50,
					'right' 		=> 50,
					'left'		=> 50,
					'bottom'		=> 50,
					'unit'		=> '%'
				],
				'selectors' => [
					'{{WRAPPER}} .gsc-icon-box-styles .icon-inner .box-icon' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};'
				],
			]
		);

		$this->end_controls_section();


		$this->start_controls_section(
			'section_style_content',
			[
				'label' => __( 'Content', 'tolips-themer' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			]
		);

		$this->add_control(
			'heading_title',
			[
				'label' => __( 'Title', 'tolips-themer' ),
				'type' => Controls_Manager::HEADING,
				'separator' => 'before',
			]
		);

		$this->add_responsive_control(
			'title_bottom_space',
			[
				'label' => __( 'Spacing', 'tolips-themer' ),
				'type' => Controls_Manager::SLIDER,
				'range' => [
					'px' => [
						'min' => 0,
						'max' => 100,
					],
				],
				'default'	=> [
					'size'	=> 0
				],
				'selectors' => [
					'{{WRAPPER}} .gsc-icon-box-styles.style-1 .title' => 'padding-bottom: {{SIZE}}{{UNIT}};',
					'{{WRAPPER}} .gsc-icon-box-styles.style-2 .title' => 'padding-bottom: {{SIZE}}{{UNIT}};',
				],
			]
		); 

		$this->add_control(
			'title_color',
			[
				'label' => __( 'Color', 'tolips-themer' ),
				'type' => Controls_Manager::COLOR,
				'default' => '',
				'selectors' => [
					'{{WRAPPER}} .gsc-icon-box-styles .title' => 'color: {{VALUE}};',
					'{{WRAPPER}} .gsc-icon-box-styles .title a' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name' => 'title_typography',
				'selector' => '{{WRAPPER}} .gsc-icon-box-styles .title, {{WRAPPER}} .gsc-icon-box-styles .title a',
			]
		);

		$this->add_control(
			'heading_description',
			[
				'label' => __( 'Description', 'tolips-themer' ),
				'type' => Controls_Manager::HEADING,
				'separator' => 'before',
				'condition' => [
					'style' => ['style-1', 'style-2', 'style-3'],
				],
			]
		);

		$this->add_control(
			'description_color',
			[
				'label' => __( 'Color', 'tolips-themer' ),
				'type' => Controls_Manager::COLOR,
				'default' => '',
				'selectors' => [
					'{{WRAPPER}} .gsc-icon-box-styles .desc' => 'color: {{VALUE}};',
				],
				'condition' => [
					'style' => ['style-1', 'style-2', 'style-3'],
				],
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name' => 'description_typography',
				'selector' => '{{WRAPPER}} .gsc-icon-box-styles .desc',
				'condition' => [
					'style' => ['style-1', 'style-2', 'style-3'],
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
         include $this->get_template('work-process.php');
      print '</div>';
	}
}

$widgets_manager->register(new GVAElement_Work_Process());
