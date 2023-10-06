<?php
namespace uListing\Classes\Builder;

use uListing\Classes\StmListingTemplate;
use uListing\Classes\StmListingType;
use uListing\Classes\StmListingSingleLayout;
use uListing\Classes\StmListingAttribute;

class UListingBuilder {
	// @codingStandardsIgnoreStart
	public static function generation_css($file_name, $content, $post_id = false){
		$upload = wp_get_upload_dir();

		if (!file_exists($upload['basedir'].'/ulisting')) {
			mkdir($upload['basedir'].'/ulisting', 0777, true);
		}

		if (!file_exists($upload['basedir'].'/ulisting/css')) {
			mkdir($upload['basedir'].'/ulisting/css', 0777, true);
		}

		if ($post_id && !file_exists($upload['basedir'].'/ulisting/css/'.$post_id)) {
			mkdir($upload['basedir'].'/ulisting/css/'.$post_id, 0777, true);
		}

		$file = fopen($upload['basedir']."/ulisting/css/".$post_id.'/'.$file_name.".css", "w");

		fwrite($file, $content);
	}

	/**
	 * @return array Config field for section
	 */
	public static function get_section_field(){
		return [
			"style" => [
				"name" => "Style",
				"fields" => [
					[
						"type"   => "color",
						"label"  => "Background color",
						"name"   => "background_color",
					]
				]
			],
			"advanced" => [
				"name" => "Advanced",
				"fields" => [
					[
						"type"   => "checkbox",
						"label"  => "Full width",
						"name"   => "full_width",
					],
					[
						"type"   => "text",
						"label"  => "ID",
						"name"   => "id",
					],
					[
						"type"   => "text",
						"label"  => "Class",
						"name"   => "class",
					],
					[
						"type"  => "margin",
						"label" => "Margin",
						"name"  => "margin",
					],
					[
						"type"  => "padding",
						"label" => "Padding",
						"name"  => "padding",
					]
				]
			]
		];
	}

	/**
	 * @return array Config field for row
	 */
	public static function get_row_field(){
		return [
			"column" => [
				"name" => "Column",
				"fields" => [
					[
						"type"   => "column",
						"label"  => "Column",
						"name"   => "column",
					]
				]
			],
			"advanced" => [
				"name" => "Advanced",
				"fields" => [
					[
						"type"   => "text",
						"label"  => "ID",
						"name"   => "id",
					],
					[
						"type"   => "text",
						"label"  => "Class",
						"name"   => "class",
					]
				]
			]
		];
	}

	/**
	 * @return array Config field for column
	 */
	public static function get_col_field(){
		return [
			"style" => [
				"name" => "Style",
				"fields" => [
					[
						"type"   => "color",
						"label"  => "Background color",
						"name"   => "background_color",
					],

				]
			],
			"advanced" => [
				"name" => "Advanced",
				"fields" => [
					[
						"type"   => "text",
						"label"  => "ID",
						"name"   => "id",
					],
					[
						"type"   => "text",
						"label"  => "Class",


						"name"   => "class",
					],
					[
						"type"   => "column-size",
						"label"  => "Size of columns",
						"items"   => [
							0 => "None",
							1 => 1,
							2 => 2,
							3 => 3,
							4 => 4,
							5 => 5,
							6 => 6,
							7 => 7,
							8 => 8,
							9 => 9,
							10 => 10,
							11 => 11,
							12 => 12,
						],
						"name"   => "size",
					],
					[
						"type"  => "margin",
						"label" => "Margin",
						"name"  => "margin",
					],
					[
						"type"  => "padding",
						"label" => "Padding",
						"name"  => "padding",
					],
					[
						"type"   => "responsive-select",
						"label"  => "Flex",
						"name"   => "flex",
						"items"   => [
							"none" => "None",
							"center" => "Flex to center",
							"flex-start" => "Flex start",
							"flex-end" => "Flex end",
							"space-around" => "Space around",
							"space-between" => "Space between",
							"space-evenly" => "Space evenly"
						],
					],
					[
						"type"   => "responsive-select",
						"label"  => "Flex direction",
						"name"   => "flex_direction",
						"items"   => [
							"none" => "None",
							"row" => "Row",
							"column" => "Column",
						]
					],
					[
						"type"   => "responsive-select",
						"label"  => "Align items",
						"name"   => "align_items",
						"items"   => [
							"none" => "None",
							"stretch" => "Stretch",
							"center" => "Flex to center",
							"flex-start" => "Flex start",
							"flex-end" => "Flex end",
							"baseline" => "Baseline",
						]
					],
                    [
                        "type"  => "responsive-position",
                        "label" => "Position",
                        "name"  => "position",
                        "items" => [
                            "static"    => "Static",
                            "fixed"    => "Fixed",
                            "absolute" => "Absolute",
                            "relative" => "Relative",
                            "sticky" => "Sticky",
                        ]
                    ],
				]
			]
		];
	}

	/**
	 * @return array Config field for tabs
	 */
	public static function get_tabs_field(){
		return [
			"items" => [
				"name" => "Items",
				"fields" => [
					[
						"type"   => "items",
						"label"  => "Tabs Items",
						"name"   => "items",
					]
				]
			],
			"advanced" => [
				"name" => "Advanced",
				"fields" => [
					[
						"type"   => "text",
						"label"  => "ID",
						"name"   => "id",
					],
					[
						"type"   => "text",
						"label"  => "Class",
						"name"   => "class",
					],
					[
						"type"  => "margin",
						"label" => "Margin",
						"name"  => "margin",
					],
					[
						"type"  => "padding",
						"label" => "Padding",
						"name"  => "padding",
					],
				]
			]
		];
	}

	/**
	 * @return array
	 */
	public static function get_accordion_field(){
		return [
			"advanced" => [
				"name" => "Advanced",
				"fields" => [
					[
						"type"   => "text",
						"label"  => "ID",
						"name"   => "id",
					],
					[
						"type"   => "text",
						"label"  => "Class",
						"name"   => "class",
					],
					[
						"type"  => "margin",
						"label" => "Margin",
						"name"  => "margin",
					],
					[
						"type"  => "padding",
						"label" => "Padding",
						"name"  => "padding",
					],
				]
			]
		];
	}

	/**
	 * @return array
	 */
	public static function get_tabs_box(){
		return [
			"id" => rand(100, 999)."_".time(),
			"title"        => "Tabs",
			"type"         => "basic",
			"group"        => "basic",
			"module"       => "tabs",
            "icon"         => "icon-1665599",
			"field_group"  => "tabs",
			"params"       => [
				'type' => 'tabs',
				'items' => "",
				'id' => "",
				'class' => "",
				"items"     => [
					[
						"title" => "Tab Title",
						"elements" => []
					]
				],
			]
		];
	}

	/**
	 * @return array object for donor
	 */
	public static function get_donor(){
		return [
			"section" => [
				"id" => 0,
				"title" => "Section",
				"rows" => [],
				"field_group" => "section",
				"params" => [
					"id" => "",
					"class" => "",
					"background_color" => "",
				],
			],
			"row" => [
				"id" => 0,
				"title" => "Row",
				"columns" => [],
				"field_group" => "row",
				"params" => [
					"id" => "",
					"class" => "",
					'column' => 1
				]
			],
			"col" => [
				"id" => 0,
				"title" => "Column",
				"number" => 0,
				"elements" => [],
				"field_group" => "col",
				"params" => [
					"id" => "",
					"class" => "",
					"size" => [
						"extra_large" =>0,
						"large" =>0,
						"medium" =>0,
						"small" =>0,
						"extra_small" =>0
					]
				]
			]
		];
	}

	/**
	 * @return array
	 */
	public static function get_inner_row(){
		return [
			"id" => rand(100, 999)."_".time(),
			"title" => "Columns",
			"type" => "basic",
			"group" => "basic",
            "icon"  => "icon-19947601",
			"module" => "columns",
			"columns" => [self::get_inner_column()],
			"field_group" => "row",
			"params" => [
				"type" => "column",
				"id" => "",
				"class" => "",
				'column' => 1
			]
		];
	}

	/**
	 * @return array
	 */
	public static function get_inner_column(){
		return [
			"id" => rand(1000, 9999)."_".time(),
			"title" => "Column",
			"field_group"  => "col",
			"elements" => [],
			"params" => [
				"id" => "",
				"class" => "",
				"background_color" => "",
				"size" => [
					"extra_large" =>0,
					"large" =>0,
					"medium" =>0,
					"small" =>0,
					"extra_small" =>0
				]
			]
		];
	}

	/**
	 * @param $data
	 *
	 * @return string
	 */
	public static function generation_html_attribute($data) {
		if(!isset($data['params']['class']))
			$data['params']['class'] = "";
		$data['params']['class'] .= " ulisting_element_".$data['id'];

        $data = apply_filters('ulisting_html_attributes', $data);

		$attribute = "";
		if(isset($data['params']['id']) AND !empty($data['params']['id']))
			$attribute .= 'id="'.$data['params']['id'].'"';
		if(isset($data['params']['data-id']) && !empty($data['params']['data-id']))
		    $attribute .= 'data-id="'. $data['params']['data-id']  .'"';
		if(isset($data['params']['class']) AND !empty($data['params']['class']))
			$attribute .= 'class="'.$data['params']['class'].'"';

		return $attribute;
	}


    /**
     * @return array
     */
	public static function get_similar_listings()
    {
        return [
            "id" => rand(100, 999)."_".time(),
            "title"           => "Similar Listings",
            "type"            => "element",
            "icon"            => "icon--184",
            "group"           => "basic",
            "module"          => "similar-listings",
            "field_group"     => "similar-listings",
            "elements_top"    => [],
            "elements_center" => [],
            "elements_bottom" => [],
            "params"          => [
                'type'  => 'similar-listings',
                'id'    => "",
                "class" => "ulisting-similar-listings",
            ]
        ];
    }

    /**
     * @return array
     */
    public static function get_featured_listing()
    {
        return [
            "id"           => rand(100, 999)."_".time(),
            "title"        => "Featured Listing",
            "type"         => "element",
            "icon"         => "icon-860808",
            "group"        => "basic",
            "module"       => "element",
            "field_group"  => "featured-listing",
            "params"       => [
                'id'    => "",
                'class' => "",
                "type"  => "featured-listing",
            ]
        ];
    }

    /**
     * @return array
     */
    public static function get_featured_listings_group(){
        return [
            "advanced" => [
                "name" => "Featured Listing",
                "fields" => [
                    [
                        "type"   => "text",
                        "label"  => "ID",
                        "name"   => "id",
                    ],
                    [
                        "type"   => "text",
                        "label"  => "Class",
                        "name"   => "class",
                    ],
                    [
                        "type"  => "margin",
                        "label" => "Margin",
                        "name"  => "margin",
                    ],
                    [
                        "type"  => "padding",
                        "label" => "Padding",
                        "name"  => "padding",
                    ]
                ]
            ]
        ];
    }


    /**
     * @return array
     */
    public static function get_similar_listings_group(){
        return [
            "advanced" => [
                "name" => "Advanced",
                "fields" => [
                    [
                        "type"   => "text",
                        "label"  => "ID",
                        "name"   => "id",
                    ],
                    [
                        "type"   => "text",
                        "label"  => "Class",
                        "name"   => "class",
                    ],
                    [
                        "type"  => "margin",
                        "label" => "Margin",
                        "name"  => "margin",
                    ],
                    [
                        "type"  => "padding",
                        "label" => "Padding",
                        "name"  => "padding",
                    ]
                ]
            ],
            "similar_listings" => [
                "name" => "Settings"
            ]
        ];
    }


	/**
	 * @return array
	 */
	public static function get_attribute_box(){
		return [
			"id" => rand(100, 999)."_".time(),
			"title"        => "Attribute Box",
			"type"         => "basic",
			"group"        => "basic",
			"module"       => "attribute-box",
            "icon"         => "icon--21",
			"field_group" => "attribute-box",
			"elements"     => [],
			"params"       => [
				"id" => "",
                "type" => "attribute-box",
                "class" => "ulisting-attribute-box",
                'column' => 1,
                'attribute_style' => 0,
                "style_template" => "ulisting_style_1"
			]
		];
	}

	/**
	 * @return array
	 */
	public static function get_attribute_box_field_group(){
		return [
			"style" => [
				"name" => "Style",
				"fields" => [
					[
						"type"   => "color",
						"label"  => "Background color",
						"name"   => "background_color",
					]
				]
			],
			"template" => [
				"name" => "Template",
				"fields" => [
					[
						"type"   => "size-column",
						"label"  => "Column",
						"name"   => "column",
					],
					[
						"type"   => "blog",
						"label"  => "Style template",
						"name"   => "style_template",
						"items"  => StmListingAttribute::get_style_templates()
					]
				]
			],
			"advanced" => [
				"name" => "Advanced",
				"fields" => [
					[
						"type"   => "text",
						"label"  => "ID",
						"name"   => "id",
					],
					[
						"type"   => "text",
						"label"  => "Class",
						"name"   => "class",
					],
					[
						"type"  => "margin",
						"label" => "Margin",
						"name"  => "margin",
					],
					[
						"type"  => "padding",
						"label" => "Padding",
						"name"  => "padding",
					]
				]
			]
		];
	}
	/**
	 * @return array
	 */
	public static function get_html_box(){
		return [
			"id" => rand(100, 999)."_".time(),
			"title"        => "Html box",
			"type"         => "element",
            "icon"         => "icon--2",
			"group"        => "basic",
			"module"       => "element",
			"field_group"  => "html-box",
			"elements"     => [],
			"params"       => [
				'html' => "",
				'id' => "",
				'class' => "",
				"type" => "html",
				"title" => "",
			]
		];
	}



	/**
	 * @return array
	 */
	public static function get_html_box_field_group(){
		return [
			"html" => [
				"name" => "Html",
				"fields" => [
					[
						"type"   => "text",
						"label"  => "Title",
						"name"   => "title",
					],
					[
						"type"   => "textarea",
						"label"  => "Html code",
						"name"   => "html",
					],
				]
			],
			"style" => [
				"name" => "Style",
				"fields" => [
					[
						"type"   => "color",
						"label"  => "Background color",
						"name"   => "background_color",
					],
                   [
                       "type"   => "color",
                       "label"  => "Text color",
                       "name"   => "color",
                   ]
				]
			],
			"advanced" => [
				"name" => "Advanced",
				"fields" => [
					[
						"type"   => "text",
						"label"  => "ID",
						"name"   => "id",
					],
					[
						"type"   => "text",
						"label"  => "Class",
						"name"   => "class",
					],
					[
						"type"  => "margin",
						"label" => "Margin",
						"name"  => "margin",
					],
					[
						"type"  => "padding",
						"label" => "Padding",
						"name"  => "padding",
					]
				]
			]
		];
	}

	/**
	 * @return array
	 */
	public static function get_short_code(){
		return [
			"id" => rand(100, 999)."_".time(),
			"title"        => "SHort code",
			"type"         => "element",
			"group"        => "basic",
            "icon"         => "icon-711284",
			"module"       => "element",
			"field_group"  => "short-code",
			"elements"     => [],
			"params"       => [
				'short_code' => "",
				"type" => "short-code",
			]
		];
	}

	/**
	 * @return array
	 */
	public static function get_short_code_field_group(){
		return [
			"short_code" => [
				"name" => "Code",
				"fields" => [
					[
						"type"   => "text",
						"label"  => "Short code",
						"name"   => "short_code",
					],
				]
			],
			"style" => [
				"name" => "Style",
				"fields" => [
					[
						"type"   => "color",
						"label"  => "Background color",
						"name"   => "background_color",
					]
				]
			],
			"advanced" => [
				"name" => "Advanced",
				"fields" => [
					[
						"type"   => "text",
						"label"  => "ID",
						"name"   => "id",
					],
					[
						"type"   => "text",
						"label"  => "Class",
						"name"   => "class",
					],
					[
						"type"  => "margin",
						"label" => "Margin",
						"name"  => "margin",
					],
					[
						"type"  => "padding",
						"label" => "Padding",
						"name"  => "padding",
					]
				]
			]
		];
	}

	public static function get_quickview(){
		return [
			"id" => rand(100, 999)."_".time(),
			"title"        => "Quick view",
			"type"         => "element",
			"group"        => "basic",
            "icon"         => "icon-1666578",
			"module"       => "element",
			"field_group" => "quickview",
			"elements"     => [],
			"params"       => [
				"id" => "",
				"type" => "quickview",
				"class" => "",
				"style_template" => "ulisting_style_1"
			]
		];
	}

	public static function get_quickview_field_group(){
		return [
			"template" => [
				"name" => "Template",
				"fields" => [
					[
						"type"   => "blog",
						"label"  => "Style template",
						"name"   => "style_template",
						"items"  => StmListingAttribute::get_quickview_style_templates()
					]
				]
			],
			"advanced" => [
				"name" => "Advanced",
				"fields" => [
					[
						"type"   => "text",
						"label"  => "ID",
						"name"   => "id",
					],
					[
						"type"   => "text",
						"label"  => "Class",
						"name"   => "class",
					],

                    [
                        "type"   => "color",
                        "label"  => "Background color",
                        "name"   => "background_color",
                    ],

					[
						"type"  => "margin",
						"label" => "Margin",
						"name"  => "margin",
					],
					[
						"type"  => "padding",
						"label" => "Padding",
						"name"  => "padding",
					]
				]
			],
            "quick_view" => [
                "name" => "Settings",
            ]
		];
	}

	/**
	 * @param $args
	 *
	 * @return bool|string
	 */
	public static function render($sections, $css_file_name, $args) {
		$content = "";
		$upload = wp_get_upload_dir();

		$listing_type = ( ! empty( $args['listingType'] ) ? $args['listingType'] : ! empty( $args['listingType'] ) ) ? $args['listingType'] : null;
		if ( ! is_null( $listing_type ) && !file_exists($upload['basedir'].'/ulisting/css/'.$listing_type->ID.'/'.$css_file_name.".css")) {
			$style_url = $upload['baseurl']."/ulisting/css/".$css_file_name.".css";
		} else {
			$style_url = $upload['baseurl'].'/ulisting/css/'.$args['listing_type']->ID.'/'.$css_file_name.".css";
		}
		wp_enqueue_style('ulisting_builder_stytle_'.$css_file_name, $style_url);

		if(is_array($sections) AND !empty($sections)){
			foreach ($sections as $section)
				$content .= StmListingTemplate::load_template('builder/section',['section' => $section, 'args' => $args]);
		}
		return $content;
	}

	/**
	 * @param $data
	 *
	 * @return string
	 */
	public static function generation_style($data) {
		$style = "";
		foreach ($data as $key => $val) {
			if(isset($val['id']) AND $val['params'])
				$style .= self::build_style($val['id'], $val['params']);

			if(isset($val['rows']))
				$style .= self::generation_style($val['rows']);

			if(isset($val['columns']))
				$style .= self::generation_style($val['columns']);

			if(isset($val['elements']))
				$style .= self::generation_style($val['elements']);

			if(isset($val['elements_top']))
				$style .= self::generation_style($val['elements_top']);

			if(isset($val['elements_bottom']))
				$style .= self::generation_style($val['elements_bottom']);

			if(isset($val['module']) AND $val['module'] == "tabs" AND isset($val['params']['items'])){
				foreach ($val['params']['items'] as $tab_item){
					if(isset($tab_item['elements']))
						$style .= self::generation_style($tab_item['elements']);
				}
			}
		}
		return $style;
	}

	/**
	 * @param $id
	 * @param $params
	 *
	 * @return string
	 */
	public static function build_style($id, $params) {
		$style = [
			"extra_large" => [],
			"large" => [],
			"medium" => [],
			"small" => [],
			"extra_small" => [],
		];

		$class_name = "ulisting_element_".$id;
		$style = [
			'main' => [],
			'extra_large' => [],
			'large' => [],
			'medium' => [],
			'small' => [],
			'extra_small' => []
		];
		foreach ($params as $key => $val){
			if(empty($val))
				continue;
			// main
			switch ($key){
				case "background_color":
					$style['main'][] = 'background-color:'.$val.'';
					break;
				case "color":
					$style['main'][] = 'color:'.$val.'';
					break;
				case "font_size":
					if(is_array($val)){
						foreach ($val as $device => $size) {
							if($size != "")
								$style[$device][] = 'font-size: '.$size.'px';
						}
					}else{
						$style['main'][] = 'font-size: '.$val.'px';
					}
					break;
				case "margin":
					foreach ($val as $device => $margin_val) {
						foreach ($margin_val as $position => $v){
							if($v != "")
								$style[$device][] = "margin-".$position.":".$v."px";
						}
					}
					break;
				case "padding":
					foreach ($val as $device => $padding_val) {
						foreach ($padding_val as $position => $v){
							if($v != "")
								$style[$device][] = "padding-".$position.":".$v."px";
						}
					}
					break;
				case "height":
					$val = (is_array($val)) ? $val : [];
					foreach ($val as $device => $v) {
						$css = "";
						if(!empty($v))
							$style[$device][] = "height:".$v."px";
					}
					break;
                case "position":
                    $val = (is_array($val)) ? $val : [];
                    foreach ($val as $device => $position_val) {
                        if($position_val == 'sticky') {
                            $style[$device][] = "position: ".$position_val . "; position: -webkit-sticky; align-self: flex-start;";
                        } else {
                            $style[$device][] = "position: ".$position_val;
                        }
                    }
                    break;
                case "values":
                    foreach ($val as $device => $_position) {
                        foreach ($_position as $position => $v){
                            if($v != "")
                                $style[$device][] = "".$position.":".$v."px";
                        }
                    }
                    break;
				case "flex":
					foreach ($val as $device => $v) {
						$css =  "display: block;";
						if($v != "none")
							$css = "display: flex; justify-content: ".$v.";";
						$style[$device][] = $css;
					}
					break;
				case "flex_direction":
					foreach ($val as $device => $v) {
						$css = "";
						if($v != "none")
							$css = "flex-direction: ".$v.";";
						$style[$device][] = $css;
					}
					break;
				case "align_items":
					foreach ($val as $device => $v) {
						$css = "";
						if($v != "none")
							$css = "align-items: ".$v.";";
						$style[$device][] = $css;
					}
					break;
			}
		}

		$style = " 
		.".$class_name."{".implode(";",$style['main']).';'.implode(";",$style['extra_large']).";}
		@media (max-width: 1199px) { .".$class_name."{".implode(";",$style['large'])."} }
		@media (max-width: 991px) { .".$class_name."{".implode(";",$style['medium'])."} }
		@media (max-width: 767px) { .".$class_name."{".implode(";",$style['small'])."} }
		@media (max-width: 575px) { .".$class_name."{".implode(";",$style['extra_small'])."} }
		";
		return $style;
	}
	// @codingStandardsIgnoreEnd
}
