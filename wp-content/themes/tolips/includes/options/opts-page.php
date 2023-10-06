<?php
Redux::setSection($opt_name, array(
   'icon'      => 'el-icon-th-list',
   'title'     => esc_html__('Page Options', 'tolips'),
   'fields'    => array(
      array(
         'id'        => 'default_show_page_heading',
         'type'      => 'button_set',
         'title'     => esc_html__('Default Show Page Heading', 'tolips'),
         'subtitle'  => esc_html__('Choose the default state for the page heading, shown/hidden.', 'tolips'),
         'options'   => array('1' => 'On','0' => 'Off'),
         'default'   => '1'
      ),
      array(
         'id'        => 'default_sidebar_config',
         'type'      => 'select',
         'title'     => esc_html__('Default Page Sidebar Config', 'tolips'),
         'subtitle'  => esc_html__('Choose the default sidebar config for pages', 'tolips'),
         'options'   => array(
            'no-sidebars'     => esc_html__('No Sidebars', 'tolips'),
            'left-sidebar'    => esc_html__('Left Sidebar', 'tolips'),
            'right-sidebar'   => esc_html__('Right Sidebar', 'tolips')
         ),
         'default'   => 'no-sidebars'
      ),
      array(
         'id'           => 'default_left_sidebar',
         'type'         => 'select',
         'title'        => esc_html__('Default Page Left Sidebar', 'tolips'),
         'subtitle'     => esc_html__('Choose the default left sidebar for pages', 'tolips'),
         'data'         => 'sidebars',
         'default'      => 'sidebar-1'
      ),
      array(
         'id'           => 'default_right_sidebar',
         'type'         => 'select',
         'title'        => esc_html__('Default Page Right Sidebar', 'tolips'),
         'subtitle'     => esc_html__('Choose the default right sidebar for pages', 'tolips'),
         'data'         => 'sidebars',
         'default'      => 'sidebar-1'
      ),
   )
));  