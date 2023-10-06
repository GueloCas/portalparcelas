<?php
Redux::setSection( $opt_name, array(
   'icon' => 'el-icon-shopping-cart',
   'title' => esc_html__('Product Options', 'tolips'),
   'fields' => array(
      array(
        'id'        => 'products_per_page',
        'type'      => 'text',
        'title'     => esc_html__('Products Per Page', 'tolips'),
        'subtitle'  => esc_html__('Number value.', 'tolips'),
        'desc'      => esc_html__('The amount of products you would like to show per page on shop/category pages.', 'tolips'),
        'validate'  => 'numeric',
        'default'   => '24'
      )       
   )
));

Redux::setSection( $opt_name, array(
   'icon'         => 'el-icon-shopping-cart',
   'title'        => esc_html__('Shop Options', 'tolips'),
   'subsection'   => true,
   'fields'       => array(
      array(
         'id'        => 'product_display_layout',
         'type'      => 'select',
         'title'     => esc_html__('Default Product Display Layout', 'tolips'),
         'subtitle'  => esc_html__('Choose the default product display layout for WooCommerce shop/category pages.', 'tolips'),
         'options'   => array(
            'grid'      => esc_html__('Grid', 'tolips'),
            'list'      => esc_html__('List', 'tolips')
        ),
        'default'    => 'standard'
      ),
      array(
         'id'     => 'product_columns_lg',
         'type'   => 'select',
         'title'  => esc_html__('Display Columns for Large Screen', 'tolips'),
         'subtitle' => esc_html__('Choose the number of columns to display on shop/category pages.', 'tolips'),
         'options'   => array(
            '1'      => '1',
            '2'      => '2',
            '3'      => '3',
            '4'      => '4',
            '5'      => '5',
            '6'      => '6',
         ),
         'default'   => '3'
      ),

      array(
         'id' => 'product_columns_md',
         'type' => 'select',
         'title' => esc_html__('Display Columns for Medium Screen', 'tolips'),
         'subtitle' => esc_html__('Choose the number of columns to display on shop/category pages.', 'tolips'),
         'options' => array(
            '1'      => '1',
            '2'      => '2',
            '3'      => '3',
            '4'      => '4',
            '5'      => '5',
            '6'      => '6',
         ),
         'default' => '3'
      ),

      array(
         'id'        => 'product_columns_sm',
         'type'      => 'select',
         'title'     => esc_html__('Display Columns for Small Screen', 'tolips'),
         'subtitle'  => esc_html__('Choose the number of columns to display on shop/category pages.', 'tolips'),
         'options'   => array(
            '1'      => '1',
            '2'      => '2',
            '3'      => '3',
            '4'      => '4',
            '5'      => '5',
            '6'      => '6',
         ),
         'default' => '2'
      ),

      array(
         'id' => 'product_columns_xs',
         'type' => 'select',
         'title' => esc_html__('Display Columns for Extra Small Screen', 'tolips'),
         'subtitle' => esc_html__('Choose the number of columns to display on shop/category pages.', 'tolips'),
         'options' => array(
            '1'      => '1',
            '2'      => '2',
            '3'      => '3',
            '4'      => '4',
            '5'      => '5',
            '6'      => '6',
         ),
         'default' => '1'
      ),

      array(
         'id' => 'woo_sidebar_config',
         'type' => 'select',
         'title' => esc_html__('WooCommerce Sidebar Config', 'tolips'),
         'subtitle' => esc_html__('Choose the sidebar config for WooCommerce shop/category pages.', 'tolips'),
         'options' => array(
           'no-sidebars'     => esc_html__('No Sidebars', 'tolips'),
           'left-sidebar'    => esc_html__('Left Sidebar', 'tolips'),
           'right-sidebar'   => esc_html__('Right Sidebar', 'tolips')
         ),
         'default' => 'no-sidebars'
      ),
      array(
         'id' => 'woo_left_sidebar',
         'type' => 'select',
         'title' => esc_html__('WooCommerce Left Sidebar', 'tolips'),
         'subtitle' => esc_html__('Choose the left sidebar for WooCommerce shop/category pages.', 'tolips'),
         'data'      => 'sidebars',
         'default' => 'woocommerce_sidebar'
      ),
      array(
         'id' => 'woo_right_sidebar',
         'type' => 'select',
         'title' => esc_html__('WooCommerce Right Sidebar', 'tolips'),
         'subtitle' => esc_html__('Choose the right sidebar for WooCommerce shop/category pages', 'tolips'),
         'data'      => 'sidebars',
         'default' => 'woocommerce_sidebar'
      ),
      array(
         'id' => 'woo_shop_divide_0',
         'type' => 'divide'
      ),
      array(
         'id'        => 'woo_breadcrumb_show_title',
         'type'      => 'button_set',
         'title'     => esc_html__('Breadcrumb Display Title Page', 'tolips'),
         'options'   => array(
            1 => esc_html__('Enable', 'tolips'),
            0 => esc_html__('Disable', 'tolips')
         ),
         'default' => 1
      ),
      array(
         'id'        => 'woo_breadcrumb_padding_top',
         'type'      => 'slider',
         'title'     => esc_html__( 'Breadcrumb Padding Top', 'tolips' ),
         'default'   => 110,
         'min'       => 50,
         'max'       => 500,
         'step'      => 1,
         'display_value' => 'text',
      ),
      array(
         'id'        => 'woo_breadcrumb_padding_bottom',
         'type'      => 'slider',
         'title'     => esc_html__( 'Breadcrumb Padding Top', 'tolips' ),
         'default'   => 100,
         'min'       => 50,
         'max'       => 500,
         'step'      => 1,
         'display_value' => 'text',
      ),
      array(
         'id'        => 'woo_breadcrumb_background_color',
         'type'      => 'color',
         'title'     => esc_html__('Background Overlay Color', 'tolips'),
         'default'   => ''
      ),
      array(
         'id'        => 'woo_breadcrumb_background_opacity',
         'type'      => 'slider',
         'title'     => esc_html__( 'Breadcrumb Ovelay Color Opacity', 'tolips' ),
         'default'   => 50,
         'min'       => 0,
         'max'       => 100,
         'step'      => 2,
         'display_value' => 'text',
      ),
      array(
         'id'        => 'woo_breadcrumb_image',
         'type'      => 'button_set',
         'title'     => esc_html__('Breadcrumb Image', 'tolips'),
         'options'   => array( 
            1 => esc_html__('Enable', 'tolips'),
            0 => esc_html__('Disable', 'tolips')
         ),
         'default' => 'enable'
      ),
      array(
         'id'        => 'woo_breadcrumb_background_image',
         'type'      => 'media',
         'url'       => true,
         'title'     => esc_html__('Breadcrumb Background Image', 'tolips'),
         'default'   => '',
         'required'  => array( 'woo_breadcrumb_image', '=', 1 )
      ),
      array(
         'id'        => 'woo_breadcrumb_text_stype',
         'type'      => 'select',
         'title'     => esc_html__( 'Breadcrumb Text Stype', 'tolips' ),
         'options'   => 
         array(
            'text-light'     => esc_html__('Light', 'tolips'),
            'text-dark'      => esc_html__('Dark', 'tolips')
         ),
         'default' => 'text-light'
      ),
      array(
         'id'      => 'woo_breadcrumb_text_align',
         'type'    => 'select',
         'title'   => esc_html__( 'Breadcrumb Text Align', 'tolips' ),
         'options' => 
         array(
            'text-left'      => esc_html__('Left', 'tolips'),
            'text-center'    => esc_html__('Center', 'tolips'),
            'text-right'     => esc_html__('Right', 'tolips')
         ),
         'default' => 'text-left'
      )
   )
));

Redux::setSection( $opt_name, array(
   'icon'         => 'el-icon-shopping-cart',
   'title'        => esc_html__('Project/Product Options', 'tolips'),
   'subsection'   => true,
   'fields' => array(
      array(
         'id'        => 'upsell_heading_text',
         'type'      => 'text',
         'title'     => esc_html__('Upsell Heading Text', 'tolips'),
         'default'   => esc_html__('Complete the look', 'tolips')
      ),
      array(
         'id'        => 'related_heading_text',
         'type'      => 'text',
         'title'     => esc_html__('Related Heading Text', 'tolips'),
         'default'   => esc_html__('Similar Projects', 'tolips'),
      ),
      array(
         'id'        => 'related_subheading_text',
         'type'      => 'text',
         'title'     => esc_html__('Related Sub Heading Text', 'tolips'),
         'default'   => esc_html__('Businesses You Can Back', 'tolips'),
      )
   )
));