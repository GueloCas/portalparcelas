<?php
add_filter('ulisting_inventory_filter_template', 'tolips_ulisting_filter_template');

function tolips_ulisting_filter_template($data){
   $data = [];
   $data['template_1'] =  [
      "icon" => '',
      "name" => esc_html__("Filter Sidebar", 'tolips'),
      "template" => "<div class='filter-sidebar-wrapper filter-sidebar-style'>[filter_panel]</div>",
      "template_inner" => "[filter]",
      "template_field" => "[field]",
   ];
   $data['template_2'] =  [
      "icon" => '',
      "name" => esc_html__("Filter Top Horizontal", 'tolips'),
      "template" => "<div class='filter-horizontal-wrapper filter-horizontal-style'>[filter_panel]</div>",
      "template_inner" => "[filter]",
      "template_field" => "[field]",

   ];
   return $data;
}
