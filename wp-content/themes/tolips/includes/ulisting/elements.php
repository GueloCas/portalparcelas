<?php

add_filter('ulisting_single_layout_builder_data', 'tolip_ulisting_builder_address_element');
add_filter('ulisting_single_layout_builder_data', 'tolip_ulisting_builder_agent_element');
add_filter('ulisting_single_layout_builder_data', 'tolip_ulisting_builder_meta_element');
add_filter('ulisting_single_layout_builder_data', 'tolip_ulisting_builder_video_element');
add_filter('ulisting_single_layout_builder_data', 'tolip_ulisting_builder_heading_element');
add_filter('ulisting_single_layout_builder_data', 'tolip_ulisting_builder_divider_element');
add_filter('ulisting_single_layout_builder_data', 'tolip_ulisting_builder_mortgage_calc_element');
add_filter('ulisting_single_layout_builder_data', 'tolip_ulisting_builder_comment_element');


add_filter("ulisting_attribute_gallery_style_templates", 'tolip_ulisting_builder_gallery_element_style');

function tolip_ulisting_builder_address_element($data){
   $data["elements"][]  = [
      "id"           => rand(10, 10000) . time(),
      "title"       => esc_html__("Address", 'tolips'),
      "type"        => "element",
      "group"       => "general",
      "module"      => "element",
      "field_group" => "tolips_address_fields",
      "params"      => tolips_ulisting_params_element_attributes('builder/attribute/address', array()) 
   ];
   
   $data["config"]["tolips_address_fields"] = [
      "field_group" => [
         "advanced" => tolips_ulising_advanced_element_attributes()
      ]
   ];
   return $data;
}

function tolip_ulisting_builder_agent_element($data){
   $data["elements"][]  = [
      "id"           => rand(10, 10000) . time(),
      "title"       => esc_html__("Agent", 'tolips'),
      "type"        => "element",
      "group"       => "basic",
      "module"      => "element",
      "field_group" => "tolips_agent_fields",
      "params"      => tolips_ulisting_params_element_attributes('builder/element/agent', array(
         "title"            => esc_html__("Agent Listing", 'tolips'),
      )) 
   ];
   
   $data["config"]["tolips_agent_fields"] = [
      "field_group" => [
         "title"     => tolips_ulisting_title_element_attributes(),
         "advanced"  => tolips_ulising_advanced_element_attributes()
      ]
   ];
   return $data;
}

function tolip_ulisting_builder_meta_element($data){
   $data["elements"][]  = [
      "id"           => rand(10, 10000) . time(),
      "title"       => esc_html__("Meta Listing", 'tolips'),
      "type"        => "element",
      "group"       => "basic",
      "module"      => "element",
      "field_group" => "tolips_meta_fields",
      "params"      => tolips_ulisting_params_element_attributes('builder/element/meta', array()) 
   ];
   
   $data["config"]["tolips_meta_fields"] = [
      "field_group" => [
         "advanced"  => tolips_ulising_advanced_element_attributes()
      ]
   ];
   return $data;
}

function tolip_ulisting_builder_video_element($data){
   $data["elements"][]  = [
      "id"           => rand(10, 10000) . time(),
      "title"       => esc_html__("Video Embed", 'tolips'),
      "type"        => "element",
      "group"       => "general",
      "module"      => "element",
      "field_group" => "tolips_video_fields",
      "params"      => tolips_ulisting_params_element_attributes('builder/attribute/video_embed', array(
         "title"            => esc_html__("Property Video", 'tolips'),
      )) 
   ];
   
   $data["config"]["tolips_video_fields"] = [
      "field_group" => [
         "title"     => tolips_ulisting_title_element_attributes(),
         "advanced"  => tolips_ulising_advanced_element_attributes()
      ]
   ];
   return $data;
}

function tolip_ulisting_builder_heading_element($data){
   $data["elements"][]  = [
      "id"           => rand(10, 10000) . time(),
      "title"       => esc_html__("Heading", 'tolips'),
      "type"        => "element",
      "group"       => "basic",
      "module"      => "element",
      "field_group" => "tolips_heading_fields",
      "params"      => tolips_ulisting_params_element_attributes('builder/element/heading', array(
         "title"            => esc_html__("Heading Title", 'tolips'),
      )) 
   ];
   
   $data["config"]["tolips_heading_fields"] = [
      "field_group" => [
         "title"     => tolips_ulisting_title_element_attributes(),
         "advanced"  => tolips_ulising_advanced_element_attributes()
      ]
   ];
   return $data;
}

function tolip_ulisting_builder_divider_element($data){
   $data["elements"][]  = [
      "id"           => rand(10, 10000) . time(),
      "title"       => esc_html__("Divider", 'tolips'),
      "type"        => "element",
      "group"       => "basic",
      "module"      => "element",
      "field_group" => "tolips_divider_fields",
      "params"      => tolips_ulisting_params_element_attributes('builder/element/divider', array()) 
   ];
   
   $data["config"]["tolips_divider_fields"] = [
      "field_group" => [
         "style"     => tolips_ulisting_style_color_element_attributes(),
         "advanced"  => tolips_ulising_advanced_element_attributes()
      ]
   ];
   return $data;
}

function tolip_ulisting_builder_mortgage_calc_element($data){
   $data["elements"][]  = [
      "id"           => rand(10, 10000) . time(),
      "title"       => esc_html__("Mortgage Calc", 'tolips'),
      "type"        => "element",
      "group"       => "basic",
      "module"      => "element",
      "field_group" => "tolips_mortgage_calc_fields",
      "params"      => tolips_ulisting_params_element_attributes('builder/element/mortgage_calc', array()) 
   ];
   
   $data["config"]["tolips_mortgage_calc_fields"] = [
      "field_group" => [
         "advanced"  => tolips_ulising_advanced_element_attributes()
      ]
   ];
   return $data;
}

function tolip_ulisting_builder_comment_element($data){
   $data["elements"][]  = [
      "id"           => rand(10, 10000) . time(),
      "title"       => esc_html__("Comment", 'tolips'),
      "type"        => "element",
      "group"       => "basic",
      "module"      => "element",
      "field_group" => "tolips_comment_fields",
      "params"      => tolips_ulisting_params_element_attributes('builder/element/comment', array()) 
   ];
   
   $data["config"]["tolips_comment_fields"] = [
      "field_group" => [
         "advanced"  => tolips_ulising_advanced_element_attributes()
      ]
   ];
   return $data;
}

function tolip_ulisting_builder_gallery_element_style($data){
   $data["gallery_style_1"] = [
      "name" => esc_html__("Style I", 'tolips'),
   ];
   return $data;
}

