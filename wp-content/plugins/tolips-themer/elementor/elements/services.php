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
class GVAElement_Services extends GVAElement_Base{

    public function get_name() {
        return 'gva-services';
    }

    public function get_title() {
        return __('GVA Services', 'tolips-themer');
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
        return 'eicon-posts-grid';
    }

    public function get_keywords() {
        return [ 'service', 'content', 'carousel', 'grid' ];
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

    private function get_categories_list(){
        $categories = array();

        $categories['none'] = __( 'None', 'tolips-themer' );
        $taxonomy = 'category_service';
        $tax_terms = get_terms( $taxonomy );
        if ( ! empty( $tax_terms ) && ! is_wp_error( $tax_terms ) ){
            foreach( $tax_terms as $item ) {
                $categories[$item->term_id] = $item->name;
            }
        }
        return $categories;
    }

    private function get_posts() {
        $posts = array();

        $loop = new \WP_Query( array(
            'post_type' => array('post'),
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
            'section_query',
            [
                'label' => __('Query & Layout', 'tolips-themer'),
                'tab'   => Controls_Manager::TAB_CONTENT,
            ]
        );

        $this->add_control(
            'category_ids',
            [
                'label' => __( 'Select By Category', 'tolips-themer' ),
                'type' => Controls_Manager::SELECT2,
                'multiple'    => true,
                'default' => '',
                'options'   => $this->get_categories_list()
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
                ],
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
                'default' => 'service-style-1',
                'options' => [
                    'service-style-1'         => __( 'Item Service Style I', 'tolips-themer' ),
                ],
                'condition' => [
                    'layout' => array('grid', 'carousel')
                ]
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

        
        // Styling Posts Grid & Carousel
        $this->start_controls_section(
            'section_styling_post_content',
            [
                'label' => __( 'Post Content', 'tolips-themer' ),
                'tab'   => Controls_Manager::TAB_STYLE,
                'condition' => [
                    'layout' => ['grid', 'carousel']
                ],
            ]
        );

        $this->add_responsive_control(
            'post_box_padding',
            [
                'label' => __( 'Padding Post Content', 'tolips-themer' ),
                'type' => Controls_Manager::DIMENSIONS,
                'size_units' => [ 'px', '%' ],
                'selectors' => [
                    '{{WRAPPER}} .gva-posts .entry-content' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
            ]
        );

        $this->add_control(
            'post_box_background',
            [
                'label' => __( 'Background Post Content', 'tolips-themer' ),
                'type' => Controls_Manager::COLOR,
                'default' => '',
                'selectors' => [
                    '{{WRAPPER}} .gva-posts .entry-content' => 'background-color: {{VALUE}};',
                ]
            ]
        );

        $this->end_controls_section();

        // Styling post title
        $this->start_controls_section(
            'section_styling_post_title',
            [
                'label' => __( 'Post Title', 'tolips-themer' ),
                'tab'   => Controls_Manager::TAB_STYLE,
                'condition' => [
                    'layout' => ['grid', 'carousel']
                ],
            ]
        );

        $this->add_control(
            'post_box_title_color',
            [
                'label' => __( 'Color Title', 'tolips-themer' ),
                'type' => Controls_Manager::COLOR,
                'default' => '',
                'selectors' => [
                    '{{WRAPPER}} .gva-posts .entry-content .entry-title a' => 'color: {{VALUE}};',
                ],
            ]
        );

        $this->add_control(
            'post_box_title_color_hover',
            [
                'label' => __( 'Color Title Hover', 'tolips-themer' ),
                'type' => Controls_Manager::COLOR,
                'default' => '',
                'selectors' => [
                    '{{WRAPPER}} .gva-posts .entry-content .entry-title a:hover' => 'color: {{VALUE}};',
                ],
            ]
        );

        $this->add_group_control(
            Group_Control_Typography::get_type(),
            [
                'name' => 'post_box_title_typography',
                'selector' => '{{WRAPPER}} .gva-posts .entry-content .entry-title a',
            ]
        );

        $this->add_control(
            'post_box_meta_color',
            [
                'label' => __( 'List Posts Meta Color', 'tolips-themer' ),
                'type' => Controls_Manager::COLOR,
                'default' => '',
                'selectors' => [
                    '{{WRAPPER}} .gva-posts .post .entry-meta, {{WRAPPER}} .gva-posts .post .entry-meta a' => 'color: {{VALUE}};',
                ],
            ]
        );

        $this->end_controls_section();

        // Styling post description
        $this->start_controls_section(
            'section_styling_post_description',
            [
                'label' => __( 'Post Description', 'tolips-themer' ),
                'tab'   => Controls_Manager::TAB_STYLE,
                'condition' => [
                    'layout' => ['grid', 'carousel']
                ],
            ]
        );

        $this->add_control(
            'post_box_description_color',
            [
                'label' => __( 'Color', 'tolips-themer' ),
                'type' => Controls_Manager::COLOR,
                'default' => '',
                'selectors' => [
                    '{{WRAPPER}} .gva-posts .entry-content .entry-description' => 'color: {{VALUE}};',
                ],
            ]
        );

        $this->add_group_control(
            Group_Control_Typography::get_type(),
            [
                'name' => 'post_box_description_typography',
                'selector' => '{{WRAPPER}} .gva-posts .entry-content .entry-description',
            ]
        );

        $this->end_controls_section();

    }

    public static function get_query_args(  $settings ) {
        $defaults = [
            'post_ids' => '',
            'category_ids' => '',
            'orderby' => 'date',
            'order' => 'desc',
            'posts_per_page' => 3,
            'offset' => 0,
        ];

        $settings = wp_parse_args( $settings, $defaults );
        $cats = $settings['category_ids'];
        $ids = $settings['post_ids'];

        $query_args = [
            'post_type' => 'service',
            'posts_per_page' => $settings['posts_per_page'],
            'orderby' => $settings['orderby'],
            'order' => $settings['order'],
            'ignore_sticky_posts' => 1,
            'post_status' => 'publish', // Hide drafts/private posts for admins
        ];

        if($cats){
            if( is_array($cats) && count($cats) > 0 ){
                $field_name = is_numeric($cats[0]) ? 'term_id':'slug';
                $query_args['tax_query'] = array(
                    array(
                      'taxonomy' => 'category_service',
                      'terms' => $cats,
                      'field' => $field_name,
                      'include_children' => false
                    )
                );
            }
        }

        if( $ids ){
          $ids = explode(',', $ids);
          if( is_array($ids) && count($ids) > 0 ){
            $query_args['post__in'] = $ids;
            $query_args['orderby'] = 'post__in';
          }
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


    protected function render() {
        $settings = $this->get_settings_for_display();
        printf( '<div class="gva-element-%s gva-element">', $this->get_name() );
        if( !empty($settings['layout']) ){
            include $this->get_template('services/' . $settings['layout'] . '.php');
        }
        print '</div>'; 

    }


}

      $widgets_manager->register(new GVAElement_Services());
