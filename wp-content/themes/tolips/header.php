<?php
/**
 * $Desc
 *
 * @author     Gaviasthemes Team     
 * @copyright  Copyright (C) 2021 gaviasthemes. All Rights Reserved.
 * @license    GNU/GPL v2 or later http://www.gnu.org/licenses/gpl-2.0.html
 * 
 */ 
$header = apply_filters('tolips_get_header_layout', null );
if(!empty($header) && class_exists('Gavias_Themer_Header')){
   $header_builder = get_page_by_path($header, OBJECT, 'gva_header');
   if($header_builder){
      get_template_part('header', 'builder');
   }else{
      get_template_part('header', 'default');
   }
}else{
  get_template_part('header', 'default');
}