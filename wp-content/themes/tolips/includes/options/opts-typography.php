<?php
Redux::setSection( $opt_name, array(
   'title'     => esc_html__( 'Typography', 'tolips' ),
   'icon'      => 'el-icon-font',
   'fields'    => array(
      array (
         'id'     => 'main_font_info',
         'type'   => 'info',
         'icon'   => true,
         'raw'    => '<h3 style="margin: 0;">' . esc_html__( 'Main Font', 'tolips' ) . '</h3>',
      ),
      array(
         'id'        => 'main_font_source',
         'type'      => 'radio',
         'title'     => esc_html__( 'Font Source', 'tolips' ),
         'options'   => array(
            '0'   => '(none)',
            '1'   => 'Standard + Google Webfonts', 
         ),
         'default'   => '1'
      ),
      // Main font: Standard + Google Webfonts
      array (
         'id'           => 'main_font',
         'type'         => 'typography',
         'title'        => esc_html__( 'Font Face', 'tolips' ),
         'line-height'  => false,
         'text-align'   => false,
         'font-style'   => false,
         'font-weight'  => false,
         'font-size'    => false,
         'color'        => false,
         'default'      => array (
            'font-family'  => 'Open Sans',
            'subsets'      => '',
         ),
         'required'     => array( 'main_font_source', '=', '1' )
      ),
   
      // Secondary font
      array (
         'id'     => 'secondary_font_info',
         'icon'   => true,
         'type'   => 'info',
         'raw'    => '<h3 style="margin: 0;">' . esc_html__( 'Secondary Font', 'tolips' ) . '</h3>',
      ),
      array(
         'id'        => 'secondary_font_source',
         'type'      => 'radio',
         'title'     => esc_html__('Font Source', 'tolips'),
         'options'   => array(
            '0' => '(none)',
            '1' => 'Standard + Google Webfonts', 
         ),
         'default'   => '0'
      ),
      // Secondary font: Standard + Google Webfonts
      array (
         'id'           => 'secondary_font',
         'type'         => 'typography',
         'title'        => esc_html__( 'Font Face', 'tolips' ),
         'line-height'  => false,
         'text-align'   => false,
         'font-style'   => false,
         'font-weight'  => false,
         'font-size'    => false,
         'color'        => false,
         'default'      => array (
            'font-family'  => 'Open Sans',
            'subsets'      => '',
         ),
         'required'     => array( 'secondary_font_source', '=', '1' )
      )
   )
));