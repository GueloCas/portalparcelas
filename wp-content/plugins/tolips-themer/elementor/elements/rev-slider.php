<?php

if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly.
}

use Elementor\Controls_Manager;
use Elementor\Utils;

class GVAElement_Rev_Slider extends GVAElement_Base{

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
        return 'gva-rev-slider';
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
        return __('GVA Revolution Slider', 'tolips-themer');
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
        return [ 'revolution', 'slider', 'images' ];
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
        $slider = new RevSlider();
        $arrSliders = $slider->getArrSliders();

        $revsliders = array('' => '-- Choose Slider --');
        if ( $arrSliders ) {
            foreach ( $arrSliders as $slider ) {
                $revsliders[ $slider->getAlias() ] = $slider->getTitle();
            }
        } else {
            $revsliders[ __( 'No sliders found', 'tolips-themer' ) ] = 0;
        }

        $this->start_controls_section(
            'section_content',
            [
                'label' => __('Content', 'tolips-themer'),
            ]
        );
        $this->add_control(
            'alias_slider',
            [
                'label'   => __( 'Choose Slider:', 'tolips-themer' ),
                'type'    => Controls_Manager::SELECT,
                'label_block' => true,
                'options' => $revsliders
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
              global $rs_loaded_by_editor;
      if ( \Elementor\Plugin::$instance->editor->is_edit_mode() ) $rs_loaded_by_editor = true;
      $settings = $this->get_settings_for_display();
      printf( '<div class="gva-element-%s gva-element">', $this->get_name() );
        include $this->get_template('rev-slider.php');
      print '</div>';
      if ( \Elementor\Plugin::$instance->editor->is_edit_mode() ) $rs_loaded_by_editor = false;
      
    }
}

$widgets_manager->register(new GVAElement_Rev_Slider());
