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
class GVAElement_Counter extends GVAElement_Base {  

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
		return 'gva-counter';
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
		return __( 'GVA Counter', 'indutri-themer' );
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
		return [ 'counter', 'icon' ];
	}

	public function get_script_depends() {
      return [
         'jquery.count_to',
         'jquery.appear',
      ];
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
			'section_content',
			[
				'label' => __( 'Content', 'indutri-themer' ),
			]
		);
		$this->add_control(
			'selected_icon',
			[
				'label' => __( 'Icon Class', 'indutri-themer' ),
				'type' => Controls_Manager::ICONS,
				'fa4compatibility' => 'icon',
				'default' => [
					'value' => 'fas fa-home',
					'library' => 'fa-solid',
				],
			]
		);
		$this->add_control(
			'number',
			[
				'label' => __( 'Number', 'indutri-themer' ),
				'type' => Controls_Manager::NUMBER,
				'default' => 110
			]
		);
		$this->add_control(
			'text_before',
			[
				'label' => __( 'Text Before Number', 'indutri-themer' ),
				'type' => Controls_Manager::TEXT,
			]
		);
		$this->add_control(
			'text_after',
			[
				'label' => __( 'Text After Number', 'indutri-themer' ),
				'type' => Controls_Manager::TEXT,
			]
		);
		$this->add_control(
			'title_text',
			[
				'label' => __( 'Title & Description', 'indutri-themer' ),
				'type' => Controls_Manager::TEXT,
				'dynamic' => [
					'active' => true,
				],
				'default' => __( 'This is the heading', 'indutri-themer' ),
				'placeholder' => __( 'Enter your title', 'indutri-themer' ),
				'label_block' => true,
			]
		);
		$this->add_control(
			'link',
			[
				'label' => __( 'Link', 'indutri-themer' ),
				'type' => Controls_Manager::URL,
				'placeholder' => __( 'https://your-link.com', 'indutri-themer' ),
			]
		);
		$this->add_control(
			'title_size',
			[
				'label' => __( 'Title HTML Tag', 'indutri-themer' ),
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
				'default' => 'div',
			]
		);
		$this->add_control(
			'style',
			[
				'label' => __( 'Style', 'indutri-themer' ),
				'type' => Controls_Manager::SELECT,
				'options' => [
					'style-1' 		=> __( 'Style I', 'indutri-themer' ),
					'style-2' 		=> __( 'Style II', 'indutri-themer' ),
					'style-3' 		=> __( 'Style III', 'indutri-themer' ),
				],
				'default' => 'style-1',
			]
		);
		$this->end_controls_section();


		// Style Icon
		$this->start_controls_section(
			'section_style_icon',
			[
				'label' => __( 'Icon', 'indutri-themer' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			]
		);

		$this->add_control(
			'icon_color',
			[
				'label' => __( 'Icon Color', 'indutri-themer' ),
				'type' => Controls_Manager::COLOR,
				'default' => '',
				'selectors' => [
					'{{WRAPPER}} .milestone-block .milestone-icon .icon i' => 'color: {{VALUE}};',
					'{{WRAPPER}} .milestone-block .milestone-icon .icon svg' => 'fill: {{VALUE}};'
				],
			]
		);

		$this->add_control(
			'icon_color_hover',
			[
				'label' => __( 'Hover | Icon Color', 'indutri-themer' ),
				'type' => Controls_Manager::COLOR,
				'default' => '',
				'selectors' => [
					'{{WRAPPER}} .milestone-block:hover .milestone-icon .icon i' => 'color: {{VALUE}};',
					'{{WRAPPER}} .milestone-block:hover .milestone-icon .icon svg' => 'fill: {{VALUE}};'
				],
			]
		);

		$this->add_responsive_control(
			'icon_size',
			[
				'label' => __( 'Icon Size', 'indutri-themer' ),
				'type' => Controls_Manager::SLIDER,
				'default' => [
					'size' => 60
				],
				'range' => [
					'px' => [
						'min' => 6,
						'max' => 100,
					],
				],
				'selectors' => [
					'{{WRAPPER}} .milestone-block .milestone-icon .icon' => 'font-size: {{SIZE}}{{UNIT}};',
					'{{WRAPPER}} .milestone-block .milestone-icon .icon svg' => 'width: {{SIZE}}{{UNIT}};'
				],
			]
		);

		$this->add_responsive_control(
			'icon_space',
			[
				'label' => __( 'Spacing', 'indutri-themer' ),
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
					'{{WRAPPER}} .milestone-block.style-1 .box-content .milestone-icon' => 'margin-bottom: {{SIZE}}{{UNIT}};',
					'{{WRAPPER}} .milestone-block.style-2 .box-content .milestone-icon' => 'padding-right: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->end_controls_section();


		// Title
		$this->start_controls_section(
			'section_title_style',
			[
				'label' => __( 'Title', 'indutri-themer' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			]
		);

		$this->add_responsive_control(
			'title_top_space',
			[
				'label' => __( 'Spacing', 'indutri-themer' ),
				'type' => Controls_Manager::SLIDER,
				'range' => [
					'px' => [
						'min' => 0,
						'max' => 100,
					],
				],
				'selectors' => [
					'{{WRAPPER}} .milestone-block .box-content .milestone-content .milestone-text' => 'margin-top: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->add_control(
			'title_color',
			[
				'label' => __( 'Color', 'indutri-themer' ),
				'type' => Controls_Manager::COLOR,
				'default' => '',
				'selectors' => [
					'{{WRAPPER}} .milestone-block .box-content .milestone-content .milestone-text' => 'color: {{VALUE}}!important;',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name' => 'title_typography',
				'selector' => '{{WRAPPER}} .milestone-block .box-content .milestone-content .milestone-text',
			]
		);

		$this->end_controls_section();

		// Number Text
		$this->start_controls_section(
			'sectionn_number_style',
			[
				'label' => __( 'Number Text', 'indutri-themer' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			]
		);

		$this->add_responsive_control(
			'number_bottom_space',
			[
				'label' => __( 'Spacing', 'indutri-themer' ),
				'type' => Controls_Manager::SLIDER,
				'range' => [
					'px' => [
						'min' => 0,
						'max' => 100,
					],
				],
				'selectors' => [
					'{{WRAPPER}} .milestone-block .milestone-number-inner' => 'margin-bottom: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->add_control(
			'number_text_color',
			[
				'label' => __( 'Color', 'indutri-themer' ),
				'type' => Controls_Manager::COLOR,
				'default' => '',
				'selectors' => [
					'{{WRAPPER}} .milestone-block .milestone-number' => 'color: {{VALUE}};',
				]
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name' => 'number_text_typography',
				'selector' => '{{WRAPPER}} .box-content .milestone-content .milestone-number-inner',
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
         include $this->get_template('counter.php');
      print '</div>';
	}
}
$widgets_manager->register(new GVAElement_Counter());
