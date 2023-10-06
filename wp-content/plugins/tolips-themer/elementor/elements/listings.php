<?php
if ( ! defined( 'ABSPATH' ) ) {
	 exit; // Exit if accessed directly.
}

use Elementor\Controls_Manager;
use Elementor\Group_Control_Typography;
use Elementor\Scheme_Typography;

/**
 * Class GVAElement_Posts_Grid
 */
class GVAElement_Listings extends GVAElement_Base{

	 public function get_name() {
		  return 'gva-listings';
	 }

	 public function get_title() {
		  return esc_html__('GVA Listing Posts', 'tolips-themer');
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
		return [ 'listings', 'content', 'carousel', 'grid' ];
	}

	public function get_script_depends() {
		return [
			'jquery.owl.carousel',
			'gavias.elements',
		];
	}

	public function get_style_depends() {
		return [
			'owl-carousel-css',
		];
	}

	private function get_categories_list($taxonomy){
		$categories = array();

		$categories['none'] = esc_html__( 'None', 'tolips-themer' );
		$tax_terms = get_terms( $taxonomy );
		if ( ! empty( $tax_terms ) && ! is_wp_error( $tax_terms ) ){
		  foreach( $tax_terms as $item ) {
			 $categories[$item->slug] = $item->name;
		  }
		}
		return $categories;
	}

	protected function register_controls() {
	  	$this->start_controls_section(
			'section_query',
			[
				 'label' => esc_html__('Query & Layout', 'tolips-themer'),
				 'tab'   => Controls_Manager::TAB_CONTENT,
			]
	  	);
		$this->add_control(
         'listing_type_id',
         [
            'label'   => esc_html__('Listing Type', 'tolips-themer'),
            'type'    => Controls_Manager::SELECT,
            'options' => $this->ulisting_type(),
            'default' => 'select',
         ]
      );
	  	$this->add_control(
         'sort_type',
         [
             'label'       => esc_html__('Sort by', 'tolips-themer'),
             'type'        => Controls_Manager::SELECT,
             'options'     => [
                 'latest'   => esc_html__('Latest', 'tolips-themer'),
                 'featured' => esc_html__('Featured', 'tolips-themer'),
                 'popular'  => esc_html__('Popular', 'tolips-themer'),
                 'category' => esc_html__('Category', 'tolips-themer'),
             ],
             'default'     => 'latest'
         ]
     	);

	  	$this->add_control(
		 	'categories',
		 	[
				'label' => esc_html__( 'Select By Category', 'tolips-themer' ),
				'type' => Controls_Manager::SELECT2,
				'multiple'    => true,
				'options'   => $this->get_categories_list('listing-category'),
				'default' => '',
				'condition' => [
               'sort_type' => ['category'],
            ],
			]
	  	);

	  	$this->add_control(
			'posts_per_page',
			[
				'label' => esc_html__( 'Posts Per Page', 'tolips-themer' ),
				'type' => Controls_Manager::NUMBER,
				'default' => 6,
			]
	  	);

		$this->add_control( // xx Layout
			'layout_heading',
			[
				'label'   => esc_html__( 'Layout', 'tolips-themer' ),
				'type'    => Controls_Manager::HEADING,
			]
		);
		$this->add_control(
			'layout',
			[
				 'label'   => esc_html__( 'Layout Display', 'tolips-themer' ),
				 'type'    => Controls_Manager::SELECT,
				 'default' => 'list',
				 'options' => [
					  'carousel'  => esc_html__( 'Carousel', 'tolips-themer' ),
					  'grid'      => esc_html__( 'Grid', 'tolips-themer' ),
					  'single'    => esc_html__( 'Single', 'tolips-themer' ),
				 ]
			]
	  	);
		$this->add_control(
			'subtitle_text',
			[
				'label' => __( 'Sub Title', 'tolips-themer' ),
				'type' => Controls_Manager::TEXT,
				'placeholder' => __( 'Enter your subtitle', 'tolips-themer' ),
				'default' => __( 'Property Featured', 'tolips-themer' ),
				'condition' => [
					'layout' => ['single']
            ],
			]
		);
		$this->add_control(
			'text_color',
			[
				'label' => __( 'Text Color', 'tolips-themer' ),
				'type' => Controls_Manager::SELECT,
				'options' => [
					  'text-default'  	=> esc_html__( 'Text Default Color', 'tolips-themer' ),
					  'text-white-color' => esc_html__( 'Text White Color', 'tolips-themer' ),
				 ],
				'default' => 'text-default',
				'condition' => [
					'layout' => ['single']
            ],
			]
		);
	  	$this->add_control(
			'style',
			[
				'label'     => esc_html__('Style', 'tolips-themer'),
				'type'      => \Elementor\Controls_Manager::SELECT,
				'options' => [
					'grid-1'                => esc_html__( 'Item Style I', 'tolips-themer' ),
					'grid-2'                => esc_html__( 'Item Style II', 'tolips-themer' )
				],
			  'default' => 'grid-1',
			  'condition' => [
            	'sort_type' => ['grid', 'list']
         	],
			]
	  	);
	  	$this->add_control(
			'image_size',
			[
				'label'     => esc_html__('Image Size', 'tolips-themer'),
				'type'      => \Elementor\Controls_Manager::SELECT,
				'options'   => $this->get_thumbnail_size(),
				'default'   => 'tolips_medium'
			]
		);

		$this->end_controls_section();

		$this->add_control_carousel(false, array('layout' => 'carousel'));

		$this->add_control_grid(array('layout' => 'grid'));

	}

	 protected function render() {
		$settings = $this->get_settings_for_display();
		  printf( '<div class="gva-element-%s gva-element">', $this->get_name() );
		  $type_id  = isset($settings['listing_type_id']) ? $settings['listing_type_id'] : 0;

		  if( $type_id == '00select' ){
		  	
		  	 	echo '<div class="alert alert-warning">' . esc_html__( 'You need select "Listing Type"', 'tolips-themer' );
		  
		  }else{

	        $layout     = $settings['layout'];
	        	
	        	$args = array(
	        		'sort_type' 		=> $settings['sort_type'],
	        		'listing_type_id' => $type_id,
	        		'limit'				=> $settings['posts_per_page'],
	        		'category'			=> $settings['categories'] ? implode(',', $settings['categories']) : ''
	        	); 

	        	$listings = uListing\Classes\StmListing::uListing_posts_view_get_listings($args);

	         if( !empty($layout) ){
	            include $this->get_template('listings/' . $layout . '.php');
	        	}

	      }	

		  echo '</div>'; 
	 }

}

$widgets_manager->register(new GVAElement_Listings());
