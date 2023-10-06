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
class GVAElement_Career_Block extends GVAElement_Base {

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
		return 'gva-career-block';
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
		return __( 'GVA Career Block', 'tolips-themer' );
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
		return 'eicon-table';
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
		return [ 'career', 'block', 'text' ];
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
			'title_text',
			[
				'label' => __( 'Title', 'tolips-themer' ),
				'type' => Controls_Manager::TEXT,
				'dynamic' => [
					'active' => true,
				],
				'placeholder' => __( 'Enter your title', 'tolips-themer' ),
				'default' => __( 'Add Your Heading Text Here', 'tolips-themer' ),
				'label_block' => true
			]
		);
		$this->add_control(
			'header_tag',
			[
				'label' => __( 'HTML Tag', 'tolips-themer' ),
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
				'default' => 'h2',
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

		$this->start_controls_section( //** Section Icon
			'image_section',
			[
				'label' => __( 'Image', 'tolips-themer' ),
			]
		);
		$this->add_control(
			'image',
			[
				'label' => __( 'Icon Image', 'tolips-themer' ),
				'type' => Controls_Manager::MEDIA,
				'dynamic' => [
					'active' => true,
				],
				'default' => [
					'url' => GAVIAS_TOLIPS_PLUGIN_URL . 'elementor/assets/images/icon.png',
				],
			]
		);

		$this->end_controls_section();

		$this->start_controls_section( 
			'content',
			[
				'label' => __( 'Content', 'tolips-themer' ),
			]
		);
		$this->add_control(
			'job_type',
			[
				'label' => __( 'Job Type', 'tolips-themer' ),
				'type' => Controls_Manager::TEXT,
				'default' => 'Full Time'
			]
		);
		$this->add_control(
			'company',
			[
				'label' => __( 'Company', 'tolips-themer' ),
				'type' => Controls_Manager::TEXT,
				'default' => 'Gaviasthemes'
			]
		);
		$this->add_control(
			'address',
			[
				'label' => __( 'Address', 'tolips-themer' ),
				'type' => Controls_Manager::TEXT,
				'default' => 'New South Wales, Australia'
			]
		);
		$this->end_controls_section();

		// Title Styling
		$this->start_controls_section(
			'section_title_style',
			[
				'label' => __( 'Title', 'tolips-themer' ),
				'tab' => Controls_Manager::TAB_STYLE,
			]
		);
		$this->add_control(
			'title_color',
			[
				'label' => __( 'Text Color', 'tolips-themer' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .gsc-career .box-content .title, {{WRAPPER}} .gsc-career .box-content .title a' => 'color: {{VALUE}};',
				],
			]
		);
		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name' => 'typography',
				'selector' => '{{WRAPPER}} .gsc-career .box-content .title',
			]
		);
		$this->add_responsive_control(
			'title_padding',
			[
				'label' => __( 'Padding', 'tolips-themer' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%' ],
				'default'	=> [
					'top' 		=> 0,
					'bottom'		=> 0,
					'right' 		=> 0,
					'left'  		=> 0,
					'unit'		=> 'px'
				],
				'selectors' => [
					'{{WRAPPER}} .gsc-career .box-content .title' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);
		$this->end_controls_section();

		// Job Type Styling
		$this->start_controls_section(
			'section_job_type_style',
			[
				'label' => __( 'Job Type', 'tolips-themer' ),
				'tab' => Controls_Manager::TAB_STYLE,
			]
		);
		$this->add_control(
			'job_type_color',
			[
				'label' => __( 'Text Color', 'tolips-themer' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .gsc-career .box-content .job-type' => 'color: {{VALUE}};',
				],
			]
		);
		$this->add_control(
			'job_type_background',
			[
				'label' => __( 'Background Color', 'tolips-themer' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .gsc-career .box-content .job-type' => 'background: {{VALUE}};',
				],
			]
		);
		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name' => 'job_type_typography',
				'selector' => '{{WRAPPER}} .gsc-career .box-content .job-type',
			]
		);
		$this->add_responsive_control(
			'job_type_padding',
			[
				'label' => __( 'Padding', 'tolips-themer' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%' ],
				'default'	=> [
					'top' 		=> 2,
					'bottom'		=> 2,
					'right' 		=> 12,
					'left'  		=> 12,
					'unit'		=> 'px'
				],
				'selectors' => [
					'{{WRAPPER}} .gsc-career .box-content .job-type' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);
		$this->end_controls_section();

		// Address & Company Styling
		$this->start_controls_section(
			'info_type_style',
			[
				'label' => __( 'Information (Address & Company)', 'tolips-themer' ),
				'tab' => Controls_Manager::TAB_STYLE,
			]
		);
		$this->add_control(
			'info_color',
			[
				'label' => __( 'Text Color', 'tolips-themer' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .gsc-career .box-content .box-information' => 'color: {{VALUE}};',
				],
			]
		);
		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name' => 'info_typography',
				'selector' => '{{WRAPPER}} .gsc-career .box-content .box-information',
			]
		);
		$this->add_responsive_control(
			'info_padding',
			[
				'label' => __( 'Padding', 'tolips-themer' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%' ],
				'default'	=> [
					'top' 		=> 10,
					'bottom'		=> 0,
					'right' 		=> 0,
					'left'  		=> 0,
					'unit'		=> 'px'
				],
				'selectors' => [
					'{{WRAPPER}} .gsc-career .box-content .box-information' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);
		$this->end_controls_section();
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
         include $this->get_template('career-block.php');
      print '</div>';
	}

}
      $widgets_manager->register(new GVAElement_Career_Block());
