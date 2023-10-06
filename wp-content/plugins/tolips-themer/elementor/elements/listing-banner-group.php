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
use Elementor\Repeater;

class GVAElement_Listings_Banner_Group extends GVAElement_Base{

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
		return 'gva-listings-banner-group';
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
		 return __('GVA Listing Banner Group', 'tolips-themer');
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
		return [ 'listings', 'banner', 'content', 'group', 'carousel', 'grid' ];
	}

	public function get_script_depends() {
		return [
			'jquery.owl.carousel',
			'gavias.elements'
		];
	}

	public function get_style_depends() {
		return [
			'owl-carousel-css',
		];
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
			'style',
			[
				'label' => __( 'Style', 'tolips-themer' ),
				'type' => Controls_Manager::SELECT,
				'options' => [
				  'style-1' => esc_html__('Style I', 'tolips-themer'),
				  'style-2' => esc_html__('Style II', 'tolips-themer')
				],
				'default' => 'style-1',
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
				'default' => 'listing-region',
			]
		);
		  
		$repeater = new Repeater();
		$repeater->add_control(
			'listing_type',
			[
            'label'   => esc_html__('Listing Type', 'tolips-themer'),
            'type'    => Controls_Manager::SELECT,
            'options' => $this->ulisting_type(),
            'default' => 'select',
         ]
		);
		$repeater->add_control(
			'title',
			[
				'label'       => __('Title', 'tolips-themer'),
				'type'        => Controls_Manager::TEXT,
				'placeholder' => esc_html__( 'Add your Title', 'tolips-themer' ),
				'default'     => 'London',
			]
		);

		$repeater->add_control(
			'term_slug',
			[
				'label'     => __( 'Location Slug', 'tolips-themer' ),
				'type'      => Controls_Manager::TEXT,
				'placeholder' => __( 'Add term slug here', 'tolips-themer' ),
			]
		);

		$repeater->add_control(
			'image',
			[
				'label'      => __('Choose Image', 'tolips-themer'),
				'default'    => [
					'url' => GAVIAS_TOLIPS_PLUGIN_URL . 'elementor/assets/images/image-1.jpg',
				],
				'type'       => Controls_Manager::MEDIA,
				'show_label' => false,
			]
		);

		$repeater->add_control(
			'image_flag',
			[
				'label'      => __('Choose Flag', 'tolips-themer'),
				'type'       => Controls_Manager::MEDIA,
				'default'    => [
					'url' => '',
				],
			]
		);

		$this->add_control(
			 'content_banners',
			 [
				'label'       => __('Listings Banner Content Item', 'tolips-themer'),
				'type'        => Controls_Manager::REPEATER,
				'fields'      => $repeater->get_controls(),
				'title_field' => '{{{ title }}}',
				'default'     => array(
				  array(
					 	'title'  		=> esc_html__( 'London', 'tolips-themer' )
				  ),
				  array(
					 	'title'       => esc_html__( 'California', 'tolips-themer' )
				  ),
				  array(
					 	'title'  		=> esc_html__( 'Las Vegas', 'tolips-themer' )
				  ),
				  array(
					 	'title'  		=> esc_html__( 'New York', 'tolips-themer' )
				  ),
				)
			 ]
		  );
		  $this->add_control( // xx Layout
				'layout_heading',
				[
					 'label'   => __( 'Layout', 'tolips-themer' ),
					 'type'    => Controls_Manager::HEADING,
				]
		  );
			$this->add_control(
				'layout',
				[
					 'label'   => __( 'Layout Display', 'tolips-themer' ),
					 'type'    => Controls_Manager::SELECT,
					 'default' => 'carousel',
					 'options' => [
						  'grid'      => __( 'Grid', 'tolips-themer' ),
						  'carousel'  => __( 'Carousel', 'tolips-themer' ),
					 ]
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
			 'view',
			 [
				'label'   => __('View', 'tolips-themer'),
				'type'    => Controls_Manager::HIDDEN,
				'default' => 'traditional',
			 ]
		  );

		  $this->end_controls_section();

		  $this->add_control_carousel(false, array('layout' => 'carousel'));

		  $this->add_control_grid(array('layout' => 'grid'));

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
		  if( !empty($settings['layout']) ){
			 include $this->get_template('listings-banner-group/' . $settings['layout'] . '.php');
		  }
		print '</div>';
	 }

}

$widgets_manager->register(new GVAElement_Listings_Banner_Group());
