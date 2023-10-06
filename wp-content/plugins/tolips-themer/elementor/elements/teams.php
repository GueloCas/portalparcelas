<?php

if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly.
}

use Elementor\Controls_Manager;
use Elementor\Scheme_Color;
use Elementor\Group_Control_Typography;
use Elementor\Scheme_Typography;
use Elementor\Group_Control_Border;
use Elementor\Group_Control_Image_Size;

class GVAElement_Teams extends  GVAElement_Base {

    public function get_name() {
        return 'gva-teams';
    }

    public function get_title() {
        return __('GVA Teams', 'tolips-themer');
    }

    public function get_keywords() {
        return [ 'team', 'content', 'carousel', 'grid' ];
    }

    public function get_icon() {
        return 'eicon-person';
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

    private function get_posts() {
        $posts = array();

        $loop = new \WP_Query( array(
            'post_type' => array('gva_team'),
            'posts_per_page' => -1,
            'post_status'=>array('publish'),
        ) );

        $posts['none'] = __('None', 'tolips-themer');

        while ( $loop->have_posts() ) : $loop->the_post();
            $id = get_the_ID();
            $title = get_the_title();
            $posts[$id] = $title;
        endwhile;

        wp_reset_postdata();

        return $posts;
    }

    protected function register_controls() {

        $this->start_controls_section(
            'section_team_query',
            [
                'label' => __('Teams Query', 'tolips-themer'),
            ]
        );
     
        $this->add_control(
            'post_ids',
            [
                'label' => __( 'Select Individually', 'tolips-themer' ),
                'type' => Controls_Manager::SELECT2,
                'default' => '',
                'multiple'    => true,
                'label_block' => true,
                'options'   => $this->get_posts()
            ]  
        );

        $this->add_control(
            'posts_per_page',
            [
                'label' => __( 'Posts Per Page', 'tolips-themer' ),
                'type' => Controls_Manager::NUMBER,
                'default' => 6,
            ]
        );

        $this->add_control(
            'orderby',
            [
                'label'   => __( 'Order By', 'tolips-themer' ),
                'type'    => Controls_Manager::SELECT,
                'default' => 'post_date',
                'options' => [
                    'post_date'  => __( 'Date', 'tolips-themer' ),
                    'post_title' => __( 'Title', 'tolips-themer' ),
                    'menu_order' => __( 'Menu Order', 'tolips-themer' ),
                    'rand'       => __( 'Random', 'tolips-themer' ),
                ],
            ]
        );

        $this->add_control(
            'order',
            [
                'label'   => __( 'Order', 'tolips-themer' ),
                'type'    => Controls_Manager::SELECT,
                'default' => 'desc',
                'options' => [
                    'asc'  => __( 'ASC', 'tolips-themer' ),
                    'desc' => __( 'DESC', 'tolips-themer' ),
                ]
            ]
        );

        $this->end_controls_section();

        $this->start_controls_section(
            'section_team_layout',
            [
                'label' => __('Layout', 'tolips-themer'),
                'type'  => Controls_Manager::SECTION,
            ]
        );
         $this->add_control(
            'layout',
            [
                'label'   => __( 'Layout Display', 'tolips-themer' ),
                'type'    => Controls_Manager::SELECT,
                'default' => 'grid',
                'options' => [
                    'grid'      => __( 'Grid', 'tolips-themer' ),
                    'carousel'  => __( 'Carousel', 'tolips-themer' ),
                ]
            ]
        );
        $this->add_control(
            'style',
            [
                'label'     => __('Style', 'tolips-themer'),
                'type'      => \Elementor\Controls_Manager::SELECT,
                'options' => [
                    'team-style-1'           => __( 'Team Style I', 'tolips-themer' ),
                ],
                 'default' => 'team-style-1',
            ]
        );
        $this->add_control(
            'image_size',
            [
               'label'     => __('Style', 'tolips-themer'),
               'type'      => \Elementor\Controls_Manager::SELECT,
               'options'   => $this->get_thumbnail_size(),
               'default'   => 'tolips_medium'
            ]
        );
        $this->add_control(
            'excerpt_words',
            [
                'label'     => __('Excerpt Words', 'tolips-themer'),
                'type'      => Controls_Manager::NUMBER,
                'default'   => '30',
                'condition' => [
                    'style' => 'team-style-1'
                ],
            ]
        );

        $this->add_control(
            'show_skills',
            [
                'label'     => __('Show Skills', 'tolips-themer'),
                'type'      => Controls_Manager::SWITCHER,
                'default'   => 'no',
            ]
        );

        $this->add_control(
            'pagination',
            [
                'label'     => __('Pagination', 'tolips-themer'),
                'type'      => Controls_Manager::SWITCHER,
                'default'   => 'no',
                'condition' => [
                    'layout' => 'grid'
                ],
            ]
        );

        $this->end_controls_section();

        $this->add_control_carousel(false, array('layout' => 'carousel'));

        $this->add_control_grid(array('layout' => 'grid'));

        // Name Styling
        $this->start_controls_section(
            'section_style_team_name',
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
                    '{{WRAPPER}} .team-block.team-v2 .team-name, {{WRAPPER}} .team-block.team-v2 .team-name a' => 'color: {{VALUE}};',
                ],
            ]
        );

        $this->add_group_control(
            Group_Control_Typography::get_type(),
            [
                'name'     => 'name_typography',
                'selector' => '{{WRAPPER}} .team-block.team-v2 .team-name, {{WRAPPER}} .team-block.team-v2 .team-name a',
            ]
        );

        $this->end_controls_section();

        // Job Styling
        $this->start_controls_section(
            'section_style_team_job',
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
                    '{{WRAPPER}} .team-block.team-v2 .team-job' => 'color: {{VALUE}};'
                ],
            ]
        );

      

        $this->end_controls_section();

        // Information.
        $this->start_controls_section(
            'section_style_team_social',
            [
                'label' => __('Social', 'tolips-themer'),
                'tab'   => Controls_Manager::TAB_STYLE,
            ]
        );

        $this->add_responsive_control(
            'social_size',
            [
                'label' => __( 'Social Size', 'tolips-themer' ),
                'type' => Controls_Manager::SLIDER,
                'default' => [
                    'size' => 16
                ],
                'range' => [
                    'px' => [
                        'min' => 12,
                        'max' => 50,
                    ],
                ],
                'selectors' => [
                    '{{WRAPPER}} .team-block.team-v2 .team-image .socials-team a' => 'font-size: {{SIZE}}{{UNIT}};',
                ],
            ]
        );

        $this->add_control(
            'social_color',
            [
                'label'     => __('Social Color', 'tolips-themer'),
                'type'      => Controls_Manager::COLOR,
                'default'   => '',
                'selectors' => [
                    '{{WRAPPER}} .team-block.team-v2 .team-image .socials-team a' => 'color: {{VALUE}};',
                ],
            ]
        );

        $this->add_control(
            'social_hover_color',
            [
                'label'     => __('Social Hover Color', 'tolips-themer'),
                'type'      => Controls_Manager::COLOR,
                'default'   => '',
                'selectors' => [
                    '{{WRAPPER}} .team-block.team-v2 .team-image .socials-team a:hover' => 'color: {{VALUE}};',
                ],
            ]
        );

        $this->end_controls_section();

    }

    public static function get_query_args( $settings ) {
        $defaults = [
            'category' => '',
            'orderby' => 'date',
            'order' => 'desc',
            'posts_per_page' => 3,
            'offset' => 0,
        ];

        $settings = wp_parse_args( $settings, $defaults );
        $ids = $settings['post_ids'];

        $query_args = [
            'post_type' => 'gva_team',
            'posts_per_page' => $settings['posts_per_page'],
            'orderby' => $settings['orderby'],
            'order' => $settings['order'],
            'ignore_sticky_posts' => 1,
            'post_status' => 'publish', 
        ];

        if( is_array($ids) && count($ids) > 0 ){
            $query_args['post__in'] = $ids;
            $query_args['orderby'] = 'post__in';
        }

        if(is_front_page()){
            $query_args['paged'] = (get_query_var('page')) ? get_query_var('page') : 1;
        }else{
            $query_args['paged'] = (get_query_var('paged')) ? get_query_var('paged') : 1;
        }
 
        return $query_args;
    }


    public function query_posts() {
        $query_args = $this->get_query_args( $this->get_settings() );
        return new WP_Query( $query_args );
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
            include $this->get_template('teams/' . $settings['layout'] . '.php');
        }
        print '</div>'; 
    }

}

$widgets_manager->register(new GVAElement_Teams());
