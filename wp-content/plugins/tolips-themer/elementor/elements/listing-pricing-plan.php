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
class GVAElement_Listings_Pricing_Plan extends GVAElement_Base{

	public function get_name() {
		return 'gva-listings-pricing-plan';
	}

	public function get_title() {
		return esc_html__('GVA Listings Pricing Plan', 'tolips-themer');
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
		return [ 'listings', 'content', 'plan', 'pricing' ];
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
			'style',
			[
				'label'   => esc_html__( 'Style', 'tolips-themer' ),
				'type'    => Controls_Manager::SELECT,
				'default' => 'style-1',
				'options' => [
					'style-1'  => esc_html__( 'Style I', 'tolips-themer' )
				]
			]
		);
			
		$this->end_controls_section();
	}


	protected function render() {
		$settings = $this->get_settings_for_display();
		printf( '<div class="gva-element-%s gva-element">', $this->get_name() );

		  	echo '<div class="gsc-listing-pricing-plan ' . $settings['style'] . '">';
		  		echo do_shortcode( '[ulisting-pricing-plan]' );
		   echo '</div>';		

		echo '</div>'; 
	}

}

$widgets_manager->register(new GVAElement_Listings_Pricing_Plan());
