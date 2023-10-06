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

class GVAElement_Testimonial extends GVAElement_Base{

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
        return 'gva-testimonials';
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
        return __('GVA Testimonials', 'tolips-themer');
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
        return 'eicon-testimonial-carousel';
    }

    public function get_keywords() {
        return [ 'testimonial', 'content', 'carousel' ];
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
            'section_testimonial',
            [
                'label' => __('Testimonials', 'tolips-themer'),
            ]
        );
        $this->add_control(
            'style',
            array(
                'label'   => esc_html__( 'Style', 'tolips-themer' ),
                'type'    => Controls_Manager::SELECT,
                'default' => 'style-1',
                'options' => [
                  'style-1' => esc_html__('Carousel I', 'tolips-themer'),
                  'style-2' => esc_html__('Carousel II', 'tolips-themer'),
                  'style-3' => esc_html__('Carousel III', 'tolips-themer'),
                ]
            )
         );

        $repeater = new Repeater();
        
        $repeater->add_control(
            'testimonial_content',
            [
                'label'       => __('Content', 'tolips-themer'),
                'type'        => Controls_Manager::TEXTAREA,
                'default'     => 'I was impresed by the moling services, not lorem ipsum is simply free text of used by refreshing. Neque porro este qui dolorem ipsum quia.',
                'label_block' => true,
                'rows'        => '10',
            ]
        );
        $repeater->add_control(
            'testimonial_image',
            [
                'label'      => __('Choose Image', 'tolips-themer'),
                'default'    => [
                    'url' => GAVIAS_TOLIPS_PLUGIN_URL . 'elementor/assets/images/testimonial.png',
                ],
                'type'       => Controls_Manager::MEDIA,
                'show_label' => false,
            ]
        );

        $repeater->add_control(
            'testimonial_name',
            [
                'label'   => __('Name', 'tolips-themer'),
                'default' => 'John Doe',
                'type'    => Controls_Manager::TEXT,
            ]
        );
        
        $repeater->add_control(
            'testimonial_job',
            [
                'label'   => __('Job', 'tolips-themer'),
                'default' => 'Designer',
                'type'    => Controls_Manager::TEXT,
            ]
        );        
 
        $this->add_control(
            'testimonials',
            [
                'label'       => __('Testimonials Content Item', 'tolips-themer'),
                'type'        => Controls_Manager::REPEATER,
                'fields'      => $repeater->get_controls(),
                'title_field' => '{{{ testimonial_name }}}',
                'default'     => array(
                    array(
                        'testimonial_content'  => esc_html__( 'I was impresed by the moling services, not lorem ipsum is simply free text of used by refreshing. Neque porro este qui dolorem ipsum quia.', 'tolips-themer' ),
                        'testimonial_name'     => esc_html__( 'Christine Eve', 'tolips-themer' ),
                        'testimonial_job'      => esc_html__( 'Founder & CEO', 'tolips-themer' ),
                        'testimonial_image'    => [
                            'url' => GAVIAS_TOLIPS_PLUGIN_URL . 'elementor/assets/images/testimonial.png',
                        ]
                    ),
                    array(
                        'testimonial_content'  => esc_html__( 'I was impresed by the moling services, not lorem ipsum is simply free text of used by refreshing. Neque porro este qui dolorem ipsum quia.', 'tolips-themer' ),
                        'testimonial_name'     => esc_html__( 'Kevin Smith', 'tolips-themer' ),
                        'testimonial_job'      => esc_html__( 'Customer', 'tolips-themer' ),
                        'testimonial_image'    => [
                            'url' => GAVIAS_TOLIPS_PLUGIN_URL . 'elementor/assets/images/testimonial.png',
                        ]
                    ),
                    array(
                        'testimonial_content'  => esc_html__( 'I was impresed by the moling services, not lorem ipsum is simply free text of used by refreshing. Neque porro este qui dolorem ipsum quia.', 'tolips-themer' ),
                        'testimonial_name'     => esc_html__( 'Jessica Brown', 'tolips-themer' ),
                        'testimonial_job'      => esc_html__( 'Founder & CEO', 'tolips-themer' ),
                        'testimonial_image'    => [
                            'url' => GAVIAS_TOLIPS_PLUGIN_URL . 'elementor/assets/images/testimonial.png',
                        ]
                    ),
                    array(
                        'testimonial_content'  => esc_html__( 'I was impresed by the moling services, not lorem ipsum is simply free text of used by refreshing. Neque porro este qui dolorem ipsum quia.', 'tolips-themer' ),
                        'testimonial_name'     => esc_html__( 'David Anderson', 'tolips-themer' ),
                        'testimonial_job'      => esc_html__( 'Customer', 'tolips-themer' ),
                        'testimonial_image'    => [
                            'url' => GAVIAS_TOLIPS_PLUGIN_URL . 'elementor/assets/images/testimonial.png',
                        ]
                    ),
                    array(
                        'testimonial_content'  => esc_html__( 'I was impresed by the moling services, not lorem ipsum is simply free text of used by refreshing. Neque porro este qui dolorem ipsum quia.', 'tolips-themer' ),
                        'testimonial_name'     => esc_html__( 'Susan Neill', 'tolips-themer' ),
                        'testimonial_job'      => esc_html__( 'Founder & CEO', 'tolips-themer' ),
                        'testimonial_image'    => [
                            'url' => GAVIAS_TOLIPS_PLUGIN_URL . 'elementor/assets/images/testimonial.png',
                        ]
                    ),
                ),
            ]
        );

        $this->add_group_control(
            Elementor\Group_Control_Image_Size::get_type(),
            [
                'name'      => 'testimonial_image', 
                'default'   => 'full',
                'separator' => 'none',
                'condition' => [
                    'style' => array('style-1', 'style-2')
                ]
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

         $this->add_control_carousel( false,
            array(
               'style' => ['style-1', 'style-2', 'style-3']
            )
         );

        // Style.
        $this->start_controls_section(
            'section_style_content',
            [
                'label' => __('Content', 'tolips-themer'),
                'tab'   => Controls_Manager::TAB_STYLE,
            ]
        );
 
        $this->add_control(
            'content_content_color',
            [
                'label'     => __('Text Color', 'tolips-themer'),
                'type'      => Controls_Manager::COLOR,
                'default'   => '',
                'selectors' => [
                    '{{WRAPPER}} .gva-testimonial-carousel .testimonial-content' => 'color: {{VALUE}};',
                    '{{WRAPPER}} .gva-testimonial-carousel .icon-quote' => 'color: {{VALUE}};',
                ],
            ]
        );

        $this->add_group_control(
            Group_Control_Typography::get_type(),
            [
                'name'     => 'content_typography',
                'selector' => '{{WRAPPER}} .gva-testimonial-carousel .testimonial-item .testimonial-content',
            ]
        );

        $this->end_controls_section();

        // Image Styling
        $this->start_controls_section(
            'section_style_image',
            [
                'label'     => __('Image', 'tolips-themer'),
                'tab'       => Controls_Manager::TAB_STYLE,
            ]
        );

        $this->add_control(
            'image_size',
            [
                'label'      => __('Image Size', 'tolips-themer'),
                'type'       => Controls_Manager::SLIDER,
                'size_units' => ['px'],
                'range'      => [
                    'px' => [
                        'min' => 20,
                        'max' => 200,
                    ],
                ],
                'selectors'  => [
                    '{{WRAPPER}} .gva-testimonial-carousel .testimonial-image img' => 'width: {{SIZE}}{{UNIT}};height: {{SIZE}}{{UNIT}};',
                ],
            ]
        );

        $this->add_group_control(
            Group_Control_Border::get_type(),
            [
                'name'      => 'image_border',
                'selector'  => '{{WRAPPER}} .gva-testimonial-carousel .testimonial-image img',
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
                    '{{WRAPPER}} .gva-testimonial-carousel .testimonial-image img' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
            ]
        );

        $this->end_controls_section();

        // Name Styling
        $this->start_controls_section(
            'section_style_name',
            [
                'label' => __('Name', 'tolips-themer'),
                'tab'   => Controls_Manager::TAB_STYLE,
            ]
        );

        $this->add_control(
            'name_text_color',
            [
                'label'     => __('Text Color', 'tolips-themer'),
                'type'      => Controls_Manager::COLOR,
                'default'   => '',
                'selectors' => [
                    '{{WRAPPER}} .testimonial-name' => 'color: {{VALUE}};',
                    '{{WRAPPER}} .dot' => 'color: {{VALUE}};',
                ],
            ]
        );

        $this->add_group_control(
            Group_Control_Typography::get_type(),
            [
                'name'     => 'name_typography',
                'selector' => '{{WRAPPER}} .testimonial-name',
            ]
        );

        $this->end_controls_section();

        // Job Styling
        $this->start_controls_section(
            'section_style_job',
            [
                'label' => __('Job', 'tolips-themer'),
                'tab'   => Controls_Manager::TAB_STYLE,
            ]
        );

        $this->add_control(
            'job_text_color',
            [
                'label'     => __('Text Color', 'tolips-themer'),
                'type'      => Controls_Manager::COLOR,
                'default'   => '',
                'selectors' => [
                    '{{WRAPPER}} .testimonial-job, {{WRAPPER}} .testimonial-job a' => 'color: {{VALUE}};',
                ],
            ]
        );

        $this->add_group_control(
            Group_Control_Typography::get_type(),
            [
                'name'     => 'job_typography',
                'selector' => '{{WRAPPER}} .elementor-testimonial-job',
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
      if(isset($settings['style']) && $settings['style']){
         include $this->get_template('testimonials/gva-testimonials-' . $settings['style'] . '.php');
      }
      print '</div>';
    }

}

$widgets_manager->register(new GVAElement_Testimonial());
