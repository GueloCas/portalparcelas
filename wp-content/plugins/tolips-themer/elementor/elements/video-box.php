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
class GVAElement_Video_Box extends GVAElement_Base {  

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
		return 'gva-video-box';
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
		return __( 'GVA Video Box', 'tolips-themer' );
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
		return [ 'video', 'box' ];
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
				'label' => __( 'Content', 'tolips-themer' ),
			]
		);

		$this->add_control(
			'title_text',
			[
				'label' => __( 'Title Left', 'tolips-themer' ),
				'type' => Controls_Manager::TEXT,
				'dynamic' => [
					'active' => true,
				],
				'placeholder' => __( 'Enter Your Title', 'tolips-themer' ),
				'label_block' => true
			]
		);
		$this->add_control(
			'title_text_right',
			[
				'label' => __( 'Title Right', 'tolips-themer' ),
				'type' => Controls_Manager::TEXT,
				'dynamic' => [
					'active' => true,
				],
				'placeholder' => __( 'Enter Your Title', 'tolips-themer' ),
				'label_block' => true
			]
		);

		$this->add_control(
			'style',
			[
				'label' => __( 'style', 'tolips-themer' ),
				'type' => Controls_Manager::SELECT,
				'options' => [
					'style-1' 		=> __( 'Style I', 'tolips-themer' ),
				],
				'default' => 'style-1',
			]
		);

		$this->add_control(
			'image',
			[
				'label' => __( 'Image', 'tolips-themer' ),
				'type' => Controls_Manager::MEDIA,
				'dynamic' => [
					'active' => true,
				],
				'default' => [
               'url' => GAVIAS_TOLIPS_PLUGIN_URL . 'elementor/assets/images/video.jpg',
				],
				'condition' => [
					'style' => ['style-1']
				]
			]
		);

		$this->add_control(
			'link',
			[
				'label' => __( 'Link', 'tolips-themer' ),
				'type' => Controls_Manager::URL,
				'dynamic' => [
					'active' => true,
				],
				'placeholder' => __( 'https://your-link.com', 'tolips-themer' ),
				'separator' => 'before',
			]
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'section_box_style',
			[
				'label' => __( 'Box Style', 'tolips-themer' ),
				'tab' => Controls_Manager::TAB_STYLE,
			]
		);

		$this->add_control(
			'box_primary_color',
			[
				'label' => __( 'Primary Color', 'tolips-themer' ),
				'type' => Controls_Manager::COLOR,
				'default'   => '',
				'selectors' => [
					'{{WRAPPER}} .gsc-icon-box-styles.style-1 .icon-inner::before, {{WRAPPER}} .gsc-icon-box-styles.style-1 .icon-inner::after' => 'background-color: {{VALUE}};',
					'{{WRAPPER}} .gsc-icon-box-styles.style-2 .block-content::before, {{WRAPPER}} .gsc-icon-box-styles.style-2 .block-content::after' => 'background-color: {{VALUE}};',
				],
			]
		);
		$this->end_controls_section();

		$this->start_controls_section(
			'section_style_icon',
			[
				'label' => __( 'Icon', 'tolips-themer' ),
				'tab'   => Controls_Manager::TAB_STYLE,
				
			]
		);

		$this->add_control(
			'icon_color',
			[
				'label' => __( 'Primary Color', 'tolips-themer' ),
				'type' => Controls_Manager::COLOR,
				'default' => '',
				'condition' => [
					'icon!' => '',
				],
				'selectors' => [
					'{{WRAPPER}}. gsc-icon-box-styles .icon-inner .icon' => 'color: {{VALUE}};'
				],
			]
		);


		$this->add_responsive_control(
			'icon_space',
			[
				'label' => __( 'Spacing', 'tolips-themer' ),
				'type' => Controls_Manager::SLIDER,
				'default' => [
					'size' => 20,
				],
				'range' => [
					'px' => [
						'min' => 0,
						'max' => 100,
					],
				],
				'selectors' => [
					'{{WRAPPER}} .gsc-icon-box.right .highlight-icon' => 'padding-left: {{SIZE}}{{UNIT}};',
					'{{WRAPPER}} .gsc-icon-box.left .highlight-icon' => 'padding-right: {{SIZE}}{{UNIT}};',
					'{{WRAPPER}} .gsc-icon-box.top .highlight-icon' => 'padding-bottom: {{SIZE}}{{UNIT}};',
					'(mobile){{WRAPPER}} .highlight-icon' => 'padding-bottom: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->add_responsive_control(
			'icon_size',
			[
				'label' => __( 'Size', 'tolips-themer' ),
				'type' => Controls_Manager::SLIDER,
				'default' => [
					'size' => 30
				],
				'range' => [
					'px' => [
						'min' => 6,
						'max' => 100,
					],
				],
				'selectors' => [
					'{{WRAPPER}} .gsc-icon-box-styles .icon-inner .icon' => 'font-size: {{SIZE}}{{UNIT}};',
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
					'{{WRAPPER}} .gsc-icon-box-styles .icon-inner' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
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
				'default' => [
					'size' => 20
				],
				'selectors' => [
					'{{WRAPPER}} .elementor-icon-box-title' => 'margin-bottom: {{SIZE}}{{UNIT}};',
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
					'style' => 'style-1',
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
					'{{WRAPPER}} .highlight_content .desc' => 'color: {{VALUE}};',
				],
				'condition' => [
					'style' => 'style-1',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name' => 'description_typography',
				'selector' => '{{WRAPPER}} .highlight_content .desc',
				'condition' => [
					'style' => 'style-1',
				],

			]
		);

		$this->add_responsive_control(
			'description_bottom_space',
			[
				'label' => __( 'Spacing', 'tolips-themer' ),
				'type' => Controls_Manager::SLIDER,
				'range' => [
					'px' => [
						'min' => 0,
						'max' => 100,
					],
				],
				'default' => [
					'size' => 20
				],
				'selectors' => [
					'{{WRAPPER}} .gsc-icon-box-styles .desc' => 'margin-bottom: {{SIZE}}{{UNIT}};',
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
         include $this->get_template('video-box.php');
      print '</div>';
	}
}

$widgets_manager->register(new GVAElement_Video_Box());
