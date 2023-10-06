<?php
namespace uListing\Classes;

use uListing\Classes\Builder\UListingBuilder;

class StmInventoryLayout{

	/**
	 * Save layout for listing type page
	 */
	public static function save_layout(){
		$result = [
			'success' => false
		];
		$request_body = file_get_contents('php://input');
		$request_data = json_decode($request_body, true);
		if(isset($request_data['id'])){
			update_option($request_data['id'], ulisting_json_encode(["name" => $request_data['name'],"section" => $request_data['sections']]));
			$result['success'] = true;
			$result['data'] = $request_data;
			$style = UListingBuilder::generation_style($request_data['sections']);
			UListingBuilder::generation_css($request_data['id'], $style);
			self::save_builder_element($request_data);
		}
		return $result;
	}

    /**
     * Activate current layout for this ListingType
     */
    public static function uListing_active_inventory_template() {
        $result = [
            'message' => __('Access denied', 'ulisting'),
            'success' => false,
            'status'  => 'error',
        ];

        StmVerifyNonce::verifyNonce(sanitize_text_field($_POST['nonce']), 'ulisting-ajax-nonce');
        $post_id   = isset( $_POST['listing_type_id'] ) ? sanitize_text_field($_POST['listing_type_id']) : null;
        $layout_id = isset( $_POST['layout_id'] ) ? sanitize_text_field($_POST['layout_id']) : null;

        if ( current_user_can('manage_options') && !empty($layout_id)  && $listingType = StmListingType::find_one($post_id) ) {
            update_post_meta($post_id, 'listing_type_layout', sanitize_key($layout_id));
            $result['success'] = true;
            $result['message'] = __('Layout activated successfully', 'ulisting');
            $result['status']  = 'success';
        }
        wp_send_json($result);
    }

    /**
     *
     */
	public static function uListing_inventory_page_data() {
        $result = [
            'success' => false,
            'status'  => 'error',
            'data'    => null,
            'message' => __('Access denied', 'ulisting')
        ];
        if ( current_user_can('manage_options') && isset($_POST['nonce']) ) {
            StmVerifyNonce::verifyNonce(sanitize_text_field($_POST['nonce']), 'ulisting-ajax-nonce');
            $layouts = self::get_layout_list();

            $result['success'] = true;
            $result['status']  = 'success';
            $result['message'] = __('Listing type data got successfully', 'ulisting');
            $result['data'] = [
                'export_url'    => get_site_url() . '/wp-admin/admin-ajax.php?type=inventory&action=stm_export_current_layout',
                'default_icon'  => esc_url(ULISTING_URL . '/assets/img/inventory-default.png'),
                'layouts'       => isset($layouts['layouts']) ? $layouts['layouts'] : [],
                'sections'      => self::get_data_builder(),
            ];

            $layout_id = isset( $_POST['layout_id'] ) ? sanitize_text_field($_POST['layout_id']) : null;
            if ( !empty( $layout_id ) && $layout = self::getLayout($layout_id) )
                $result['data']['layout'] = $layout;
        }

        wp_send_json($result);
    }

	/**
	 * @param $items
	 * @param $data
	 *
	 * @return array∂
	 */
	public static function get_layout_element($items, $data){

		foreach ( $items as $item ){

			if ( isset($item['type']) && $item['type'] == 'inventory_element' ){
				$data[] = $item;
			}

			if( isset($item['rows']) ){
				$data = self::get_layout_element($item['rows'], $data);
			}

			if( isset($item['columns']) ){
				$data = self::get_layout_element($item['columns'], $data);
			}

			if( isset($item['elements']) ){
				$data = self::get_layout_element($item['elements'], $data);
			}

			if( isset($item['module']) && $item['module'] == 'tabs' AND isset($item['params']['items']) ){
				foreach ($item['params']['items'] as $tab_item){
					$data = self::get_layout_element($tab_item['elements'], $data);
				}
			}
		}
		return $data;
	}

	/**
	 * @param $data
	 */
	public static function save_builder_element($data){
		$elements = self::get_layout_element($data['sections'], []);
		$id = str_replace('ulisting','ulisting_element', $data['id']);
		update_option($id, ulisting_json_encode($elements));
	}

	/**
	 * @return array
	 */
	public static function getLayoutList() {
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

		foreach ( $result as $layout ){
			$value      = json_decode($layout[2],true);
			$image      = get_option('inventory_layout_image_'. $layout[0], '');
			$layouts[]  = [
				"id"        => $layout[1],
                "image"     => $image,
                "key"       => 'inventory_layout_image_'. $layout[0],
				"name"      => isset($value['name']) ? $value['name'] : '',
				"section"   => isset($value['section']) ? $value['section'] : []
			];
		}

		return $layouts;
	}

	/**
	 * @return array
	 */
	public static function get_layout_list() {
		global $wpdb;
		$result = [
			'success' => false,
			'layouts' => []
		];
		foreach (self::getLayoutList() as $layout){
            $image      = get_option($layout['key'], '');
			$result['layouts'][] = [
			    "image" => $image,
				"id"    => $layout['id'],
				"name"  => $layout['name']
			];
		}
		$result['success'] = true;
		return $result;
	}

	/**
	 * @param $id
	 *
	 * @return array|mixed|null|object
	 */
	public static function getLayout($id) {
		if($layout = get_option($id))
			return json_decode($layout, true);
		return [];
	}

	public static function uListing_save_inventory_layout() {
        $result = [
            'message' => __('Access denied', 'ulisting'),
            'success' => false,
            'status'  => 'error',
            'data'    => [],
        ];

        $request_body = file_get_contents('php://input');
        $request_data = json_decode($request_body, true);

        $layout_id = isset($request_data['layout_id']) ? $request_data['layout_id'] : null;
        $listing_type_id = isset($request_data['listing_type_id']) ? $request_data['listing_type_id'] : null;
        $name      = isset($request_data['layout_name']) ? $request_data['layout_name'] : null;
        $section   = isset($request_data['sections'])  ? $request_data['sections'] : [];
        StmVerifyNonce::verifyNonce(sanitize_text_field($request_data['nonce']), 'ulisting-ajax-nonce');

        if ( !empty($section) ){
            $section = str_replace('\\"', "'", ulisting_json_encode($section) );
            $section = json_decode($section, true);
        }

        if ( !empty($layout_id) ){
            update_option($layout_id, ulisting_json_encode(["name" => $name,"section" => $section]));
            $result['data']    = self::get_layout_list();
            $result['success'] = true;
            $result['message'] = __('Layout saved successfully', 'ulisting');
            $result['status']  = 'success';
            $style = UListingBuilder::generation_style($section);
            UListingBuilder::generation_css($layout_id, $style, $listing_type_id);
            self::save_builder_element(['id' => $layout_id, 'name' => $name, 'sections' => $section]);
        }

        wp_send_json($result);
    }

	/**
	 * @return array
	 */
	public static function get_layout() {
        $result = [
            'message'  => __('Access denied', 'ulisting'),
            'success'  => false,
            'status'   => 'error',
            'sections' => self::get_data_builder(),
            'data'     => [],
        ];

        $layout_id = isset($_POST['layout_id']) ? sanitize_text_field($_POST['layout_id']) : null;
        StmVerifyNonce::verifyNonce(sanitize_text_field($_POST['nonce']), 'ulisting-ajax-nonce');

        if ( $layout = self::getLayout( $layout_id ) ) {
                $result['data']    = $layout;
                $result['success'] = true;
                $result['message'] = __('Layout got successfully', 'ulisting');
                $result['status']  = 'success';
        }

        wp_send_json($result);
	}

	public static function get_layout_delete() {
		$result = [
		    'message' => __('Access denied', 'ulisting'),
			'success' => false,
            'status'  => 'error',
		];

		$id = isset($_POST['id']) ? sanitize_text_field($_POST['id']) : null;
		if ( 'ulisting_type_page_layout' === substr($id, 0, 25)
		     && current_user_can('manage_options')
		     && !empty($id) AND $layout = get_option($id) && isset($_POST['nonce'])
		) {
			StmVerifyNonce::verifyNonce(sanitize_text_field($_POST['nonce']), 'ulisting-ajax-nonce');
			delete_option($id);
			$result['message'] = 'Layout deleted successfully';
			$result['success'] = true;
			$result['status']  = 'success';
		}

		wp_send_json($result);
	}

	/**
	 * @param $listing_type
	 *
	 * @return mixed
	 */
	public static function get_data_builder(){
		$data = [];
		$data['config'] = [
			"section" => [
				"field_group" => UListingBuilder::get_section_field()
			],
			"row" => [
				"field_group" => UListingBuilder::get_row_field()
			],
			"col" => [
				"field_group" =>  UListingBuilder::get_col_field()
			],
			"html-box" => [
				"field_group" =>  UListingBuilder::get_html_box_field_group()
			],
			"short-code" => [
				"field_group" =>  UListingBuilder::get_short_code_field_group()
			],
			"tabs" => [
				"field_group" => UListingBuilder::get_tabs_field()
			],
			"general" => [
				"field_group" => [
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
								"type"  => "text",
								"label" => "ID",
								"name"  => "id",
							],
							[
								"type"  => "text",
								"label" => "Class",
								"name"  => "class",
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
				]
			],
			"map" => [
				"field_group" => [
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
								"type"   => "blog",
								"label"  => "Style template",
								"name"   => "template",
								"items"  => self::get_map_template()
							]
						]

					],
					"advanced" => [
						"name" => "Advanced",
						"fields" => [
							[
								"type"  => "responsive-input",
								"label" => "Height",
								"name"  => "height",
							],
							[
								"type"  => "text",
								"label" => "ID",
								"name"  => "id",
							],

							[
								"type"  => "text",
								"label" => "Class",
								"name"  => "class",
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
				]
			],
			"sort" => [
				"field_group" => [
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
								"type"   => "blog",
								"label"  => "Style template",
								"name"   => "template",
								"items"  => self::get_sort_template()
							]
						]

					],
					"advanced" => [
						"name" => "Advanced",
						"fields" => [
							[
								"type"  => "text",
								"label" => "ID",
								"name"  => "id",
							],
							[
								"type"  => "text",
								"label" => "Class",
								"name"  => "class",
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
				]
			],
			"column_switch" => [
				"field_group" => [
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
								"type"   => "blog",
								"label"  => "Style template",
								"name"   => "template",
								"items"  => self::get_column_switch_template()
							]
						]

					],
					"advanced" => [
						"name" => "Advanced",
						"fields" => [
							[
								"type"  => "text",
								"label" => "ID",
								"name"  => "id",
							],
							[
								"type"  => "text",
								"label" => "Class",
								"name"  => "class",
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
				]
			],
			"pagination" => [
				"field_group" => [
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
								"type"   => "blog",
								"label"  => "Style template",
								"name"   => "template",
								"items"  => self::get_pagination_template()
							]
						]

					],
					"advanced" => [
						"name" => "Advanced",
						"fields" => [
							[
								"type"  => "text",
								"label" => "ID",
								"name"  => "id",
							],
							[
								"type"  => "text",
								"label" => "Class",
								"name"  => "class",
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
				]
			],

            "matches" => [
                "field_group" => [
                    "advanced" => [
                        "name" => "Advanced",
                        "fields" => [
                            [
                                "type"  => "text",
                                "label" => "Title",
                                "name"  => "title",
                                "value" => "Matches"
                            ],
                            [
                                "type"  => "text",
                                "label" => "ID",
                                "name"  => "id",
                            ],
                            [
                                "type"  => "text",
                                "label" => "Class",
                                "name"  => "class",
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
                ]
            ],

			"list" => [
				"field_group" => [
					"template" => [
						"name" => "Template",
						"fields" => [
							[
								"type"   => "select",
								"label"  => "Default item view",
								"name"   => "default_item_view",
								"items"   => [
									"grid" => "Grid",
									"list" => "List"
								],
							],

							[
								"type"   => "blog",
								"label"  => "Style template",
								"name"   => "template",
								"items"  => self::get_list_template()
							],
							[
								"type"  => "text",
								"label" => "Limit number pagination",
								"name"  => "limit_number_pagination",
							]
						]

					],
					"advanced" => [
						"name" => "Advanced",
						"fields" => [
							[
								"type"  => "text",
								"label" => "ID",
								"name"  => "id",
							],
							[
								"type"  => "text",
								"label" => "Class",
								"name"  => "class",
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
					"style" => [
						"name" => "Style",
						"fields" => [
							[
								"type"   => "color",
								"label"  => "Background color",
								"name"   => "background_color",
							]
						]
					]
				]
			],
			"filter" => [
				"field_group" => [
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
								"type"   => "blog",
								"label"  => "Style template",
								"name"   => "template",
								"items"  => self::get_filter_template()
							]
						]

					],
					"advanced" => [
						"name" => "Advanced",
						"fields" => [
							[
								"type"  => "text",
								"label" => "ID",
								"name"  => "id",
							],
							[
								"type"  => "text",
								"label" => "Class",
								"name"  => "class",
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
				]
			],
			"title" => [
				"field_group" => [
					"style" => [
						"name" => "Style",
						"fields" => [
							[
								"type"   => "color",
								"label"  => "Background color",
								"name"   => "background_color",
							],
							[
								"type"  => "responsive-input",
								"label" => "Font size",
								"name"  => "font_size",
							],
						]
					],
					"template" => [
						"name" => "Template",
						"fields" => [
							[
								"type"   => "blog",
								"label"  => "Style template",
								"name"   => "template",
								"items"  => self::get_title_template()
							]
						]

					],
					"advanced" => [
						"name" => "Advanced",
						"fields" => [
							[
								"type"  => "text",
								"label" => "ID",
								"name"  => "id",
							],
							[
								"type"  => "text",
								"label" => "Class",
								"name"  => "class",
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
				]
			],
			"reset_filter" => [
				"field_group" => [
					"style" => [
						"name" => "Style",
						"fields" => [
							[
								"type"   => "color",
								"label"  => "Color",
								"name"   => "color",
							],
							[
								"type"   => "color",
								"label"  => "Background color",
								"name"   => "background_color",
							],
							[
								"type"  => "responsive-input",
								"label" => "Font size",
								"name"  => "font_size",
							],
						]
					],
					"template" => [
						"name" => "Template",
						"fields" => [
							[
								"type"   => "blog",
								"label"  => "Style template",
								"name"   => "template",
								"items"  => self::get_reset_filter_template()
							]
						]

					],
					"advanced" => [
						"name" => "Advanced",
						"fields" => [
							[
								"type"  => "text",
								"label" => "ID",
								"name"  => "id",
							],
							[
								"type"  => "text",
								"label" => "Class",
								"name"  => "class",
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
				]
			]
		];
		$data['donor']      = UListingBuilder::get_donor();
		$data['elements'][] = UListingBuilder::get_inner_row();
		$data['elements'][] = UListingBuilder::get_html_box();
		$data['elements'][] = UListingBuilder::get_short_code();
		$data['elements'][] = UListingBuilder::get_tabs_box();
		$data['elements'][] = [
			"id" => rand(100, 999)."_".time(),
			"title"        => "Title",
			"type"         => "inventory_element",
			"group"        => "general",
            "icon"         => "icon-text",
            "module"       => "element",
			"field_group"  => "title",
			"params"       => [
				"template"          => "template_1",
				"type"              => "title",
				"id"                => "",
				"class"             => "",
				"color"             => "",
				"background_color"  => "",
			],
		];
		$data['elements'][] = [
			"id" => rand(100, 999)."_".time(),
			"title"        => "Reset filter",
			"type"         => "inventory_element",
			"group"        => "general",
			"module"       => "element",
            "icon"         => "icon-117115",
			"field_group"  => "reset_filter",
			"params"       => [
				"template"          => "template_1",
				"type"              => "reset_filter",
				"id"                => "",
				"class"             => "",
				"color"             => "",
				"background_color"  => "",
			],
		];
		$data['elements'][] = [
			"id" => rand(100, 999)."_".time(),
			"title"        => "Filter",
			"type"         => "inventory_element",
			"group"        => "general",
            "icon"         => "icon-876756",
			"module"       => "element",
			"field_group"  => "filter",
			"params"       => [
				"template"          => "template_1",
				"type"              => "filter",
				"id"                => "",
				"class"             => "",
				"color"             => "",
				"background_color"  => "",
			],
		];
		$data['elements'][] = [
			"id" => rand(100, 999)."_".time(),
			"title"        => "List",
			"type"         => "inventory_element",
			"group"        => "general",
			"module"       => "element",
            "icon"         => "icon-1",
			"field_group"  => "list",
			"params"       => [
				"template"          => "template_1",
				"type"              => "list",
				"default_item_view" => "grid",
				"id"                => "",
				"class"             => "",
				"color"             => "",
				"background_color"  => "",
			],
		];
		$data['elements'][] = [
			"id" => rand(100, 999)."_".time(),
			"title"        => "Map",
			"type"         => "inventory_element",
			"group"        => "general",
			"module"       => "element",
            "icon"         => "icon-5352391",
			"field_group"  => "map",
			"params"       => [
				"template"          => "template_1",
				"type"              => "map",
				"id"                => "",
				"class"             => "",
				"color"             => "",
				"background_color"  => "",
				"height" => [
					"extra_large" => 400,
					"large" => "",
					"medium" => "",
					"small" => "",
					"extra_small" => ""
            	]
			],
		];
		$data['elements'][] = [
			"id" => rand(100, 999)."_".time(),
			"title"        => "Sort",
			"type"         => "inventory_element",
			"group"        => "general",
            "icon"         => "icon-117221",
			"module"       => "element",
			"field_group"  => "sort",
			"params"       => [
				"template"          => "template_1",
				"type"              => "sort",
				"id"                => "",
				"class"             => "",
				"color"             => "",
				"background_color"  => "",
			],
		];
		$data['elements'][] = [
			"id" => rand(100, 999)."_".time(),
			"title"        => "Column switch",
			"type"         => "inventory_element",
			"group"        => "general",
            "icon"         => "icon-19947601",
			"module"       => "element",
			"field_group"  => "column_switch",
			"params"       => [
				"template"          => "template_1",
				"type"              => "column_switch",
				"id"                => "",
				"class"             => "",
				"color"             => "",
				"background_color"  => "",
			],
		];
		$data['elements'][] = [
			"id" => rand(100, 999)."_".time(),
			"title"        => "Pagination",
			"type"         => "inventory_element",
			"group"        => "general",
			"module"       => "element",
            "icon"         => "icon-1287074",
			"field_group"  => "pagination",
			"params"       => [
				"template"          => "template_1",
				"type"              => "pagination",
				"id"                => "",
				"class"             => "",
				"color"             => "",
				"background_color"  => "",
			],
		];

        $data['elements'][] = [
            "id" => rand(100, 999)."_".time(),
            "title"        => "Matches",
            "type"         => "inventory_element",
            "group"        => "general",
            "module"       => "element",
            "icon"         => "icon-2910785",
            "field_group"  => "matches",
            "params"       => [
                "type"              => "matches",
                "id"                => "",
                "class"             => "",
                "color"             => "",
                "title"             => "Matches",
            ],
        ];

		return apply_filters("ulisting_inventory_layout_data", $data);
	}

	/**
	 * @return array data for builder
	 */
	public static function get_builder_data(){
		$result = [
			'success' => false
		];

		$result['data']    = self::get_data_builder();
		$result['success'] = true;

		return $result;
	}

	/**
	 * @param $element
	 *
	 * @return null|string
	 */
	public static function get_element_template($element){
		switch ($element['params']['type']){
			case "filter":
				return "listing-list/filter";
				break;
			case "list":
				return "listing-list/listing-list";
				break;
			case "pagination":
				return "listing-list/pagination";
				break;
            case "matches":
                return "listing-list/matches";
                break;
			case "map":
				return "listing-list/map";
				break;
			case "sort":
				return "listing-list/listing-order";
				break;
			case "column_switch":
				return "listing-list/column-switch";
				break;
			case "title":
				return "listing-list/title";
				break;
			case "reset_filter":
				return "listing-list/reset-filter";
				break;
			default:
				return null;
				break;
		}
	}

	/**
	 * @return mixed|void
	 */
	public static function get_sort_template(){
		$templates = [
			"template_1" => [
				"icon" => ULISTING_URL."/assets/img/ordering-options.png",
				"name" => "Style 1",
				"template" => "[sort_panel]",
				"template_inner" => "[list] [select]",
			]
		];
		return apply_filters("ulisting_inventory_sort_template", $templates);

	}

	/**
	 * @param $template_id string
	 * @param $sort_panel string
	 * @param $list string
	 * @param $select string
	 *
	 * @return template string
	 */
	public static function render_sort($template_id, $sort_panel, $list, $select){
		$templates      = self::get_sort_template();
		if ( !isset($templates[$template_id]) )
			return "";
		$template       = $templates[$template_id]['template'];
		$template_inner = $templates[$template_id]['template_inner'];
		$template_inner = str_replace("[list]", $list, $template_inner);
		$template_inner = str_replace("[select]", $select, $template_inner);
		$template       = str_replace("[sort_panel]", $sort_panel, $template);
		$template       = str_replace("[sort_pane_inner]", $template_inner, $template);
		return $template;
	}

	/**
	 * @return mixed|void
	 */
	public static function get_column_switch_template(){
		$templates = [
			"template_1" => [
				"icon" => ULISTING_URL."/assets/img/column-switch.png",
				"name" => "Style 1",
				"template" => "[column_switch_panel]",
				"template_inner" => "[button_switch]",
			]
		];
		return apply_filters("ulisting_inventory_column_switch_template", $templates);
	}

	/**
	 * @param $template_id string
	 * @param $column_switch_panel string
	 * @param $button_switch string
	 *
	 * @return string
	 */
	public static function render_column_switch($template_id ,$column_switch_panel, $button_switch) {
		$templates      = self::get_column_switch_template();
		if(!isset($templates[$template_id]))
			return "";
		$template       = $templates[$template_id]['template'];
		$template_inner = $templates[$template_id]['template_inner'];
		$template_inner = str_replace("[button_switch]", $button_switch, $template_inner);
		$template       = str_replace("[column_switch_panel]", $column_switch_panel, $template);
		$template       = str_replace("[column_switch_inner]", $template_inner, $template);
		return $template;
	}

	/**
	 * @return mixed|void
	 */
	public static function get_pagination_template(){
		$templates = [
			"template_1" => [
				"icon" => ULISTING_URL."/assets/img/pagination.png",
				"name" => "Style 1",
				"template" => "[pagination_panel]",
				"template_inner" => "[pagination]",
			]
		];
		return apply_filters("ulisting_inventory_pagination_template", $templates);
	}


	/**
	 * @param $template_id string
	 * @param $pagination_panel string
	 * @param $pagination string
	 *
	 * @return template string
	 */
	public static function render_pagination($template_id ,$pagination_panel, $pagination) {
		$templates      = self::get_pagination_template();
		if(!isset($templates[$template_id]))
			return "";
		$template       = $templates[$template_id]['template'];
		$template_inner = $templates[$template_id]['template_inner'];
		$template_inner = str_replace("[pagination]", $pagination, $template_inner);
		$template       = str_replace("[pagination_panel]", $pagination_panel, $template);
		$template       = str_replace("[pagination_panel_inner]", $template_inner, $template);
		return $template;
	}

	/**
	 * @return mixed|void
	 */
	public static function get_list_template(){
		$templates = [
			"template_1" => [
				"icon" => ULISTING_URL."/assets/img/list.png",
				"name" => "Style 1",
				"template" => "<div class='scroll-panel-list'>[list_panel]</div>",
				"template_inner" => " <div class='stm-row'>[feature_list]</div> <div class='stm-row'>[listing_list]</div>[no_list]",
			]
		];
		return apply_filters("ulisting_inventory_list_template", $templates);
	}

	/**
	 * @param $template_id string
	 * @param $list_panel string
	 * @param $feature_list string
	 * @param $listing_list string
	 * @param $no_list string
	 *
	 * @return d . string
	 */
	public static function render_list($template_id , $list_panel, $feature_list, $listing_list, $no_list = '') {
		$templates = self::get_list_template();
		if(!isset($templates[$template_id]))
			return "";
		$template       = $templates[$template_id]['template'];
		$template_inner = $templates[$template_id]['template_inner'];
        $template_inner = str_replace("[no_list]",  $no_list, $template_inner);
        $template_inner = str_replace("[feature_list]", $feature_list, $template_inner);
        $template_inner = str_replace("[listing_list]", $listing_list, $template_inner);
        $template       = str_replace("[list_panel]", $list_panel, $template);
		$template       = str_replace("[list_panel_inner]", $template_inner, $template);
		return $template;
	}

	/**
	 * @return mixed|void
	 */
	public static function get_map_template(){
		$templates = [
			"template_1" => [
				"icon" => ULISTING_URL."/assets/img/map.png",
				"name" => "Style 1",
				"template" => "[map_panel]",
				"template_inner" => "[map]",
			]
		];
		return apply_filters("ulisting_inventory_map_template", $templates);
	}

	/**
	 * @param $template_id string
	 * @param $map_panel string
	 * @param $map string
	 *
	 * @return template string
	 */
	public static function render_map($template_id, $map_panel, $map){
		$templates      = self::get_map_template();
		if(!isset($templates[$template_id]))
			return "";
		$template       = $templates[$template_id]['template'];
		$template_inner = $templates[$template_id]['template_inner'];
		$template_inner = str_replace("[map]", $map, $template_inner);
		$template       = str_replace("[map_panel]", $map_panel, $template);
		$template       = str_replace("[map_panel_inner]", $template_inner, $template);
		return $template;
	}

	/**
	 * @return mixed|void
	 */
	public static function get_filter_template(){
		$templates = [
			"template_1" => [
				"icon" => ULISTING_URL."/assets/img/filter.png",
				"name" => "Style 1",
				"template" => "[filter_panel]",
				"template_inner" => "[filter]",
				"template_field" => "[field]",
			]
		];
		return apply_filters("ulisting_inventory_filter_template", $templates);
	}

	/**
	 * @param $template_id string
	 * @param $filter_panel string
	 * @param $filter string
	 * @param $filter_field string
	 *
	 * @return template string
	 */
	public static function render_filter($template_id, $filter_panel, $filter, $filter_field){

		$templates      = self::get_filter_template();

		if(!isset($templates[$template_id]))
			return "";

		$fields         = "";
		$template       = $templates[$template_id]['template'];
		$template_inner = $templates[$template_id]['template_inner'];
		$template_field = $templates[$template_id]['template_field'];

		foreach ($filter_field as $field){
			$fields .= str_replace("[field]", $field, $template_field);;
		}

		$template_inner = str_replace("[filter]", $filter, $template_inner);
		$template_inner = str_replace("[filter_field]", $fields, $template_inner);
		$template       = str_replace("[filter_panel]", $filter_panel, $template);
		$template       = str_replace("[filter_panel_inner]", $template_inner, $template);

		return $template;
	}

	/**
	 * @return mixed|void
	 */
	public static function get_title_template(){
		$templates = [
			"template_1" => [
				"icon" => ULISTING_URL."/assets/img/title.png",
				"name" => "Style 1",
				"template" => "[title_panel]",
				"template_inner" => "[title]",
			]
		];
		return apply_filters("ulisting_inventory_title_template", $templates);
	}

	/**
	 * @return mixed|void
	 */
	public static function get_reset_filter_template(){
		$templates = [
			"template_1" => [
				"icon" => ULISTING_URL."/assets/img/reset.png",
				"name" => "Style 1",
				"template" => "[reset_filter_panel]",
				"template_inner" => "[reset_filter]",
			]
		];
		return apply_filters("ulisting_inventory_reset_filter_template", $templates);
	}

	/**
	 * @param $template_id
	 * @param $title_panel
	 * @param $title
	 *
	 * @return mixed|string
	 */
	public static function render_title($template_id, $title_panel, $title){
		$templates = self::get_title_template();

		if(!isset($templates[$template_id]))
			return "";

		$fields         = "";
		$template       = $templates[$template_id]['template'];
		$template_inner = $templates[$template_id]['template_inner'];
		$template_inner = str_replace("[title]", $title, $template_inner);
		$template       = str_replace("[title_panel]", $title_panel, $template);
		$template       = str_replace("[title_panel_inner]", $template_inner, $template);

		return $template;
	}

	public static function render_reset_filter($template_id, $reset_filter_panel, $reset_filter){
		$templates = self::get_reset_filter_template();

		if(!isset($templates[$template_id]))
			return "";

		$fields         = "";
		$template       = $templates[$template_id]['template'];
		$template_inner = $templates[$template_id]['template_inner'];
		$template_inner = str_replace("[reset_filter]", $reset_filter, $template_inner);
		$template       = str_replace("[reset_filter_panel]", $reset_filter_panel, $template);
		$template       = str_replace("[reset_filter_panel_inner]", $template_inner, $template);

		return $template;
	}
}