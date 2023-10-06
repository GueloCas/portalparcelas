<?php

namespace uListing\Classes;

use uListing\Classes\Builder\UListingBuilder;

class StmListingItemCardLayout
{

    /**
     * Save layout for listing type page
     */
    public static function save_layout()
    {
        $result = [
            'success' => false
        ];
        $request_body = file_get_contents('php://input');
        $data = json_decode($request_body, true);
        if (isset($data['listing_type_id']) AND ($listing_type = StmListingType::find_one($data['listing_type_id']))) {
            $layout = [
                "config" => isset($data['layout']['config']) ? $data['layout']['config'] : [],
                "sections" => isset($data['sections']) ? $data['sections'] : [],
            ];
            update_post_meta($listing_type->ID, "stm_listing_item_card_" . $data['layout']['id'], apply_filters('uListing-sanitize-data', $layout));
            $style = UListingBuilder::generation_style($data['sections']);
            UListingBuilder::generation_css("ulisting_item_card_" . $listing_type->ID . "_" . $data['layout']['id'], $style);
            $listing_type->save_builder_element("ulisting_listing_type_item_card_element_data_" . $data['layout']['id'], $data['sections']);
            $result['success'] = true;
        }

        return $result;
    }

    /**
     * @return array
     */
    public static function getLayoutList()
    {
        global $wpdb;

        $layouts = [];
        $result = $wpdb->get_results(
            "
		    SELECT * 
		    FROM {$wpdb->prefix}options  as options
		    WHERE options.`option_name` like '%ulisting_type_page_layout_%'
	    ",
            ARRAY_N
        );

        foreach ($result as $layout) {
            $value = json_decode($layout[2], true);
            $layouts[] = [
                "id" => $layout[1],
                "name" => $value['name'],
                "section" => $value['section']
            ];
        }

        return $layouts;
    }

    /**
     * @return array
     */
    public static function get_layout_list()
    {
        global $wpdb;
        $result = [
            'success' => false
        ];
        foreach (self::getLayoutList() as $layout) {
            $result['layouts'][] = [
                "id" => $layout['id'],
                "name" => $layout['name']
            ];
        }
        $result['success'] = true;
        return $result;
    }

    public static function get_layout_by_id() {
        $result = [
            'message' => __('Access denied', 'ulisting'),
            'success' => false,
            'status'  => 'error',
            'data'    => []
        ];

        StmVerifyNonce::verifyNonce(sanitize_text_field($_POST['nonce']), 'ulisting-ajax-nonce');

        $post_id = isset($_POST['listing_type_id']) ? sanitize_text_field($_POST['listing_type_id']) : null;
        $key     = isset($_POST['layout_id']) ? sanitize_text_field($_POST['layout_id']) : null;

        if ($listing_type = StmListingType::find_one($post_id)) {
            $layout             = get_post_meta($listing_type->ID, "stm_listing_item_card_" . $key);
            $result['data']     = isset($layout[0]) ? maybe_unserialize($layout[0]) : [];
            $result['success']  = true;
            $result['message']  = __('Layout got successfully', 'ulisting');
            $result['status']   = 'success';
        }

        wp_send_json($result);
    }

    public static function save_layout_by_id() {
        $result = [
            'message' => __('Access denied', 'ulisting'),
            'success' => false,
            'status'  => 'error',
            'data'    => [],
        ];

        $request_body = file_get_contents('php://input');
        $request_data = json_decode($request_body, true);

        StmVerifyNonce::verifyNonce(sanitize_text_field($request_data['nonce']), 'ulisting-ajax-nonce');

        $post_id   = isset($request_data['listing_type_id']) ? (int)sanitize_text_field($request_data['listing_type_id']) : null;
        $layout    = isset($request_data['layout']) ? apply_filters('uListing-sanitize-data', $request_data['layout']) : null;
        $section   = isset($request_data['sections'])  ? apply_filters('uListing-sanitize-data', $request_data['sections']) : [];

        if ( isset($layout['id']) && !empty($post_id) and $listing_type = StmListingType::find_one($post_id) ){
            $layout_data = [
                "config"   => isset($layout['config']) ? $layout['config'] : [],
                "sections" => $section,
            ];

            update_post_meta($listing_type->ID, "stm_listing_item_card_" . $layout['id'], apply_filters('uListing-sanitize-data', $layout_data));
            $style = UListingBuilder::generation_style($section);
            UListingBuilder::generation_css("ulisting_item_card_" . $listing_type->ID . "_" . $layout['id'], $style);
            $listing_type->save_builder_element("ulisting_listing_type_item_card_element_data_" . $layout['id'], $section);

            $result['success'] = true;
            $result['message'] = __('Layout saved successfully', 'ulisting');
            $result['status']  = 'success';
        }

        wp_send_json($result);

    }

    /**
     * @param $id
     *
     * @return array|mixed|null|object
     */
    public static function getLayout($listing_type_id, $layout_id)
    {
        if ($listing_type = StmListingType::find_one($listing_type_id) AND $layout = get_post_meta($listing_type->ID, "stm_listing_item_card_" . $layout_id))
            return maybe_unserialize($layout[0]);
        return null;
    }

    /**
     * @return array
     */
    public static function get_layout()
    {
        $result = [
            'success' => false
        ];
        $request_body = file_get_contents('php://input');
        $data = json_decode($request_body, true);
        $config = self::get_item_layout();

        if (isset($data['listing_type_id']) AND isset($data['layout_id']) AND $layout = self::getLayout($data['listing_type_id'], $data['layout_id'])) {
            $result['layout'] = array_merge($config[$data['layout_id']], $layout);
            $result['success'] = true;
        } else if (isset($config[$data['layout_id']])) {
            $result['layout'] = [
                "config" => $config[$data['layout_id']],
                "sections" => []
            ];
            $result['success'] = true;
        }

        return $result;
    }

    /**
     * @return array
     */
    public static function get_item_layout()
    {
        return [
            "grid" => [
                "template" => "template_1",
                "column" => [
                    "extra_large" => 4,
                    "large" => 3,
                    "medium" => 2,
                    "small" => 1,
                    "extra_small" => 1,
                ]
            ],
            "list" => [
                "template" => "template_1"
            ],
            "map" => [
                "template" => "template_1"
            ]
        ];
    }

    /**
     * @return array
     */
    public static function get_basic_fields()
    {

        return [
            "style" => [
                "name" => "Style",
                "fields" => [
                    [
                        "type" => "color",
                        "label" => "Background color",
                        "name" => "background_color",
                    ],
                    [
                        "type" => "color",
                        "label" => "Text color",
                        "name" => "color",
                    ],
                ]
            ],
            "template" => [
                "name" => "Template",
                "fields" => [
//					[
//						"type"   => "checkbox",
//						"label"  => "Link option",
//						"name"   => "link_option",
//					],
//					[
//						"type"   => "checkbox",
//						"label"  => "Hidden value",
//						"name"   => "hidden_value",
//					],
                    [
                        "type" => "blog",
                        "label" => "Style template",
                        "name" => "style_template",
                        "items" => StmListingAttribute::get_style_templates()
                    ]
                ]
            ],
            "advanced" => [
                "name" => "Advanced",
                "fields" => [
                    [
                        "type" => "text",
                        "label" => "ID",
                        "name" => "id",
                    ],
                    [
                        "type" => "text",
                        "label" => "Class",
                        "name" => "class",
                    ]
                ]
            ]
        ];

    }

    /**
     * @param $listing_type
     *
     * @return mixed
     */
    public static function get_data_builder($listing_type)
    {
        $data = [];
        $data['config'] = [
            "section" => [
                "field_group" => UListingBuilder::get_section_field()
            ],
            "row" => [
                "field_group" => UListingBuilder::get_row_field()
            ],
            "col" => [
                "field_group" => UListingBuilder::get_col_field()
            ],
            "html-box" => [
                "field_group" => UListingBuilder::get_html_box_field_group()
            ],
            "short-code" => [
                "field_group" => UListingBuilder::get_short_code_field_group()
            ],
            "attribute-box" => [
                "field_group" => UListingBuilder::get_attribute_box_field_group()
            ],
            "quickview" => [
                "field_group" =>  UListingBuilder::get_quickview_field_group()
            ],
            "basic" => [
                "field_group" => self::get_basic_fields()
            ],

            "no-template"  => StmListingSingleLayout::get_no_template_field(),
            "list-block"   => StmListingSingleLayout::get_list_block_field(),
            "file-block"   => StmListingSingleLayout::get_file_block_field(),
            "input-block"  => StmListingSingleLayout::get_input_block_field(),
            "extra-block"  => StmListingSingleLayout::get_extra_block_field(),
            "yes-no-block" => StmListingSingleLayout::get_yesNo_block_field(),
            "map" => [
                "field_group" => [
                    "style" => [
                        "name" => "Style",
                        "fields" => [
                            [
                                "type" => "color",
                                "label" => "Background color",
                                "name" => "background_color",
                            ],
                            [
                                "type" => "color",
                                "label" => "Text color",
                                "name" => "color",
                            ],
                            [
                                "type" => "number-input",
                                "label" => "Font size",
                                "name" => "font_size",
                            ]
                        ]
                    ],
                    "map_config" => [
                        "name" => "Map",
                        "fields" => [
                            [
                                "type" => "text",
                                "label" => "Zoom",
                                "name" => "zoom",
                            ],
                            [
                                "type" => "text",
                                "label" => "Width",
                                "name" => "width",
                            ],
                            [
                                "type" => "text",
                                "label" => "Height",
                                "name" => "height",
                            ],
                        ]

                    ],
                    "advanced" => [
                        "name" => "Advanced",
                        "fields" => [
                            [
                                "type" => "text",
                                "label" => "ID",
                                "name" => "id",
                            ],
                            [
                                "type" => "text",
                                "label" => "Class",
                                "name" => "class",
                            ],
                            [
                                "type" => "margin",
                                "label" => "Margin",
                                "name" => "margin",
                            ],
                            [
                                "type" => "padding",
                                "label" => "Padding",
                                "name" => "padding",
                            ]
                        ]
                    ]
                ]
            ],
            "price" => [
                "field_group" => [
                    "style" => [
                        "name" => "Style",
                        "fields" => [
                            [
                                "type" => "color",
                                "label" => "Background color",
                                "name" => "background_color",
                            ],
                            [
                                "type" => "color",
                                "label" => "Text color",
                                "name" => "color",
                            ],
                            [
                                "type" => "number",
                                "label" => "Font size",
                                "name" => "font_size",
                            ]
                        ]
                    ],
                    "template" => [
                        "name" => "Template",
                        "fields" => [
                            [
                                "type" => "blog",
                                "label" => "Style template",
                                "name" => "style_template",
                                "items" => StmListingAttribute::get_price_style_templates()
                            ]
                        ]

                    ],
                    "advanced" => [
                        "name" => "Advanced",
                        "fields" => [
                            [
                                "type" => "text",
                                "label" => "ID",
                                "name" => "id",
                            ],
                            [
                                "type" => "text",
                                "label" => "Class",
                                "name" => "class",
                            ],
                            [
                                "type" => "margin",
                                "label" => "Margin",
                                "name" => "margin",
                            ],
                            [
                                "type" => "padding",
                                "label" => "Padding",
                                "name" => "padding",
                            ]
                        ]
                    ]
                ],
            ],
            "thumbnail-box" => [
                "field_group" => [
                    "template" => [
                        "name" => "Template",
                        "fields" => [
                            [
                                "type" => "blog",
                                "label" => "Style template",
                                "name" => "template",
                                "items" => self::get_thumbnail_box_template()
                            ]
                        ]

                    ],
                    "style" => [
                        "name" => "Style",
                        "fields" => [
                            [
                                "type" => "color",
                                "label" => "Background color",
                                "name" => "background_color",
                            ]
                        ]
                    ],
                    "advanced" => [
                        "name" => "Advanced",
                        "fields" => [
                            [
                                "type" => "text",
                                "label" => "ID",
                                "name" => "id",
                            ],
                            [
                                "type" => "text",
                                "label" => "Class",
                                "name" => "class",
                            ],
                            [
                                "type" => "text",
                                "label" => "Image Size (Example: 150x150)",
                                "name" => "image_size",
                            ],
                            [
                                "type" => "margin",
                                "label" => "Margin",
                                "name" => "margin",
                            ],
                            [
                                "type" => "padding",
                                "label" => "Padding",
                                "name" => "padding",
                            ],
                            [
                                "type" => "responsive-input",
                                "label" => "Height",
                                "name" => "height",
                            ],
                        ]
                    ]
                ]
            ],
            "title" => [
                "field_group" => [
                    "style" => [
                        "name" => "Style",
                        "fields" => [
                            [
                                "type" => "color",
                                "label" => "Background color",
                                "name" => "background_color",
                            ],
                            [
                                "type" => "color",
                                "label" => "Text color",
                                "name" => "color",
                            ],
                        ]
                    ],
                    "advanced" => [
                        "name" => "Advanced",
                        "fields" => [
                            [
                                "type" => "text",
                                "label" => "ID",
                                "name" => "id",
                            ],
                            [
                                "type" => "text",
                                "label" => "Class",
                                "name" => "class",
                            ],
                            [
                                "type" => "margin",
                                "label" => "Margin",
                                "name" => "margin",
                            ],
                            [
                                "type" => "padding",
                                "label" => "Padding",
                                "name" => "padding",
                            ]
                        ]
                    ]
                ]
            ],
            "photo-count" => [
                "field_group" => [
                    "style" => [
                        "name" => "Style",
                        "fields" => [
                            [
                                "type" => "color",
                                "label" => "Background color",
                                "name" => "background_color",
                            ]
                        ]
                    ],
                    "template" => [
                        "name" => "Template",
                        "fields" => [
                            [
                                "type" => "blog",
                                "label" => "Style template",
                                "name" => "template",
                                "items" => self::get_photo_count_template()
                            ]
                        ]

                    ],
                    "advanced" => [
                        "name" => "Advanced",
                        "fields" => [
                            [
                                "type" => "text",
                                "label" => "ID",
                                "name" => "id",
                            ],
                            [
                                "type" => "text",
                                "label" => "Class",
                                "name" => "class",
                            ],
                            [
                                "type" => "margin",
                                "label" => "Margin",
                                "name" => "margin",
                            ],
                            [
                                "type" => "padding",
                                "label" => "Padding",
                                "name" => "padding",
                            ]
                        ]
                    ]
                ]
            ],
            "category" => [
                "field_group" => [
                    "template" => [
                        "name" => "Template",
                        "fields" => [
                            [
                                "type" => "blog",
                                "label" => "Style template",
                                "name" => "template",
                                "items" => self::get_category_template()
                            ]
                        ]

                    ],
                    "style" => [
                        "name" => "Style",
                        "fields" => [
                            [
                                "type" => "color",
                                "label" => "Background color",
                                "name" => "background_color",
                            ]
                        ]
                    ],
                    "advanced" => [
                        "name" => "Advanced",
                        "fields" => [
                            [
                                "type" => "text",
                                "label" => "ID",
                                "name" => "id",
                            ],
                            [
                                "type" => "text",
                                "label" => "Class",
                                "name" => "class",
                            ],
                            [
                                "type" => "margin",
                                "label" => "Margin",
                                "name" => "margin",
                            ],
                            [
                                "type" => "padding",
                                "label" => "Padding",
                                "name" => "padding",
                            ]
                        ]
                    ]
                ]
            ],
            "region" => [
                "field_group" => [
                    "template" => [
                        "name" => "Template",
                        "fields" => [
                            [
                                "type" => "blog",
                                "label" => "Style template",
                                "name" => "template",
                                "items" => self::get_region_template()
                            ]
                        ]

                    ],
                    "style" => [
                        "name" => "Style",
                        "fields" => [
                            [
                                "type" => "color",
                                "label" => "Background color",
                                "name" => "background_color",
                            ]
                        ]
                    ],
                    "advanced" => [
                        "name" => "Advanced",
                        "fields" => [
                            [
                                "type" => "text",
                                "label" => "ID",
                                "name" => "id",
                            ],
                            [
                                "type" => "text",
                                "label" => "Class",
                                "name" => "class",
                            ],
                            [
                                "type" => "margin",
                                "label" => "Margin",
                                "name" => "margin",
                            ],
                            [
                                "type" => "padding",
                                "label" => "Padding",
                                "name" => "padding",
                            ]
                        ]
                    ]
                ]
            ],
            "card_config" => [
                "grid" => [
                    [
                        "type" => "blog",
                        "label" => "Style template",
                        "name" => "template",
                        "items" => self::get_grid_template()
                    ],
                    [
                        "type" => "responsive-select",
                        "label" => "Column",
                        "name" => "column",
                        "items" => [
                            1 => "Column 1",
                            2 => "Column 2",
                            3 => "Column 3",
                            4 => "Column 4"
                        ],
                    ],
                ],
                "list" => [
                    [
                        "type" => "blog",
                        "label" => "Style template",
                        "name" => "template",
                        "items" => self::get_list_template()
                    ],
                ],
                "map" => [
                    [
                        "type" => "blog",
                        "label" => "Style template",
                        "name" => "template",
                        "items" => self::get_map_template()
                    ],
                ]
            ]
        ];
        $data['donor'] = UListingBuilder::get_donor();
        $data['elements'][] = UListingBuilder::get_inner_row();
        $data['elements'][] = UListingBuilder::get_html_box();
        $data['elements'][] = UListingBuilder::get_short_code();
        $data['elements'][] = UListingBuilder::get_quickview();
        $data['elements'][] = UListingBuilder::get_attribute_box();
        $data['elements'][] = [
            "id" => rand(100, 999) . "_" . time(),
            "builder_type" => "item_card_layout",
            "title" => "Thumbnail box",
            "type" => "thumbnail-box",
            "group" => "basic",
            "module" => "thumbnail-box",
            "icon"   => "icon-37816091",
            "field_group" => "thumbnail-box",
            "elements_top" => [],
            "elements_bottom" => [],
            "params" => [
                "template" => "template_1",
                "type" => "thumbnail-box",
                "id" => "",
                "class" => "",
                "color" => "",
                "background_color" => "",
                "height" => [
                    "extra_large" => 300,
                    "large" => "",
                    "medium" => "",
                    "small" => "",
                    "extra_small" => ""
                ]
            ],
        ];
        $data['elements'][] = [
            "id" => rand(100, 999) . "_" . time(),
            "builder_type" => "item_card_layout",
            "title" => "Title",
            "type" => "attribute",
            "group" => "general",
            "module" => "element",
            "icon"  => "icon-text",
            "field_group" => "title",
            "params" => [
                "template" => "none",
                "type" => "title",
                "id" => "",
                "class" => "",
                "color" => "",
                "background_color" => "",
            ],
        ];
        $data['elements'][] = [
            "id" => rand(100, 999) . "_" . time(),
            "builder_type" => "item_card_layout",
            "title" => "Photo count",
            "type" => "attribute",
            "group" => "general",
            "module" => "element",
            "icon"   => "icon--11",
            "field_group" => "photo-count",
            "params" => [
                "template" => "template_1",
                "type" => "photo-count",
                "id" => "",
                "class" => "",
                "color" => "",
                "background_color" => "",
            ],
        ];
        $data['elements'][] = [
            "id" => rand(100, 999) . "_" . time(),
            "builder_type" => "item_card_layout",
            "title" => "Category",
            "type" => "attribute",
            "icon" => "icon-2438114",
            "group" => "general",
            "module" => "element",
            "field_group" => "category",
            "params" => [
                "template" => "template_1",
                "type" => "category",
                "id" => "",
                "class" => "",
                "color" => "",
                "background_color" => "",
            ],
        ];

        $data['elements'][] = [
            "id" => rand(100, 999) . "_" . time(),
            "builder_type" => "item_card_layout",
            "title" => "Region",
            "type" => "attribute",
            "group" => "general",
            "module" => "element",
            "icon"    => "icon-5352391",
            "field_group" => "region",
            "params" => [
                "template" => "template_1",
                "type" => "region",
                "id" => "",
                "class" => "",
                "color" => "",
                "background_color" => "",
            ],
        ];
        $data['elements'] = array_merge($data['elements'], self::build_attribute_list($listing_type->getAttribute()));
        return apply_filters("ulisting_item_layout_data", $data);
    }

    /**
     * Build attribute array for builder
     *
     * @param $attributes
     *
     * @return array
     */
    public static function build_attribute_list($attributes)
    {
        $i = 0;
        $data = [];
        foreach ($attributes as $attribute) {
            if ($attribute->type == StmListingAttribute::TYPE_GALLAEY || $attribute->type == StmListingAttribute::TYPE_ACCORDION)
                continue;

            $data[$i] = [
                "id" => rand(100, 999) . "_" . time(),
                "builder_type" => "item_card_layout",
                "title" => $attribute->title,
                "type" => "attribute",
                "group" => "general",
                "icon" => isset($attribute->icon) ? $attribute->icon : '',
                "module" => "element",
                "field_group" => self::get_field_group($attribute->type),
                "params" => [],
            ];

            switch ($attribute->type) {
                case StmListingAttribute::TYPE_LOCATION:
                    $data[$i]['params'] = [
                        "view_item" => "true",
                        "id" => "",
                        "class" => "",
                        "zoom" => 10,
                        "width" => "100%",
                        "height" => "300px",
                        "type" => "attribute",
                        "attribute" => $attribute->name,
                        "attribute_type" => $attribute->type
                    ];
                    break;
                case StmListingAttribute::TYPE_PRICE:
                    $data[$i]['params'] = [
                        "id" => "",
                        "type" => "attribute",
                        "class" => "",
                        "attribute" => $attribute->name,
                        "attribute_type" => $attribute->type,
                        "style_template" => "ulisting_style_1",
                    ];
                    break;
                default:
                    $data[$i]['params'] = [
                        "type" => "attribute",
                        "id" => "",
                        "class" => "",
                        "color" => "",
                        "attribute" => $attribute->name,
                        "attribute_type" => $attribute->type,
                        "background_color" => "",
                    ];
                    break;
            }

            $i++;
        }
        return $data;
    }

    /**
     * @param $attribute_type
     *
     * @return string
     */
    public static function get_field_group($attribute_type)
    {

        $file_block = [
            StmListingAttribute::TYPE_FILE,
        ];

        $input_block = [
            StmListingAttribute::TYPE_TEXT,
            StmListingAttribute::TYPE_NUMBER,
        ];

        $list_block = [
            StmListingAttribute::TYPE_SELECT,
            StmListingAttribute::TYPE_CHECKBOX,
            StmListingAttribute::TYPE_MULTISELECT,
        ];

        $extra_block = [
            StmListingAttribute::TYPE_TIME,
            StmListingAttribute::TYPE_DATE,
        ];

        $yesNo_block = [
            StmListingAttribute::TYPE_YES_NO,
        ];

        if(in_array($attribute_type, $list_block))  return 'list-block';
        if(in_array($attribute_type, $file_block))  return 'file-block';
        if(in_array($attribute_type, $input_block)) return 'input-block';
        if(in_array($attribute_type, $extra_block)) return 'extra-block';
        if(in_array($attribute_type, $yesNo_block)) return 'yes-no-block';

        switch ($attribute_type) {
            case StmListingAttribute::TYPE_LOCATION:
                return "map";
                break;
            case StmListingAttribute::TYPE_PRICE:
                return "price";
                break;
            case StmListingAttribute::TYPE_VIDEO:
                return "input-block";
                break;
            default:
                return "no-template";
                break;

        }
    }

    /**
     * @return array data for builder
     */
    public static function get_builder_data()
    {
        $result = [
            'success' => false
        ];
        $request_data = ulisting_sanitize_array($_GET);
        if (isset($request_data['listing_type_id']) AND $listingType = \uListing\Classes\StmListingType::find_one(sanitize_text_field($request_data['listing_type_id']))) {
            $result['data'] = self::get_data_builder($listingType);
            $result['success'] = true;
        }
        return $result;
    }

    /**
     * @param $element
     *
     * @return null|string
     */
    public static function get_element_template($element)
    {

        if (!isset($element['params']['type']))
            return "";

        switch ($element['params']['type']) {
            case "title":
                return "/builder/attribute/title";
                break;
            case "attribute":
                return "/builder/attribute/" . $element['params']['attribute_type'];
                break;
            case "quickview":
                return "builder/element/quickview";
                break;
            case "thumbnail-box":
                return "loop/thumbnail-box";
                break;
            case "compare":
                return "loop/compare";
                break;
            case "photo-count":
                return "loop/photo-count";
                break;
            case "custom-label":
                return "loop/custom-label";
                break;
            case "category":
                return "loop/category";
                break;
            case "region":
                return "loop/region";
                break;
            default:
                return null;
                break;
        }
    }

    /**
     * @return mixed|void
     */
    public static function get_category_template()
    {
        $templates = [
            "template_1" => [
                "icon" => ULISTING_URL . "/assets/img/category.png",
                "name" => "Style 1",
                "template" => "<span class='ulisting-listing-category'>[category]</span>",
            ]
        ];
        return apply_filters("ulisting_loop_category_template", $templates);
    }

    /**
     * @return mixed|void
     */
    public static function get_region_template()
    {
        $templates = [
            "template_1" => [
                "icon" => ULISTING_URL . "/assets/img/attribute_template_style_1.png",
                "name" => "Style 1",
                "template" => "<span class='ulisting-listing-region'>[region]</span>",
            ]
        ];
        return apply_filters("ulisting_loop_region_template", $templates);
    }

    /**
     * @param $template_id
     * @param $category
     *
     * @return mixed|string
     */
    public static function render_category($template_id, $category)
    {
        $templates = self::get_category_template();
        if (!isset($templates[$template_id]))
            return "";
        $template = $templates[$template_id]['template'];
        $template = str_replace("[category]", $category->name, $template);
        return $template;
    }

    /**
     * @param $template_id
     * @param $region
     *
     * @return mixed|string
     */
    public static function render_region($template_id, $region)
    {
        $templates = self::get_region_template();
        if (!isset($templates[$template_id]))
            return "";
        $template = $templates[$template_id]['template'];
        $template = str_replace("[region]", $region->name, $template);
        return $template;
    }

    /**
     * @return mixed|void
     */
    public static function get_photo_count_template()
    {
        $templates = [
            "template_1" => [
                "icon" => ULISTING_URL . "/assets/img/photo-count.png",
                "name" => "Style 1",
                "template" => '	<span class="ulisting-listing-photo-count"> <i class="fa fa-camera"></i> [count] </span>',
            ]
        ];
        return apply_filters("ulisting_loop_photo_count_template", $templates);
    }

    /**
     * @param $template_id
     * @param StmListing $model
     *
     * @return string
     */
    public static function render_photo_count($template_id, StmListing $model)
    {
        $templates = self::get_photo_count_template();
        if (!isset($templates[$template_id]))
            return "";
        $template = $templates[$template_id]['template'];
        $count = $model->getImageCount($model->ID);
        $count = (isset($count[$model->ID])) ? $count[$model->ID] : 0;
        $template = str_replace("[count]", $count, $template);
        return $template;
    }

    /**
     * @return mixed|void
     */
    public static function get_thumbnail_box_template()
    {
        $templates = [
            "template_1" => [
                "icon" => ULISTING_URL . "/assets/img/thumbnail-box.png",
                "name" => "Style 1",
                "template" => "<div class='ulisting-thumbnail-panel'>[thumbnail_panel]</div>",
                "template_inner" => "<div class='ulisting-thumbnail-panel-top'>[thumbnail_top]</div> <div class='ulisting-thumbnail-panel-bottom'>[thumbnail_bottom]</div>",
            ]
        ];
        return apply_filters("ulisting_loop_thumbnail_box_template", $templates);
    }

    /**
     * @param $template_id
     * @param $thumbnail_panel
     * @param $top
     * @param $bottom
     * @param $id
     *
     * @return mixed|string
     */
    public static function render_thumbnail_box($template_id, $thumbnail_panel, $top, $bottom, $id = '')
    {
        $templates = self::get_thumbnail_box_template();
        if (!isset($templates[$template_id]))
            return "";
        $template = $templates[$template_id]['template'];
        $template_inner = $templates[$template_id]['template_inner'];

        $template_inner = str_replace("[thumbnail_top]", $top, $template_inner);
        $template_inner = str_replace("[thumbnail_bottom]", $bottom, $template_inner);
        $template = str_replace("[thumbnail_panel]", $thumbnail_panel, $template);
        $template = str_replace("[thumbnail_panel_inner]", $template_inner, $template);
        $template = str_replace("[id]", $id, $template);

        return $template;
    }

    /**
     * @return mixed|void
     */
    public static function get_grid_template()
    {
        $templates = [
            "template_1" => [
                "icon" => ULISTING_URL . "/assets/img/grid-item.png",
                "name" => "Template 1",
            ]
        ];
        return apply_filters("ulisting_loop_grid_template", $templates);
    }

    /**
     * @return mixed|void
     */
    public static function get_list_template()
    {
        $templates = [
            "template_1" => [
                "icon" => ULISTING_URL . "/assets/img/list-item.png",
                "name" => "Template 1",
            ]
        ];
        return apply_filters("ulisting_loop_list_template", $templates);
    }

    /**
     * @return mixed|void
     */
    public static function get_map_template()
    {
        $templates = [
            "template_1" => [
                "icon" => ULISTING_URL . "/assets/img/map.png",
                "name" => "template_1",
            ]
        ];
        return apply_filters("ulisting_loop_map_template", $templates);
    }
}

