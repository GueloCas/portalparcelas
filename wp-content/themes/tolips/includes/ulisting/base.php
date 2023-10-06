<?php

function tolips_ulising_advanced_element_attributes(){
   $attrs = [
      "name" => esc_html__("Advanced", 'tolips'),
      "fields" => [
         [
            "type"  => "text",
            "label" => esc_html__("ID", 'tolips'),
            "name"  => "id",
         ],
         [
            "type"  => "text",
            "label" => esc_html__("Class", 'tolips'),
            "name"  => "class",
         ],
         [
            "type"  => "margin",
            "label" => esc_html__("Margin", 'tolips'),
            "name"  => "margin",
         ],
         [
            "type"  => "padding",
            "label" => esc_html__("Padding",'tolips'),
            "name"  => "padding",
         ]
      ]
   ];
   return $attrs;
}

function tolips_ulisting_style_color_element_attributes(){
   $attrs = [
      "name"   => esc_html__("Style", 'tolips'),
      "fields" => [
         [
            "type"  => "color",
            "label" => esc_html__("Background color", 'tolips'),
            "name"  => "background_color",
         ],
         [
            "type"  => "color",
            "label" => esc_html__("Text color", 'tolips'),
            "name"  => "color",
         ]
      ]
   ];
   return $attrs;
}

function tolips_ulisting_title_element_attributes(){
   $attrs = [
      "name" => esc_html__("Title", 'tolips'),
      "fields" => [
         [
            "type"   => "text",
            "label"  => esc_html__("Title", 'tolips'),
            "name"   => "title",
         ]
      ]
   ];
   return $attrs;
}


function tolips_ulisting_params_element_attributes($template_path, $attrs){
   $attrs['template_path'] = $template_path;
   $attrs['type'] = 'element';
   $attrs['id'] = '';
   $attrs['class'] = '';
   return $attrs;
}