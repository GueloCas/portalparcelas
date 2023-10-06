<?php

add_filter("ulisting_attribute_style_templates", "tolips_ulisting_attribute_style_templates");
add_filter("ulisting_input_block_style_templates", 'tolips_attribute_template');
add_filter("ulisting_extra_block_style_templates", 'tolips_attribute_template');

function tolips_ulisting_attribute_style_templates ($styles) {
	$styles["ulisting_style_1"] = [
		"icon"               => GAVIAS_TOLIPS_PLUGIN_URL . "/assets/images/ulisting/attribute/style-1.jpg",
		"name"               => esc_html__("Style I", 'tolips'),
		"attribute_template" => "
			<div class='ulisting-attribute-template type-1 attribute_[attribute_type]'>
				<span class='ulisting-attribute-template-icon'>[attribute_icon]</span>
				<span class='ulisting-attribute-template-value'>[attribute_value] <span class='sub-title'>[sub_title]</span></span>
		  </div> ",
	];

	$styles["ulisting_style_2"] = [
		"icon"               => GAVIAS_TOLIPS_PLUGIN_URL . "/assets/images/ulisting/attribute/style-2.jpg",
		"name"               => esc_html__("Style II", 'tolips'),
		"attribute_template" => "
			<div class='ulisting-attribute-template type-2 attribute_[attribute_type]'>
				<span class='ulisting-attribute-template-name'>[attribute_name]:</span>
				<span class='ulisting-attribute-template-value'>[attribute_value]</span>
				<span class='ulisting-attribute-template-sub-title hidden'>[sub_title]</span>
		  </div> ",
	];

	$styles["ulisting_style_3"] = [
		"icon"               => GAVIAS_TOLIPS_PLUGIN_URL . "/assets/images/ulisting/attribute/style-3.jpg",
		"name"               => esc_html__("Style III", 'tolips'),
		"attribute_template" => "
			<div class='ulisting-attribute-template type-3 attribute_[attribute_type]'>
				<span class='attribute-icon'>[attribute_icon]</span>
				<span class='attribute-right'>
					<span class='attribute-value'>[attribute_value]</span>
					<span class='attribute-affix'>[sub_title]</span>
				</span>   
			</div> ",
	];

	$styles["ulisting_style_4"] = [
		"icon"               => GAVIAS_TOLIPS_PLUGIN_URL . "/assets/images/ulisting/attribute/style-4.jpg",
		"name"               => esc_html__("List Attribute Option", 'tolips'),
		"attribute_template" => "
			<div class='ulisting-attribute-template type-4 attribute_[attribute_type]'>
				<h4 class='ulisting-el-heading'>[attribute_name]</h4>  
				<ul class='attributes-list'>[option_items]</ul> 
			</div> ",
		"option_template"  => "
			<li>
				<span class='ulisting-attribute-template-icon'>[attribute_option_icon]</span>
				<span class='ulisting-attribute-template-value'>[attribute_value]</span>
			</li>",
	];

	return $styles;
}

function tolips_attribute_template($styles) {
   $styles["ulisting_style_1"] = [
		"icon"               => GAVIAS_TOLIPS_PLUGIN_URL . "/assets/images/ulisting/attribute/style-1.jpg",
		"name"               => esc_html__("Style I", 'tolips'),
		"attribute_template" => "
			<div class='ulisting-attribute-template type-1 attribute_[attribute_type]'>
				<span class='ulisting-attribute-template-icon'>[attribute_icon]</span>
				<span class='ulisting-attribute-template-value'>[attribute_value] [sub_title]</span>
		  </div> ",
	];

	$styles["ulisting_style_2"] = [
		"icon"               => GAVIAS_TOLIPS_PLUGIN_URL . "/assets/images/ulisting/attribute/style-2.jpg",
		"name"               => esc_html__("Style II", 'tolips'),
		"attribute_template" => "
			<div class='ulisting-attribute-template type-2 attribute_[attribute_type]'>
				<span class='ulisting-attribute-template-name'>[attribute_name]:</span>
				<span class='ulisting-attribute-template-value'>[attribute_value]</span>
				<span class='ulisting-attribute-template-sub-title hidden'>[sub_title]</span>
		  </div> ",
	];

	$styles["ulisting_style_3"] = [
		"icon"               => GAVIAS_TOLIPS_PLUGIN_URL . "/assets/images/ulisting/attribute/style-3.jpg",
		"name"               => esc_html__("Style III", 'tolips'),
		"attribute_template" => "
			<div class='ulisting-attribute-template type-3 attribute_[attribute_type]'>
				<span class='attribute-icon'>[attribute_icon]</span>
				<span class='attribute-right'>
					<span class='attribute-value'>[attribute_value]</span>
					<span class='attribute-affix'>[sub_title]</span>
				</span>   
			</div> ",
	];

   return $styles;
}
