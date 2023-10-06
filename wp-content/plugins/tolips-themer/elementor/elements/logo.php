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
class GVAElement_Logo extends GVAElement_Base {

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
		return 'gva-logo';
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
		return __( 'GVA Logo', 'tolips-themer' );
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
		return 'eicon-site-logo';
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
		return [ 'image', 'logo', 'branding' ];
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
			'section_title',
			[
				'label' => __( 'Content', 'tolips-themer' ),
			]
		);
		$this->add_control(
			'title_text',
			[
				'label' => __( 'Title', 'tolips-themer' ),
				'type' => Controls_Manager::TEXT,
				'placeholder' => __( 'Enter your title', 'tolips-themer' ),
				'default' => __( 'Home', 'tolips-themer' ),
				'label_block' => true
			]
		);
		$this->add_control(
			'image',
			[
				'label' => __( 'Choose Logo Image', 'tolips-themer' ),
				'type' => Controls_Manager::MEDIA,
				'label_block' => true,
				'default' => [
					'url' => GAVIAS_TOLIPS_PLUGIN_URL . 'elementor/assets/images/logo.png',
				],
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
				'label_block' => true
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
			'max_width',
			[
				'label' => __( 'Max Width', 'tolips-themer' ),
				'type' => Controls_Manager::SLIDER,
				'range' => [
					'px' => [
						'min' => 120,
						'max' => 500,
					],
				],
				'selectors' => [
					'{{WRAPPER}} .gsc-logo .site-branding-logo' => 'max-width: {{SIZE}}{{UNIT}};',
				],
			]
		);
		$this->end_controls_section();

		$this->start_controls_section(
			'section_box_style',
			[
				'label' => __( 'Logo Styling', 'tolips-themer' ),
				'tab' => Controls_Manager::TAB_STYLE,
			]
		);
		$this->add_responsive_control(
			'logo_padding',
			[
				'label' => __( 'Padding', 'tolips-themer' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%' ],
				'selectors' => [
					'{{WRAPPER}} .gsc-logo .site-branding-logo' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);
		$this->add_responsive_control(
			'logo_margin',
			[
				'label' => __( 'Margin', 'tolips-themer' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%' ],
				'selectors' => [
					'{{WRAPPER}} .gsc-logo .site-branding-logo' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);
		$this->add_group_control(
         Group_Control_Border::get_type(),
         [
            'name'      => 'logo_border',
            'selector'  => '{{WRAPPER}} .gsc-logo .site-branding-logo',
            'separator' => 'before',
         ]
       );

       $this->add_control(
         'image_border_radius',
         [
            'label'      => __('Border Radius', 'tolips-themer'),
            'type'       => Controls_Manager::DIMENSIONS,
            'size_units' => ['px', '%'],
            'selectors'  => [
                '{{WRAPPER}} .gsc-logo .site-branding-logo' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
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
		
      printf( '<div class="gva-element-%s gva-element">', $this->get_name() );
         include $this->get_template('logo.php');
      print '</div>';
	}
}

$widgets_manager->register( new GVAElement_Logo );
