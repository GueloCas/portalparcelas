<?php
add_filter('ulisting_item_layout_data', 'tolips_ulisting_preview_grid');
add_filter('ulisting_item_layout_data', 'tolips_ulisting_preview_list');
add_filter('ulisting_item_layout_data', 'tolips_ulisting_preview_map');

function tolips_ulisting_preview_grid($data){
   $data['elements'][] = [
      'id'          => rand(10, 10000) . time(),
      'title'       => esc_html__('Item Preview Grid', 'tolips'),
      'type'        => 'element',
      'group'       => 'basic',
      'module'      => 'element',
      'field_group' => 'tolips_preview_grid',
      'params'      => [
         'template_path'  => 'loop/preview',
         'type'           => 'element',
         'style_template' => 'item-grid-1'
      ],
   ];

   $data['config']['tolips_preview_grid'] = [
      'field_group' => [
         'template' => [
            'name'   => esc_html__('Template', 'tolips'),
            'fields' => [
               [
                  'type'  => 'select',
                  'label' => esc_html__('Style Template', 'tolips'),
                  'name'  => 'style_template',
                  'items' => [
                     'item-grid-1' => esc_html__('Style 1', 'tolips'),
                     'item-grid-2' => esc_html__('Style 2', 'tolips')
                  ]
               ]
            ]
         ],
      ]
   ];
   return $data;
}

function tolips_ulisting_preview_list($data){
   $data['elements'][] = [
      'id'          => rand(10, 10000) . time(),
      'title'       => esc_html__('Item Preview List', 'tolips'),
      'type'        => 'element',
      'group'       => 'basic',
      'module'      => 'element',
      'field_group' => 'tolips_preview_list',
      'params'      => [
         'template_path'  => 'loop/preview',
         'type'           => 'element',
         'style_template' => 'item-list-1'
      ],
   ];

   $data['config']['tolips_preview_list'] = [
      'field_group' => [
         'template' => [
            'name'   => esc_html__('Template', 'tolips'),
            'fields' => [
               [
                  'type'  => 'select',
                  'label' => esc_html__('Style Template', 'tolips'),
                  'name'  => 'style_template',
                  'items' => [
                     'item-list-1' => esc_html__('Style 1', 'tolips')
                  ]
               ]
            ]
         ],
      ]
   ];
   
   return $data;
}

function tolips_ulisting_preview_map($data){
   $data['elements'][] = [
      'id'          => rand(10, 10000) . time(),
      'title'       => esc_html__('Item Preview Map', 'tolips'),
      'type'        => 'element',
      'group'       => 'basic',
      'module'      => 'element',
      'field_group' => 'tolips_preview_map',
      'params'      => [
         'template_path'  => 'loop/preview',
         'type'           => 'element',
         'style_template' => 'item-map-1'
      ],
   ];

   $data['config']['tolips_preview_map'] = [
      'field_group' => [
         'template' => [
            'name'   => esc_html__('Template', 'tolips'),
            'fields' => [
               [
                  'type'  => 'select',
                  'label' => esc_html__('Style Template', 'tolips'),
                  'name'  => 'style_template',
                  'items' => [
                     'item-map-1' => esc_html__('Style 1', 'tolips'),
                  ]
               ]
            ]
         ],
      ]
   ];
   
   return $data;
} 
