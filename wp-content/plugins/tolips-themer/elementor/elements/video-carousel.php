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

class GVAElement_Video_Carousel extends GVAElement_Base{

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
        return 'gva-video-carousel';
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
        return __('GVA Video Carousel', 'tolips-themer');
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
        return 'eicon-media-carousel';
    }

    public function get_keywords() {
        return [ 'video', 'content', 'carousel' ];
    }

    public function get_script_depends() {
      return [
          'jquery.owl.carousel',
          'gavias.elements',
      ];
    }

    public function get_style_depends() {
      return array('owl-carousel-css');
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
            'section_videos',
            [
                'label' => __('Videos', 'tolips-themer'),
            ]
        );
        $repeater = new Repeater();
        
        $repeater->add_control(
            'video_title',
            [
                'label'       => __('Title', 'tolips-themer'),
                'type'        => Controls_Manager::TEXT,
                'placeholder' => 'Add your title',
                'label_block' => true
            ]
        );
        $repeater->add_control(
            'video_image',
            [
                'label'      => __('Choose Image', 'tolips-themer'),
                'default'    => [
                    'url' => GAVIAS_TOLIPS_PLUGIN_URL . 'elementor/assets/images/image-1.jpg',
                ],
                'type'       => Controls_Manager::MEDIA,
            ]
        );
        $repeater->add_control(
            'video_link',
            [
                'label'   => __('Video Link', 'tolips-themer'),
                'default' => 'https://www.youtube.com/watch?v=knTiUD5IAww',
                'type'    => Controls_Manager::TEXT,
                'description' => esc_html__( 'You can add youtube/vimeo video link', 'tolips-themer' )
            ]
        );
       
        $this->add_control(
            'videos_content',
            [
                'label'       => __('Video Content', 'tolips-themer'),
                'type'        => Controls_Manager::REPEATER,
                'fields'      => $repeater->get_controls(),
                'title_field' => '',
                'default'     => array(
                    array(
                        'video_image'    => [
                            'url' => GAVIAS_TOLIPS_PLUGIN_URL . 'elementor/assets/images/image-1.jpg',
                        ],
                        'video_link'      => 'https://www.youtube.com/watch?v=knTiUD5IAww',
                    ),
                    array(
                        'video_image'    => [
                            'url' => GAVIAS_TOLIPS_PLUGIN_URL . 'elementor/assets/images/image-1.jpg',
                        ],
                        'video_link'      => 'https://www.youtube.com/watch?v=knTiUD5IAww',
                    ),

                ),
            ]
        );

        $this->add_group_control(
            Elementor\Group_Control_Image_Size::get_type(),
            [
                'name'      => 'video_image', 
                'default'   => 'full',
                'separator' => 'none',
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

         $this->add_control_carousel( false, array());

        // Title Styling
        $this->start_controls_section(
            'section_style_name',
            [
                'label' => __('Title', 'tolips-themer'),
                'tab'   => Controls_Manager::TAB_STYLE,
            ]
        );
        $this->add_control(
            'title_text_color',
            [
                'label'     => __('Text Color', 'tolips-themer'),
                'type'      => Controls_Manager::COLOR,
                'default'   => '',
                'selectors' => [
                    '{{WRAPPER}} .testimonial-name, {{WRAPPER}} .testimonial-name a' => 'color: {{VALUE}};',
                ],
            ]
        );
        $this->add_group_control(
            Group_Control_Typography::get_type(),
            [
                'name'     => 'title_typography',
                'selector' => '{{WRAPPER}} .testimonial-name',
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
        include $this->get_template('video-carousel.php');
      print '</div>';
    }

}
$widgets_manager->register(new GVAElement_Video_Carousel());
