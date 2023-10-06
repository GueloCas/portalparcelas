<?php

if (!defined('ABSPATH')) {
	 exit; // Exit if accessed directly.
}

use Elementor\Controls_Manager;
use Elementor\Utils;
use Elementor\Scheme_Color;
use Elementor\Group_Control_Typography;
use Elementor\Scheme_Typography;
use Elementor\Group_Control_Border;
use Elementor\Group_Control_Image_Size;

class GVAElement_Listings_Banner extends GVAElement_Base{

	/**
	 	* Get widget name.
	 	*
	 	* Retrieve testimonial widget name.
	  	*
	  	* @since  1.0.0
	  	* @access public
	  	*
	  	* @return string Widget name.
	 */
	public function get_name() {
		return 'gva-listings-banner';
	}

	/**
	  * Get widget title.
	  *
	  * Retrieve testimonial widget title.
	  *
	  * @since  1.0.0
	  * @access public
	  *
	  * @return string Widget title.
	*/
	public function get_title() {
		 return __('GVA Listing Banner', 'tolips-themer');
	}

	/**
	  * Get widget icon.
	  *
	  * Retrieve testimonial widget icon.
	  *
	  * @since  1.0.0
	  * @access public
	  *
	  * @return string Widget icon.
	 */
	public function get_icon() {
		return 'eicon-posts-carousel';
	}

	public function get_keywords() {
		return [ 'listings', 'banner', 'content' ];
	}

	public function get_script_depends() {
		return array();
	}

	public function get_style_depends() {
		return array();
	}

	/**
	  * Register testimonial widget controls.
	  *
	  * Adds different input fields to allow the user to change and customize the widget settings.
	  *
	  * @since  1.0.0
	  * @access protected
	 */
	protected function register_controls() {
		$this->start_controls_section(
			'section_content',
			[
				'label' => __('Content', 'tolips-themer'),
			]
		);
		$this->add_control(
			'subtitle',
			[
				'label' => __( 'SubTitle', 'tolips-themer' ),
				'type' => Controls_Manager::TEXT,
				'label_block'	=> true,
				'placeholder' => esc_html__( 'Add your Sub Title', 'tolips-themer' ),
				'default' => esc_html__('Inside Europe', 'tolips-themer' )
			]
		);
		$this->add_control(
			'title',
			[
				'label' => __( 'Title', 'tolips-themer' ),
				'type' => Controls_Manager::TEXT,
				'label_block'	=> true,
				'placeholder' => esc_html__( 'Add your Title', 'tolips-themer' ),
				'default' => esc_html__("Let have dinner", 'tolips-themer' )
			]
		);
		$this->add_control(
			'listing_type',
			[
            'label'   => esc_html__('Listing Type', 'tolips-themer'),
            'type'    => Controls_Manager::SELECT,
            'options' => $this->ulisting_type(),
            'default' => 'select',
         ]
		);
		$this->add_control(
			'taxonomy',
			[
				'label' => __( 'Taxonomy', 'tolips-themer' ),
				'type' => Controls_Manager::SELECT,
				'label_block'	=> true,
				'options' => [
				  'listing-category' => esc_html__('Listing Category Taxonomy', 'tolips-themer'),
				  'listing-region' => esc_html__('Listing Region Taxonomy', 'tolips-themer'),
				],
				'default' => 'listing-category',
			]
		);

		$this->add_control(
			'term_slug',
			[
				'label' => __( 'Region & Category Slug', 'tolips-themer' ),
				'type' => Controls_Manager::TEXT,
				'label_block'	=> true,
				'placeholder' => esc_html__( 'Term slug', 'tolips-themer' ),
				'default' => ''
			]
		);
		$this->add_control(
			'image',
			[
				'label' => __( 'Image', 'tolips-themer' ),
				'type' => Controls_Manager::TEXT,
				'label'      => __('Choose Image', 'tolips-themer'),
				'default'    => [
					 'url' => GAVIAS_TOLIPS_PLUGIN_URL . 'elementor/assets/images/image-1.jpg',
				],
				'dynamic' => [
					'active' => true,
				],
				'type'       => Controls_Manager::MEDIA,
				'show_label' => false,
			]
		);

		$this->add_control(
			'link_custom',
			[
				'label' => __( 'Link Custom', 'tolips-themer' ),
				'type' => Controls_Manager::TEXT,
				'label_block'	=> true,
				'default' => ''
			]
		);

		$this->add_control(
			'image_size',
			[
				'label'     => __('Image Size', 'tolips-themer'),
				'type'      => \Elementor\Controls_Manager::SELECT,
				'options'   => $this->get_thumbnail_size(),
				'default'   => 'full'
			]
		);
		$this->add_control(
			'content_align',
			[
				'label' => __( 'Alignment Text', 'tolips-themer' ),
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
			'show_number_content',
			[
				'label'   => __( 'Show number content', 'tolips-themer' ),
				'type'    => Controls_Manager::SWITCHER,
				'default' => 'no'
			]
		 );
		$this->end_controls_section();


		  // Icon Styling
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
				'label' => __( 'Icon Color', 'tolips-themer' ),
				'type' => Controls_Manager::COLOR,
				'default' => '',
				'selectors' => [
				  '{{WRAPPER}} .gsc-icon-box-group .icon-box-item-content .box-icon i' => 'color: {{VALUE}};',
				  '{{WRAPPER}} .gsc-icon-box-group .icon-box-item-content svg' => 'fill: {{VALUE}};'
				],
			 ]
		  );

		  $this->add_responsive_control(
			 'icon_size',
			 [
				'label' => __( 'Size', 'tolips-themer' ),
				'type' => Controls_Manager::SLIDER,
				'default' => [
				  'size' => 60
				],
				'range' => [
				  'px' => [
					 'min' => 20,
					 'max' => 80,
				  ],
				],
				'selectors' => [
				  '{{WRAPPER}} .gsc-icon-box-group .icon-box-item-content .box-icon i' => 'font-size: {{SIZE}}{{UNIT}};',
				  '{{WRAPPER}} .gsc-icon-box-group .icon-box-item-content .box-icon svg' => 'width: {{SIZE}}{{UNIT}};'
				],
			 ]
		  );

		  $this->add_responsive_control(
			 'icon_space',
			 [
				'label' => __( 'Spacing', 'tolips-themer' ),
				'type' => Controls_Manager::SLIDER,
				'default' => [
				  'size' => 0,
				],
				'range' => [
				  'px' => [
					 'min' => 0,
					 'max' => 50,
				  ],
				],
				'selectors' => [
				  '{{WRAPPER}} .gsc-icon-box-group .icon-box-item-content .icon-inner' => 'padding-bottom: {{SIZE}}{{UNIT}};',
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
				  '{{WRAPPER}} .gsc-icon-box-group .icon-box-item-content .icon-inner .box-icon' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
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
				  'size'  => 5
				],
				'selectors' => [
				  '{{WRAPPER}} .gsc-icon-box-group .icon-box-item-content .title' => 'padding-bottom: {{SIZE}}{{UNIT}};',
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
				  '{{WRAPPER}} .gsc-icon-box-group .icon-box-item-content .title' => 'color: {{VALUE}};',
				  '{{WRAPPER}} .gsc-icon-box-group .icon-box-item-content .title a' => 'color: {{VALUE}};',
				],
			 ]
		  );

		  $this->add_group_control(
			 Group_Control_Typography::get_type(),
			 [
				'name' => 'title_typography',
				'selector' => '{{WRAPPER}} .gsc-icon-box-group .icon-box-item-content .title, {{WRAPPER}} .gsc-icon-box-group .icon-box-item-content .title a',
			 ]
		  );

		  $this->add_control(
			 'heading_description',
			 [
				'label' => __( 'Description', 'tolips-themer' ),
				'type' => Controls_Manager::HEADING,
				'separator' => 'before',
				'condition' => [
				  'style' => ['style-2'],
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
				  '{{WRAPPER}} .gsc-icon-box-group .icon-box-item-content .desc' => 'color: {{VALUE}};',
				],
				'condition' => [
				  'style' => ['style-2'],
				],
			 ]
		  );

		  $this->add_group_control(
			 Group_Control_Typography::get_type(),
			 [
				'name' => 'description_typography',
				'selector' => '{{WRAPPER}} .gsc-icon-box-group .icon-box-item-content',
				'condition' => [
				  'style' => ['style-2'],
				],

			 ]
		  );

		  $this->end_controls_section();
	 }

	 /**
	  * Render testimonial widget output on the frontend.
	  *
	  * Written in PHP and used to generate the final HTML.
	  *
	  * @since  1.0.0
	  * @access protected
	  */
	 protected function render() {
		$settings = $this->get_settings_for_display();
		printf( '<div class="gva-element-%s gva-element">', $this->get_name() );
			include $this->get_template('listings-banner.php');
		print '</div>';
	 }

}

$widgets_manager->register(new GVAElement_Listings_Banner());
