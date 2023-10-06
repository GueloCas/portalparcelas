<?php
Redux::setSection( $opt_name, array(
  'title'   => esc_html__('Footer Options', 'tolips'),
  'icon'    => 'el-icon-compass',
  'fields'  => array(
    array(
      'id'      => 'footer_layout',
      'type'    => 'select',
      'options' => tolips_get_footer(),
      'default' => 'footer-1'
    ),
    array(
      'id'      => 'copyright_default',
      'type'    => 'button_set',
      'title'   => esc_html__('Enable/Disable Copyright Text', 'tolips'),
      'options' => array(
        'yes' => esc_html__('Enable', 'tolips'),
        'no'  => esc_html__('Disable', 'tolips')
      ),
      'default' => 'yes'
    ),
    array(
      'id'      => 'copyright_text',
      'type'    => 'editor',
      'title'   => esc_html__('Footer Copyright Text', 'tolips'),
      'default' => esc_html__('Copyright - 2021 - Company - All rights reserved. Powered by WordPress.', 'tolips')
    ),
  )
));