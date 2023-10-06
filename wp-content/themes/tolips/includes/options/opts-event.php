<?php
Redux::setSection( $opt_name, array(
   'title'   => esc_html__( 'Event Options', 'tolips' ),
   'icon'    => 'el-icon-website',
   'fields'  => array(
      array(
        'id'    => 'event_single_post_info',
        'type'  => 'info',
        'icon'  => true,
        'raw'   => '<h3 style="margin: 0;">' . esc_html__( 'Single Event', 'tolips' ) . '</h3>',
      ),
      array(
        'id'        => 'single_event_sidebar',
        'type'      => 'select',
        'title'     => esc_html__('Default Single Sidebar Config', 'tolips'),
        'subtitle'  => esc_html__('Choose single Event layout.', 'tolips'),
        'options'   => array(
          'no-sidebars'     => esc_html__('No Sidebars', 'tolips'),
          'left-sidebar'    => esc_html__('Left Sidebar', 'tolips'),
          'right-sidebar'   => esc_html__('Right Sidebar', 'tolips')
        ),
        'default' => 'no-sidebars'
      ),
      array(
        'id'        => 'single_event_left_sidebar',
        'type'      => 'select',
        'title'     => esc_html__('Default Single Left Sidebar', 'tolips'),
        'subtitle'  => esc_html__('Choose the default left sidebar for Single Event.', 'tolips'),
        'data'      => 'sidebars',
        'default'   => 'event_sidebar'
      ),
       array(
        'id'        => 'single_event_right_sidebar',
        'type'      => 'select',
        'title'     => esc_html__('Default Single Right Sidebar', 'tolips'),
        'subtitle'  => esc_html__('Choose the default right sidebar for Single Event.', 'tolips'),
        'data'      => 'sidebars',
        'default'   => 'event_sidebar'
      )
   )
));