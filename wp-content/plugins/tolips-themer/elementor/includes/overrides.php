<?php
use Elementor\Controls_Manager;
use Elementor\Repeater;
class GVA_Elementor_Override{
   public function __construct() {
      $this->add_actions();
      $this->elementor_init_setup();
   }

   function elementor_init_setup(){
      if(!get_option('elementor_allow_svg', '')) update_option( 'elementor_allow_svg', 1 );
      if(!get_option('elementor_load_fa4_shim', '')) update_option( 'elementor_load_fa4_shim', 'yes' );
      if(!get_option('elementor_disable_color_schemes', '')) update_option( 'elementor_disable_color_schemes', 'yes' );
      if(!get_option('elementor_disable_typography_schemes', '')) update_option( 'elementor_disable_typography_schemes', 'yes' );
      if(!get_option('elementor_container_width', '')) update_option( 'elementor_container_width', '1200' );
      $cpt_support = get_option( 'elementor_cpt_support' );
      if( empty($cpt_support) ){
         $cpt_support[] = 'page';
         $cpt_support[] = 'footer';
         $cpt_support[] = 'gva_header';
         update_option('elementor_cpt_support', $cpt_support);
      }else{
         if( !in_array('footer', $cpt_support) || !in_array('gva_header', $cpt_support) ){
            $cpt_support[] = 'footer';
            $cpt_support[] = 'gva_header';
            update_option('elementor_cpt_support', $cpt_support);
         }
      }
   }

   public function add_actions() {
      add_action( 'elementor/element/column/layout/after_section_end', [ $this, 'column_style' ], 10, 2 );
      add_action( 'elementor/element/section/section_structure/after_section_end', [ $this, 'row_style' ], 10, 2 );
      add_action( 'elementor/element/section/section_layout/after_section_end', [ $this, 'after_row_end' ], 10, 2 );

   }

   public function column_style( $obj, $args ) {
      $obj->start_controls_section(
         'gva_column_style',
         array(
            'label' => esc_html__( 'Gavias Style Settings', 'tolips-themer' ),
            'tab'   => Controls_Manager::TAB_STYLE,
         )
      );

      $obj->add_control(
         '_gva_extra_classes',
         [
            'label' => __( 'Style Available', 'gaviasthemer' ),
            'type' => Controls_Manager::SELECT,
            'options' => [
               '' => __( '-- None --', 'gaviasthemer' ),
               'bg-overflow-left' => __( 'Background Overflow Left', 'gaviasthemer' ),
               'bg-overflow-right' => __( 'Background Overflow Right', 'gaviasthemer' ),
            ],
            'default' => 'top',
            'prefix_class' => 'column-style-',
         ]
      );
 
      $obj->end_controls_section();    

   }

   public function after_row_end( $obj, $args ) {
      
      $obj->start_controls_section(
         'gva_section_row',
         array(
            'label' => esc_html__( 'Gavias Extra Settings Row for Header Builder', 'tolips-themer' ),
            'tab'   => Controls_Manager::TAB_LAYOUT,
         )
      );

      // Header Sticky
      $obj->add_control(
         'row_header_sticky',
         [
            'label'  => esc_html__( 'Sticky Row Settings (Use only for row in header)', 'tolips-themer' ),
            'type'      => Controls_Manager::HEADING
         ]
      );

      $obj->add_control(
         '_gva_sticky_menu',
         [
            'label'     => __( 'Sticky Menu Row', 'tolips-themer' ),
            'type'      => Controls_Manager::SELECT,
            'options'   => [
               '' => __( '-- None --', 'tolips-themer' ),
               'gv-sticky-menu' => __( 'Sticky Menu', 'tolips-themer' ),
            ],
            'default'         => '',
            'prefix_class'    => '',
            'description'     => __('You can only enable sticky menu for one row, please make sure display all sticky menu for other rows')
         ]
      );

      $obj->add_control(
         '_gva_sticky_background',
         [
            'label'     => __('Sticky Background Color', 'tolips-themer'),
            'type'      => Controls_Manager::COLOR,
            'selectors' => [ 
               '.gv-sticky-wrapper.is-fixed > .elementor-section' => 'background: {{VALUE}}!important;', 
            ],
            'condition' => [
               '_gva_sticky_menu!' => ''
            ]
         ]
      );
      $obj->add_control(
         '_gva_sticky_menu_text_color',
         [
            'label'     => __('Sticky Text Color', 'tolips-themer'),
            'type'      => Controls_Manager::COLOR,
            'selectors' => [
               '.gv-sticky-wrapper.is-fixed > .elementor-section' => 'color: {{VALUE}}', 
            ],
            'condition' => [
               '_gva_sticky_menu!' => ''
            ]
         ]
      );
      $obj->add_control(
         '_gva_sticky_menu_link_color',
         [
            'label'     => __('Sticky Link Menu Color', 'tolips-themer'),
            'type'      => Controls_Manager::COLOR,
            'selectors' => [
               '.gv-sticky-wrapper.is-fixed > .elementor-section .gva-navigation-menu ul.gva-nav-menu > li > a' => 'color: {{VALUE}}',
            ],
            'condition' => [
               '_gva_sticky_menu!' => ''
            ]
         ]
      );
      $obj->add_control(
         '_gva_sticky_menu_link_hover_color',
         [
            'label'     => __('Sticky Link Menu Hover Color', 'tolips-themer'),
            'type'      => Controls_Manager::COLOR,
            'selectors' => [
               '.gv-sticky-wrapper.is-fixed > .elementor-section .gva-navigation-menu ul.gva-nav-menu > li > a:hover' => 'color: {{VALUE}}',
            ],
            'condition' => [
               '_gva_sticky_menu!' => ''
            ]
         ]
      );
      $obj->end_controls_section();
   }

   public function row_style($obj, $args){

      // Settings for row
      $obj->start_controls_section(
         'gva_extra_settings_row',
         array(
            'label' => esc_html__( 'Gavias Style Settings', 'tolips-themer' ),
            'tab'   => Controls_Manager::TAB_STYLE,
         )
      );
      $obj->add_control(
         '_gva_extra_row_style',
         [
            'label'     => __( 'Style Available', 'gaviasthemer' ),
            'type'      => Controls_Manager::SELECT,
            'options'   => [
               '' => __( '-- None --', 'gaviasthemer' ),
               'style-1' => __( 'Background White Full To Left and Border Theme', 'gaviasthemer' ),
               'style-2' => __( 'Background Theme Full To Right', 'gaviasthemer' ),
            ],
            'label_block'  => true,
            'default'      => 'top',
            'prefix_class' => 'row-',
         ]
      );
      $obj->add_control(
         'gva_row_color',
         [
            'label' => __( 'Background Color', 'gaviasthemer' ),
            'type' => Controls_Manager::SELECT,
            'label_block'  => true,
            'options' => [
               '' => __( '-- Default --', 'gaviasthemer' ),
               'theme'         => __( 'Background Color Theme', 'gaviasthemer' ),
               'theme-second'  => __( 'Background Color Theme Second', 'gaviasthemer' ),
            ],
            'default' => '',
            'prefix_class' => 'bg-row-',
         ]
      );
      $obj->add_control(
         'gva_bg_row_effect',
         [
            'label' => __( 'Background Effect', 'gaviasthemer' ),
            'type' => Controls_Manager::SELECT,
            'label_block'  => true,
            'options' => [
               '' => __( '-- Default --', 'gaviasthemer' ),
               'particles-js'  => __( 'Particles', 'gaviasthemer' )
            ],
            'default' => '',
            'prefix_class' => 'row-background-',
         ]
      );


      $obj->end_controls_section();
   }

}

new GVA_Elementor_Override();

