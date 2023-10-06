<?php
   Redux::setSection( $opt_name, array(
      'title'     => esc_html__('General Options', 'tolips'),
      'icon'      => 'el-icon-wrench',
      'fields'    => array(
         array(
            'id'        => 'skin_color',
            'type'      => 'select',
            'title'     => esc_html__('Skin Color', 'tolips'),
            'options'   => array(
               ''              => 'Default',
               'blue'          => esc_html__('Blue', 'tolips'),
               'brown'         => esc_html__('Brown', 'tolips'),
               'green'         => esc_html__('Green', 'tolips'),
               'lilac'         => esc_html__('Lilac', 'tolips'),
               'orange'        => esc_html__('Orange', 'tolips'),
               'pink'          => esc_html__('Pink', 'tolips'),
               'purple'        => esc_html__('Purple', 'tolips'),
               'red'           => esc_html__('Red', 'tolips'),
               'turquoise'     => esc_html__('Turquoise', 'tolips'),
               'yellow'        => esc_html__('Yellow', 'tolips')
            ),
            'default' => ''
         ),
         array(
            'id'           => 'color_theme',
            'type'         => 'color',
            'title'        => esc_html__( 'Custom Color Theme', 'tolips' ),
            'desc'         => esc_html__( 'Used custom color instead of Skin Colors Available.', 'tolips' ),
            'default'      => '',
            'transparent'  => false,
            'validate'     => 'color'
         ),
         array(
            'id'           => 'color_theme_second',
            'type'         => 'color',
            'title'        => esc_html__( 'Custom Color Theme Second', 'tolips' ),
            'desc'         => esc_html__( 'Used custom color instead of Skin Colors Available.', 'tolips' ),
            'default'      => '',
            'transparent'  => false,
            'validate'     => 'color'
         ),
         array(
            'id'           => 'page_layout',
            'type'         => 'button_set',
            'title'        => esc_html__('Page Layout', 'tolips'),
            'subtitle'     => esc_html__('Select the page layout type', 'tolips'),
            'options'      => array('boxed' => 'Boxed','fullwidth' => 'Fullwidth'),
            'default'      => 'fullwidth'
         ),
         array(
            'id'           => 'enable_backtotop',
            'type'         => 'button_set',
            'title'        => esc_html__('Enable Back To Top', 'tolips'),
            'subtitle'     => esc_html__('Enable the back to top button that appears in the bottom right corner of the screen.', 'tolips'),
            'options'      => array('1' => 'On','0' => 'Off'),
            'default'      => '1'
         ),
      )
   ));