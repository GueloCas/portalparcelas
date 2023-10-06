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
class GVAElement_Countdown extends GVAElement_Base {

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
		return 'gva-countdown';
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
		return __( 'GVA Countdown', 'tolips-themer' );
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
		return 'eicon-countdown';
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
		return [ 'countdown', 'time', 'text' ];
	}

	public function get_script_depends() {
      return [
         'countdown'
      ];
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
				'type' => Controls_Manager::TEXTAREA,
				'dynamic' => [
					'active' => true,
				],
				'placeholder' => __( 'Enter your title', 'tolips-themer' ),
				'default' => __( 'Add Your Heading Text Here', 'tolips-themer' ),
				'label_block' => true
			]
		);
		$this->add_control(
			'year',
			[
				'label' => __( 'Year', 'tolips-themer' ),
				'type' => Controls_Manager::TEXT,
				'default' => date('Y'),
				'placeholder' => __( 'ex: 2019', 'tolips-themer' ),
				'label_block' => true
			]
		);
		$this->add_control(
			'month',
			[
				'label' => __( 'Month', 'tolips-themer' ),
				'type' => Controls_Manager::TEXT,
				'default' => '12',
				'placeholder' => __( 'ex: 12', 'tolips-themer' ),
			]
		);
		$this->add_control(
			'day',
			[
				'label' => __( 'Day', 'tolips-themer' ),
				'type' => Controls_Manager::TEXT,
				'default' => '01',
				'placeholder' => __( 'ex: 01', 'tolips-themer' ),
			]
		);
		$this->add_control(
			'hour',
			[
				'label' => __( 'Hour', 'tolips-themer' ),
				'type' => Controls_Manager::TEXT,
				'default' => '00',
				'placeholder' => __( 'ex: 00', 'tolips-themer' ),
			]
		);
		$this->add_control(
			'minutes',
			[
				'label' => __( 'Minutes', 'tolips-themer' ),
				'type' => Controls_Manager::TEXT,
				'default' => '00',
				'placeholder' => __( 'ex: 00', 'tolips-themer' ),
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
		$this->end_controls_section();

		//Box Styling
		$this->start_controls_section(
			'section_box_style',
			[
				'label' => __( 'Box', 'tolips-themer' ),
				'tab' => Controls_Manager::TAB_STYLE,
			]
		);
		$this->add_responsive_control(
			'box_padding',
			[
				'label' => __( 'Padding', 'tolips-themer' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%' ],
				'default' => [
					'top' 		=> 40,
					'right' 		=> 40,
					'left'		=> 40,
					'bottom'		=> 40,
					'unit'		=> 'px'
				],
				'selectors' => [
					'{{WRAPPER}} .gsc-countdown' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);
		$this->add_control(
			'box_background',
			[
				'label' => __( 'Background Color', 'tolips-themer' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .gsc-countdown' => 'background: {{VALUE}};',
				],
			]
		);
		$this->end_controls_section();

		//Content Styling
		$this->start_controls_section(
			'section_content_style',
			[
				'label' => __( 'Title', 'tolips-themer' ),
				'tab' => Controls_Manager::TAB_STYLE,
			]
		);
		$this->add_control(
			'color',
			[
				'label' => __( 'Text Color', 'tolips-themer' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .gsc-countdown .content-inner .title' => 'color: {{VALUE}};',
				],
			]
		);
		
		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name' => 'typography_title',
				'selector' => '{{WRAPPER}} .gsc-countdown .content-inner .title',
			]
		);
		$this->add_responsive_control(
			'space',
			[
				'label' => __( 'Spacing', 'tolips-themer' ),
				'type' => Controls_Manager::SLIDER,
				'default' => [
					'size' => 60,
				], 
				'range' => [
					'px' => [
						'min' => 0,
						'max' => 200,
					],
				],
				'selectors' => [
					'{{WRAPPER}} .gsc-countdown .content-inner .title' => 'margin-bottom: {{SIZE}}{{UNIT}};',
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
         include $this->get_template('countdown.php');
      print '</div>';
	}

}
$widgets_manager->register(new GVAElement_Countdown());

