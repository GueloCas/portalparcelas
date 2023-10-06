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
class GVAElement_Call_To_Action extends GVAElement_Base {

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
		return 'gva-call-to-action';
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
		return __( 'GVA Call To Action', 'tolips-themer' );
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
		return 'eicon-archive-title';
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
		return [ 'call to action', 'block', 'action' ];
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
			'sub_title',
			[
				'label' => __( 'Sub Title', 'tolips-themer' ),
				'type' => Controls_Manager::TEXT,
				'dynamic' => [
					'active' => true,
				],
				'placeholder' => __( 'Enter your Sub Title', 'tolips-themer' ),
				'label_block' => true,
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
			'description_text',
			[
				'label' => __( 'Description', 'tolips-themer' ),
				'type' => Controls_Manager::TEXTAREA,
				'dynamic' => [
					'active' => true,
				],
				'placeholder' => __( 'Enter Your Description', 'tolips-themer' ),
			]
		);

		$this->add_control(
			'style',
			[
				'label' => __( 'Style', 'tolips-themer' ),
				'type' => Controls_Manager::SELECT,
				'options' => [
				'button-left' => esc_html__('Button Left', 'tolips-themer'),            
            'button-right' => esc_html__('Button Right', 'tolips-themer'),   
            'button-bottom' => esc_html__('Button Bottom Left', 'tolips-themer'),   
            'button-center' => esc_html__('Button Bottom Center', 'tolips-themer'),
				],
				'default' => 'button-bottom',
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
			'max_width',
			[
				'label' => __( 'Max Width (px)', 'tolips-themer' ),
				'type' => Controls_Manager::SLIDER,
				'default' => [
					'size' => 800,
				],
				'range' => [
					'px' => [
						'min' => 300,
						'max' => 1170,
					],
				],
				'selectors' => [
					'{{WRAPPER}} .gsc-call-to-action .content-inner .cta-content' => 'max-width: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->end_controls_section();

		$this->start_controls_section( //** Section Icon
			'section_Button',
			[
				'label' => __( 'Button', 'tolips-themer' ),
			]
		);

		$this->add_control(
			'button_url',
			[
				'label' => __( 'Button URL', 'tolips-themer' ),
				'type' => Controls_Manager::URL,
				'default' => [
					'url' => '#'
				]
			]
		);

		$this->add_control(
			'button_text',
			[
				'label' => __( 'Button Text', 'tolips-themer' ),
				'type' => Controls_Manager::TEXT,
				'default' => 'Read More'
			]
		);
		$this->add_control(
			'button_style',
			[
				'label' => __( 'Button Style', 'tolips-themer' ),
				'type' => Controls_Manager::SELECT,
				'options' => [
					'btn-theme' 		=> esc_html__('Button Theme', 'tolips-themer'),
					'btn-theme-2' 		=> esc_html__('Button Theme Second', 'tolips-themer'),
					'btn-black' 		=> esc_html__('Button Black', 'tolips-themer')
				],
				'default' => 'btn-theme',
			]
		);
		$this->add_control(
			'button_color',
			[
				'label' => __( 'Button Text Color', 'tolips-themer' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .gsc-call-to-action .button-action .btn-cta' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'button_background',
			[
				'label' => __( 'Button Background', 'tolips-themer' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .gsc-call-to-action .button-action .btn-cta' => 'background: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'button_color_hover',
			[
				'label' => __( 'Button Color Hover', 'tolips-themer' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .gsc-call-to-action .button-action .btn-cta:hover' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'button_background_hover',
			[
				'label' => __( 'Button Background Hover', 'tolips-themer' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .gsc-call-to-action .button-action .btn-cta:hover' => 'background: {{VALUE}}!important;',
					'{{WRAPPER}} .gsc-call-to-action .button-action .btn-cta:after' => 'background: {{VALUE}};',
				],
			]
		);

		$this->end_controls_section();

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
					'{{WRAPPER}} .gsc-call-to-action .title' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name' => 'typography',
				'selector' => '{{WRAPPER}} .gsc-call-to-action .title',
			]
		);

		$this->add_responsive_control(
			'title_space',
			[
				'label' => __( 'Title Spacing', 'tolips-themer' ),
				'type' => Controls_Manager::SLIDER,
				'default' => [
					'size' => 5,
				],
				'range' => [
					'px' => [
						'min' => 0,
						'max' => 100,
					],
				],
				'selectors' => [
					'{{WRAPPER}} .gsc-call-to-action .title' => 'margin-bottom: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'section_sub_title_style',
			[
				'label' => __( 'Sub Title', 'tolips-themer' ),
				'tab' => Controls_Manager::TAB_STYLE,
			]
		);
 
		$this->add_control(
			'sub_title_color',
			[
				'label' => __( 'Text Color', 'tolips-themer' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .gsc-call-to-action .sub-title' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_responsive_control(
			'sub_title_space',
			[
				'label' => __( 'Sub Title Spacing', 'tolips-themer' ),
				'type' => Controls_Manager::SLIDER,
				'default' => [
					'size' => 5,
				],
				'range' => [
					'px' => [
						'min' => 0,
						'max' => 100,
					],
				],
				'selectors' => [
					'{{WRAPPER}} .gsc-call-to-action .sub-title' => 'margin-bottom: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name' => 'typography_sub_title',
				'selector' => '{{WRAPPER}} .gsc-call-to-action .sub-title',
			]
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'section_desc_style',
			[
				'label' => __( 'Description', 'tolips-themer' ),
				'tab' => Controls_Manager::TAB_STYLE,
			]
		);
 
		$this->add_control(
			'desc_color',
			[
				'label' => __( 'Text Color', 'tolips-themer' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .gsc-call-to-action .desc' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name' => 'typography_desc',
				'selector' => '{{WRAPPER}} .gsc-call-to-action .desc',
			]
		);

		$this->add_responsive_control(
			'description_space',
			[
				'label' => __( 'Description Spacing', 'tolips-themer' ),
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
					'{{WRAPPER}} .gsc-call-to-action .desc' => 'margin-bottom: {{SIZE}}{{UNIT}};',
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
         include $this->get_template('call-to-action.php');
      print '</div>';
	}

	public function add_wpml_support() {
		add_filter( 'wpml_elementor_widgets_to_translate', [ $this, 'wpml_widgets_to_translate_filter' ] );
	}

	public function wpml_widgets_to_translate_filter( $widgets ) {
		$widgets[ $this->get_name() ] = [
			'conditions' => [ 'widgetType' => $this->get_name() ],
			'fields'     => [
				[
					'field'       => 'sub_title',
					'type'        => __( 'Sub Title', 'tolips-themer' ),
					'editor_type' => 'LINE'
				],
				[
					'field'       => 'title_text',
					'type'        => __( 'Title', 'tolips-themer' ),
					'editor_type' => 'LINE'
				],
				[
					'field'       => 'description_text',
					'type'        => __( 'Description', 'tolips-themer' ),
					'editor_type' => 'LINE'
				],
			],
		];
		return $widgets;
	}
}
      $widgets_manager->register(new GVAElement_Call_To_Action());
