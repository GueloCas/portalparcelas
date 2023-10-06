<?php

namespace uListing\Classes;

use uListing\Classes\StmListingAttribute;
use uListing\Classes\Builder\UListingBuilder;
use uListing\Classes\StmListingType;

class StmListingSingleLayout {

	/**
	 * @return array
	 */
	public static function get_basic_fields() {
		return [
			"style"    => [
				"name"   => "Style",
				"fields" => [
					[
						"type"  => "color",
						"label" => __( "Background color", 'ulisting' ),
						"name"  => "background_color",
					],
					[
						"type"  => "color",
						"label" => __( "Text color", 'ulisting' ),
						"name"  => "color",
					],
				]
			],
			"template" => [
				"name"   => "Template",
				"fields" => [
					[
						"type"  => "blog",
						"label" => __( "Style template", 'ulisting' ),
						"name"  => "style_template",
						"items" => StmListingAttribute::get_style_templates()
					]
				]
			],
			"advanced" => [
				"name"   => "Advanced",
				"fields" => [
					[
						"type"  => "text",
						"label" => __( "ID", 'ulisting' ),
						"name"  => "id",
					],
					[
						"type"  => "text",
						"label" => __( "Class", 'ulisting' ),
						"name"  => "class",
					]
				]
			]
		];
	}

	/**
	 * @return array
	 */
	public static function get_no_template_field() {
		return [
			"field_group" => [
				"style"    => [
					"name"   => "Style",
					"fields" => [
						[
							"type"  => "color",
							"label" => __( "Background color", 'ulisting' ),
							"name"  => "background_color",
						],
						[
							"type"  => "color",
							"label" => __( "Text color", 'ulisting' ),
							"name"  => "color",
						],
					]
				],
				"advanced" => [
					"name"   => "Advanced",
					"fields" => [
						[
							"type"  => "text",
							"label" => __( "ID", 'ulisting' ),
							"name"  => "id",
						],
						[
							"type"  => "text",
							"label" => __( "Class", 'ulisting' ),
							"name"  => "class",
						],
						[
							"type"  => "margin",
							"label" => __( "Margin", 'ulisting' ),
							"name"  => "margin",
						],
						[
							"type"  => "padding",
							"label" => __( "Padding", 'ulisting' ),
							"name"  => "padding",
						]
					]
				]
			]
		];
	}

	/**
	 * @return array
	 */
	public static function get_list_block_field() {
		return [
			"field_group" => [
				"style"    => [
					"name"   => "Style",
					"fields" => [
						[
							"type"  => "color",
							"label" => __( "Background color", 'ulisting' ),
							"name"  => "background_color",
						],
						[
							"type"  => "color",
							"label" => __( "Text color", 'ulisting' ),
							"name"  => "color",
						],
					]
				],
				"template" => [
					"name"   => "Template",
					"fields" => [
						[
							"type"  => "blog",
							"label" => __( "Style template", 'ulisting' ),
							"name"  => "style_template",
							"items" => StmListingAttribute::get_style_templates()
						]
					]
				],
				"advanced" => [
					"name"   => "Advanced",
					"fields" => [
						[
							"type"  => "text",
							"label" => __( "ID", 'ulisting' ),
							"name"  => "id",
						],
						[
							"type"  => "text",
							"label" => __( "Class", 'ulisting' ),
							"name"  => "class",
						],
						[
							"type"  => "margin",
							"label" => __( "Margin", 'ulisting' ),
							"name"  => "margin",
						],
						[
							"type"  => "padding",
							"label" => __( "Padding", 'ulisting' ),
							"name"  => "padding",
						]
					]
				]
			]
		];
	}

	/**
	 * @return array
	 */
	public static function get_input_block_field() {
		return [
			"field_group" => [
				"style"    => [
					"name"   => "Style",
					"fields" => [
						[
							"type"  => "color",
							"label" => __( "Background color", 'ulisting' ),
							"name"  => "background_color",
						],
						[
							"type"  => "color",
							"label" => __( "Text color", 'ulisting' ),
							"name"  => "color",
						],
					]
				],
				"template" => [
					"name"   => "Template",
					"fields" => [
						[
							"type"  => "blog",
							"label" => __( "Style template", 'ulisting' ),
							"name"  => "style_template",
							"items" => StmListingAttribute::get_input_block_style_templates()
						]
					]
				],
				"advanced" => [
					"name"   => "Advanced",
					"fields" => [
						[
							"type"  => "text",
							"label" => __( "ID", 'ulisting' ),
							"name"  => "id",
						],
						[
							"type"  => "text",
							"label" => __( "Class", 'ulisting' ),
							"name"  => "class",
						],
						[
							"type"  => "margin",
							"label" => __( "Margin", 'ulisting' ),
							"name"  => "margin",
						],
						[
							"type"  => "padding",
							"label" => __( "Padding", 'ulisting' ),
							"name"  => "padding",
						]
					]
				]
			]
		];
	}

	/**
	 * @return array
	 */
	public static function get_file_block_field() {
		return [
			"field_group" => [
				"style"    => [
					"name"   => "Style",
					"fields" => [
						[
							"type"  => "color",
							"label" => __( "Background color", 'ulisting' ),
							"name"  => "background_color",
						],
						[
							"type"  => "color",
							"label" => __( "Text color", 'ulisting' ),
							"name"  => "color",
						],
					]
				],
				"template" => [
					"name"   => "Template",
					"fields" => [
						[
							"type"  => "blog",
							"label" => __( "Style template", 'ulisting' ),
							"name"  => "style_template",
							"items" => StmListingAttribute::get_file_block_style_templates()
						]
					]
				],
				"advanced" => [
					"name"   => "Advanced",
					"fields" => [
						[
							"type"  => "text",
							"label" => __( "ID", 'ulisting' ),
							"name"  => "id",
						],
						[
							"type"  => "text",
							"label" => __( "Class", 'ulisting' ),
							"name"  => "class",
						],
						[
							"type"  => "margin",
							"label" => __( "Margin", 'ulisting' ),
							"name"  => "margin",
						],
						[
							"type"  => "padding",
							"label" => __( "Padding", 'ulisting' ),
							"name"  => "padding",
						]
					]
				]
			]
		];
	}

	/**
	 * @return array
	 */

	public static function get_yesNo_block_field() {
		return [
			"field_group" => [
				"style"    => [
					"name"   => "Style",
					"fields" => [
						[
							"type"  => "color",
							"label" => __( "Background color", 'ulisting' ),
							"name"  => "background_color",
						],
						[
							"type"  => "color",
							"label" => __( "Text color", 'ulisting' ),
							"name"  => "color",
						],
					]
				],
				"template" => [
					"name"   => "Template",
					"fields" => [
						[
							"type"  => "blog",
							"label" => __( "Style template", 'ulisting' ),
							"name"  => "style_template",
							"items" => StmListingAttribute::get_yesNo_block_style_templates()
						]
					]
				],
				"advanced" => [
					"name"   => "Advanced",
					"fields" => [
						[
							"type"  => "text",
							"label" => __( "ID", 'ulisting' ),
							"name"  => "id",
						],
						[
							"type"  => "text",
							"label" => __( "Class", 'ulisting' ),
							"name"  => "class",
						],
						[
							"type"  => "margin",
							"label" => __( "Margin", 'ulisting' ),
							"name"  => "margin",
						],
						[
							"type"  => "padding",
							"label" => __( "Padding", 'ulisting' ),
							"name"  => "padding",
						]
					]
				]
			]
		];
	}

	public static function get_extra_block_field() {
		return [
			"field_group" => [
				"style"    => [
					"name"   => "Style",
					"fields" => [
						[
							"type"  => "text",
							"label" => __( "Label", 'ulisting' ),
							"name"  => "label",
						],
						[
							"type"  => "color",
							"label" => __( "Background color", 'ulisting' ),
							"name"  => "background_color",
						],
						[
							"type"  => "color",
							"label" => __( "Text color", 'ulisting' ),
							"name"  => "color",
						],
					]
				],
				"template" => [
					"name"   => "Template",
					"fields" => [
						[
							"type"  => "blog",
							"label" => __( "Style template", 'ulisting' ),
							"name"  => "style_template",
							"items" => StmListingAttribute::get_extra_block_style_templates()
						]
					]
				],
				"advanced" => [
					"name"   => "Advanced",
					"fields" => [
						[
							"type"  => "text",
							"label" => __( "ID", 'ulisting' ),
							"name"  => "id",
						],
						[
							"type"  => "text",
							"label" => __( "Class", 'ulisting' ),
							"name"  => "class",
						],
						[
							"type"  => "margin",
							"label" => __( "Margin", 'ulisting' ),
							"name"  => "margin",
						],
						[
							"type"  => "padding",
							"label" => __( "Padding", 'ulisting' ),
							"name"  => "padding",
						]
					]
				]
			]
		];
	}

	/**
	 * @param $attribute_type
	 *
	 * @return string
	 */
	public static function get_field_group( $attribute_type ) {

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
			StmListingAttribute::TYPE_FILE,
		];

		$yesNo_block = [
			StmListingAttribute::TYPE_YES_NO
		];

		if ( in_array( $attribute_type, $file_block ) ) {
			return 'file-block';
		}
		if ( in_array( $attribute_type, $list_block ) ) {
			return 'list-block';
		}
		if ( in_array( $attribute_type, $extra_block ) ) {
			return 'extra-block';
		}
		if ( in_array( $attribute_type, $input_block ) ) {
			return 'input-block';
		}
		if ( in_array( $attribute_type, $yesNo_block ) ) {
			return 'yes-no-block';
		}

		switch ( $attribute_type ) {
			case StmListingAttribute::TYPE_LOCATION:
				return "map";
				break;
			case StmListingAttribute::TYPE_GALLAEY:
				return "gallaey";
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
	 * Build attribute array for builder
	 *
	 * @param $attributes
	 *
	 * @return array
	 */
	public static function build_attribute_list( $attributes ) {
		$data = [];
		$i    = 0;
		foreach ( $attributes as $attribute ) {
			$data[ $i ] = [
				"id"          => rand( 100, 999 ) . "_" . time(),
				"title"       => $attribute->title,
				"icon"        => isset( $attribute->icon ) ? $attribute->icon : '',
				"type"        => "attribute",
				"group"       => "general",
				"module"      => "element",
				"field_group" => self::get_field_group( $attribute->type ),
				"params"      => [],
			];
			$style      = $attribute->type === StmListingAttribute::TYPE_GALLAEY ? '0' : 'ulisting_style_1';

			switch ( $attribute->type ) {
				case StmListingAttribute::TYPE_LOCATION:
					$data[ $i ]['params'] = [
						"id"             => "",
						"class"          => "",
						"zoom"           => 10,
						"width"          => "100%",
						"height"         => "300px",
						"type"           => "attribute",
						"attribute"      => $attribute->name,
						"attribute_type" => $attribute->type
					];
					break;
				case StmListingAttribute::TYPE_PRICE:
					$data[ $i ]['params'] = [
						"id"             => "",
						"class"          => "",
						"attribute"      => $attribute->name,
						"attribute_type" => $attribute->type,
						"style_template" => "ulisting_style_1",
					];
					break;
				case StmListingAttribute::TYPE_ACCORDION:
					$data[ $i ] = [
						"id"          => rand( 100, 999 ) . "_" . time(),
						"title"       => $attribute->title,
						"icon"        => isset( $attribute->icon ) ? $attribute->icon : '',
						"type"        => "basic",
						"group"       => "basic",
						"module"      => "accordion",
						"field_group" => "accordion",
						"params"      => [
							'type'      => 'accordion',
							'id'        => "",
							'class'     => "",
							'attribute' => $attribute->name,
							'template'  => "style_1",
						]
					];
					break;
				default:
					$data[ $i ]['params'] = [
						"type"             => "attribute",
						"id"               => "",
						"label"            => $attribute->name,
						"class"            => "",
						"color"            => "",
						"attribute"        => $attribute->name,
						"attribute_type"   => $attribute->type,
						"background_color" => "",
						"style_template"   => $style,
					];
					break;
			}

			$i ++;
		}

		return $data;
	}

	/**
	 * @param $listing_type
	 *
	 * @return mixed
	 */
	public static function get_data_builder( $listing_type ) {
		$data      = [];
		$layout_id = get_post_meta( $listing_type->ID, 'stm_listing_single_layout' );

		if ( isset( $layout_id[0] ) and $layout = get_post_meta( $listing_type->ID, $layout_id[0] ) and isset( $layout[0] ) ) {
			$layout         = json_decode( $layout[0], true );
			$data['layout'] = [
				"id"   => $layout_id[0],
				"name" => ( isset( $layout['name'] ) ) ? $layout['name'] : ""
			];
		}

		$data['config']     = [
			"section"          => [
				"field_group" => UListingBuilder::get_section_field()
			],
			"row"              => [
				"field_group" => UListingBuilder::get_row_field()
			],
			"col"              => [
				"field_group" => UListingBuilder::get_col_field()
			],
			"basic"            => [
				"field_group" => self::get_basic_fields()
			],
			"attribute-box"    => [
				"field_group" => UListingBuilder::get_attribute_box_field_group()
			],
			"similar-listings" => [
				"field_group" => UListingBuilder::get_similar_listings_group()
			],
			"html-box"         => [
				"field_group" => UListingBuilder::get_html_box_field_group()
			],
			"featured-listing" => [
				"field_group" => UListingBuilder::get_featured_listings_group()
			],
			"short-code"       => [
				"field_group" => UListingBuilder::get_short_code_field_group()
			],
			"tabs"             => [
				"field_group" => UListingBuilder::get_tabs_field()
			],
			"accordion"        => [
				"field_group" => UListingBuilder::get_accordion_field()
			],
			"title"            => self::get_no_template_field(),
			"no-template"      => self::get_no_template_field(),
			"list-block"       => self::get_list_block_field(),
			"input-block"      => self::get_input_block_field(),
			"file-block"       => self::get_file_block_field(),
			"extra-block"      => self::get_extra_block_field(),
			"yes-no-block"     => self::get_yesNo_block_field(),

			"price"           => [
				"field_group" => [
					"style"    => [
						"name"   => "Style",
						"fields" => [
							[
								"type"  => "color",
								"label" => __( "Background color", 'ulisting' ),
								"name"  => "background_color",
							],
							[
								"type"  => "color",
								"label" => __( "Text color", 'ulisting' ),
								"name"  => "color",
							],
							[
								"type"  => "number",
								"label" => __( "Font size", 'ulisting' ),
								"name"  => "font_size",
							]
						]
					],
					"template" => [
						"name"   => "Template",
						"fields" => [
							[
								"type"  => "blog",
								"label" => __( "Style template", 'ulisting' ),
								"name"  => "style_template",
								"items" => StmListingAttribute::get_price_style_templates()
							]
						]

					],
					"advanced" => [
						"name"   => "Advanced",
						"fields" => [
							[
								"type"  => "text",
								"label" => __( "ID", 'ulisting' ),
								"name"  => "id",
							],
							[
								"type"  => "text",
								"label" => __( "Class", 'ulisting' ),
								"name"  => "class",
							],
							[
								"type"  => "margin",
								"label" => __( "Margin", 'ulisting' ),
								"name"  => "margin",
							],
							[
								"type"  => "padding",
								"label" => __( "Padding", 'ulisting' ),
								"name"  => "padding",
							]
						]
					]
				],
			],
			"gallaey"         => [
				"field_group" => [
					"style"    => [
						"name"   => "Style",
						"fields" => [
							[
								"type"  => "color",
								"label" => __( "Background color", 'ulisting' ),
								"name"  => "background_color",
							],
							[
								"type"  => "color",
								"label" => __( "Text color", 'ulisting' ),
								"name"  => "color",
							],
							[
								"type"  => "number",
								"label" => __( "Font size", 'ulisting' ),
								"name"  => "font_size",
							]
						]
					],
					"template" => [
						"name"   => "Template",
						"fields" => [
							[
								"type"  => "blog",
								"label" => __( "Style template", 'ulisting' ),
								"name"  => "style_template",
								"items" => StmListingAttribute::get_gallery_style_templates()
							]
						]
					],
					"advanced" => [
						"name"   => "Advanced",
						"fields" => [
							[
								"type"  => "text",
								"label" => __( "ID", 'ulisting' ),
								"name"  => "id",
							],
							[
								"type"  => "text",
								"label" => __( "Class", 'ulisting' ),
								"name"  => "class",
							],
							[
								"type"  => "margin",
								"label" => __( "Margin", 'ulisting' ),
								"name"  => "margin",
							],
							[
								"type"  => "padding",
								"label" => __( "Padding", 'ulisting' ),
								"name"  => "padding",
							]
						]
					]
				]
			],
			"map"             => [
				"field_group" => [
					"style"      => [
						"name"   => "Style",
						"fields" => [
							[
								"type"  => "color",
								"label" => __( "Background color", 'ulisting' ),
								"name"  => "background_color",
							],
							[
								"type"  => "color",
								"label" => __( "Text color", 'ulisting' ),
								"name"  => "color",
							],
							[
								"type"  => "number",
								"label" => __( "Font size", 'ulisting' ),
								"name"  => "font_size",
							]
						]
					],
					"map_config" => [
						"name"   => "Map",
						"fields" => [
							[
								"type"  => "text",
								"label" => __( "Zoom", 'ulisting' ),
								"name"  => "zoom",
							],
							[
								"type"  => "text",
								"label" => __( "Width", 'ulisting' ),
								"name"  => "width",
							],
							[
								"type"  => "text",
								"label" => __( "Height", 'ulisting' ),
								"name"  => "height",
							],
						]

					],
					"advanced"   => [
						"name"   => "Advanced",
						"fields" => [
							[
								"type"  => "text",
								"label" => __( "ID", 'ulisting' ),
								"name"  => "id",
							],
							[
								"type"  => "text",
								"label" => __( "Class", 'ulisting' ),
								"name"  => "class",
							],
							[
								"type"  => "margin",
								"label" => __( "Margin", 'ulisting' ),
								"name"  => "margin",
							],
							[
								"type"  => "padding",
								"label" => __( "Padding", 'ulisting' ),
								"name"  => "padding",
							]
						]
					]
				]
			],
			"category"        => [
				"field_group" => [
					"template" => [
						"name"   => "Template",
						"fields" => [
							[
								"type"  => "blog",
								"label" => __( "Style template", 'ulisting' ),
								"name"  => "template",
								"items" => StmListingItemCardLayout::get_category_template()
							]
						]

					],
					"style"    => [
						"name"   => "Style",
						"fields" => [
							[
								"type"  => "color",
								"label" => __( "Background color", 'ulisting' ),
								"name"  => "background_color",
							]
						]
					],
					"advanced" => [
						"name"   => "Advanced",
						"fields" => [
							[
								"type"  => "text",
								"label" => __( "ID", 'ulisting' ),
								"name"  => "id",
							],
							[
								"type"  => "text",
								"label" => __( "Class", 'ulisting' ),
								"name"  => "class",
							],
							[
								"type"  => "margin",
								"label" => __( "Margin", 'ulisting' ),
								"name"  => "margin",
							],
							[
								"type"  => "padding",
								"label" => __( "Padding", 'ulisting' ),
								"name"  => "padding",
							]
						]
					]
				]
			],
			"region"          => [
				"field_group" => [
					"template" => [
						"name"   => "Template",
						"fields" => [
							[
								"type"  => "blog",
								"label" => __( "Style template", 'ulisting' ),
								"name"  => "template",
								"items" => StmListingItemCardLayout::get_region_template()
							]
						]

					],
					"style"    => [
						"name"   => "Style",
						"fields" => [
							[
								"type"  => "color",
								"label" => __( "Background color", 'ulisting' ),
								"name"  => "background_color",
							]
						]
					],
					"advanced" => [
						"name"   => "Advanced",
						"fields" => [
							[
								"type"  => "text",
								"label" => __( "ID", 'ulisting' ),
								"name"  => "id",
							],
							[
								"type"  => "text",
								"label" => __( "Class", 'ulisting' ),
								"name"  => "class",
							],
							[
								"type"  => "margin",
								"label" => __( "Margin", 'ulisting' ),
								"name"  => "margin",
							],
							[
								"type"  => "padding",
								"label" => __( "Padding", 'ulisting' ),
								"name"  => "padding",
							]
						]
					]
				]
			],
			"page-statistics" => [
				"field_group" => [
					"style"    => [
						"name"   => "Style",
						"fields" => [
							[
								"type"    => "color",
								"label"   => __( "Listing background color", 'ulisting' ),
								"name"    => "listing_background_color",
								"default" => "rgba(88, 170, 228, 0.5)"
							],
							[
								"type"    => "color",
								"label"   => __( "Listing border color", 'ulisting' ),
								"name"    => "listing_border_color",
								"default" => "transparent"
							],
							[
								"type"    => "color",
								"label"   => __( "User click background color", 'ulisting' ),
								"name"    => "user_background_color",
								"default" => "rgba(73, 212, 99, 0.7)"
							],
							[
								"type"    => "color",
								"label"   => __( "User click border color", 'ulisting' ),
								"name"    => "user_border_color",
								"default" => "transparent"
							],

						]
					],
					"advanced" => [
						"name"   => "Advanced",
						"fields" => [
							[
								"type"  => "text",
								"label" => __( "ID", 'ulisting' ),
								"name"  => "id",
							],
							[
								"type"  => "text",
								"label" => __( "Class", 'ulisting' ),
								"name"  => "class",
							],
							[
								"type"  => "text",
								"label" => __( "Page statistics step", 'ulisting' ),
								"name"  => "page_statistics_step",
							],
							[
								"type"  => "margin",
								"label" => __( "Margin", 'ulisting' ),
								"name"  => "margin",
							],
							[
								"type"  => "padding",
								"label" => __( "Padding", 'ulisting' ),
								"name"  => "padding",
							]
						]
					]
				]
			],
			"accordion"       => [
				"field_group" => [
					"style"    => [
						"name"   => "Style",
						"fields" => [
							[
								"type"  => "color",
								"label" => __( "Background color", 'ulisting' ),
								"name"  => "background_color",
							],
							[
								"type"  => "color",
								"label" => __( "Border color", 'ulisting' ),
								"name"  => "border_color",
							],
						]
					],
					"advanced" => [
						"name"   => "Advanced",
						"fields" => [
							[
								"type"  => "text",
								"label" => __( "ID", 'ulisting' ),
								"name"  => "id",
							],
							[
								"type"  => "text",
								"label" => __( "Class", 'ulisting' ),
								"name"  => "class",
							],
							[
								"type"  => "margin",
								"label" => __( "Margin", 'ulisting' ),
								"name"  => "margin",
							],
							[
								"type"  => "padding",
								"label" => __( "Padding", 'ulisting' ),
								"name"  => "padding",
							]
						]
					]
				]
			],
		];
		$data['donor']      = UListingBuilder::get_donor();
		$data['elements'][] = UListingBuilder::get_inner_row();
		$data['elements'][] = UListingBuilder::get_attribute_box();
		$data['elements'][] = UListingBuilder::get_similar_listings();
		$data['elements'][] = UListingBuilder::get_featured_listing();
		$data['elements'][] = UListingBuilder::get_html_box();
		$data['elements'][] = UListingBuilder::get_short_code();
		$data['elements'][] = UListingBuilder::get_tabs_box();
		$data['elements'][] = [
			"id"          => rand( 100, 999 ) . "_" . time(),
			"title"       => "Title",
			"type"        => "attribute",
			"group"       => "general",
			"module"      => "element",
			"icon"        => "icon-text",
			"field_group" => "title",
			"params"      => [
				"type"             => "attribute",
				"id"               => "",
				"class"            => "",
				"color"            => "",
				"attribute_type"   => "title",
				"background_color" => "",
			],
		];
		$data['elements'][] = [
			"id"          => rand( 100, 999 ) . "_" . time(),
			"title"       => "Postal code",
			"type"        => "attribute",
			"group"       => "general",
			"module"      => "element",
			"icon"        => "icon-3214748",
			"field_group" => "title",
			"params"      => [
				"type"             => "attribute",
				"id"               => "",
				"class"            => "",
				"color"            => "",
				"attribute_type"   => "postal_code",
				"background_color" => "",
			],
		];
		$data['elements'][] = [
			"id"           => rand( 100, 999 ) . "_" . time(),
			"builder_type" => "item_card_layout",
			"title"        => "Category",
			"type"         => "attribute",
			"icon"         => "icon-2438114",
			"group"        => "general",
			"module"       => "element",
			"field_group"  => "category",
			"params"       => [
				"template"         => "template_1",
				"type"             => "category",
				"id"               => "",
				"class"            => "",
				"color"            => "",
				"background_color" => "",
			],
		];
		$data['elements'][] = [
			"id"           => rand( 100, 999 ) . "_" . time(),
			"builder_type" => "item_card_layout",
			"title"        => "Region",
			"type"         => "attribute",
			"group"        => "general",
			"module"       => "element",
			"icon"         => "icon-5352391",
			"field_group"  => "region",
			"params"       => [
				"template"         => "template_1",
				"type"             => "region",
				"id"               => "",
				"class"            => "",
				"color"            => "",
				"background_color" => "",
			],
		];
		$data['elements'][] = [
			"id"          => rand( 100, 999 ) . "_" . time(),
			"title"       => "Page statistics",
			"type"        => "attribute",
			"group"       => "general",
			"module"      => "element",
			"icon"        => "icon-446026",
			"field_group" => "page-statistics",
			"params"      => [
				"template_path"            => "statistics/listing-page-statistics",
				"template"                 => "none",
				"type"                     => "page-statistics",
				"id"                       => "",
				"class"                    => "",
				"listing_border_color"     => "",
				"listing_background_color" => "",
				"user_border_color"        => "",
				"user_background_color"    => "",
				"page_statistics_step"     => "10",
			],
		];

		/**
		 * Custom single modules Profile Form
		 */
		$data["elements"][] = [
			"id"          => 0,
			"title"       => "Profile Form",
			"type"        => "element",
			"group"       => "general",
			"module"      => "element",
			"field_group" => "profile_form",
			"params"      => [
				"template_path"    => "profile/profile_form/style_1",
				"template"         => "style_1",
				"type"             => "element",
				"id"               => "",
				"class"            => "",
				"color"            => "",
				"background_color" => ""
			],
		];

		$data["config"]["profile_form"] = [
			"field_group" => [
				"template" => [
					"name"   => "Template",
					"fields" => [
						[
							"type"  => "blog",
							"label" => __( "Style template", 'ulisting' ),
							"name"  => "template",
							"items" => self::single_profile_form_template()
						]
					]

				],
				"advanced" => [
					"name"   => "Advanced",
					"fields" => [
						[
							"type"  => "text",
							"label" => __( "ID", 'ulisting' ),
							"name"  => "id",
						],
						[
							"type"  => "text",
							"label" => __( "Class", 'ulisting' ),
							"name"  => "class",
						],

						[
							"type"  => "text",
							"label" => __( "ShortCode", 'ulisting' ),
							"name"  => "short_code",
						],

						[
							"type"  => "margin",
							"label" => __( "Margin", 'ulisting' ),
							"name"  => "margin",
						],
						[
							"type"  => "padding",
							"label" => __( "Padding", 'ulisting' ),
							"name"  => "padding",
						],
						[
							"type"  => "responsive-input",
							"label" => __( "Count", 'ulisting' ),
							"name"  => "count",
						],
						[
							"type"  => "responsive-position",
							"label" => __( "Position", 'ulisting' ),
							"name"  => "position",
							"items" => [
								"static"   => "Static",
								"fixed"    => "Fixed",
								"absolute" => "Absolute",
								"relative" => "Relative",
							]
						],
					]
				]
			]
		];

		$data['elements'] = array_merge( $data['elements'], StmListingSingleLayout::build_attribute_list( $listing_type->getAttribute() ) );
		$data['sections'] = ( isset( $layout['section'] ) ) ? $layout['section'] : [];

		return apply_filters( "ulisting_single_layout_builder_data", $data );
	}

	public static function single_profile_form_template() {
		$styles = [];

		$styles["style_1"] = [
			"icon" => ULISTING_URL . "/assets/img/attribute_template_style_1.png",
			"name" => "Style 1",
		];
		$styles["style_2"] = [
			"icon" => ULISTING_URL . "/assets/img/attribute_template_style_2.png",
			"name" => "Style 2",
		];

		return $styles;
	}

	/**
	 * Activate current layout for this ListingType
	 */
	public static function uListing_active_single_template() {
		$result = [
			'message' => __( 'Access denied', 'ulisting' ),
			'success' => false,
			'status'  => 'error',
		];

		StmVerifyNonce::verifyNonce( sanitize_text_field( $_POST['nonce'] ), 'ulisting-ajax-nonce' );
		$post_id   = isset( $_POST['listing_type_id'] ) ? sanitize_text_field( $_POST['listing_type_id'] ) : null;
		$layout_id = isset( $_POST['layout_id'] ) ? sanitize_text_field( $_POST['layout_id'] ) : null;

		if ( current_user_can( 'manage_options' ) && ! empty( $layout_id ) && $listingType = StmListingType::find_one( $post_id ) ) {
			update_post_meta( $post_id, 'stm_listing_single_layout', sanitize_key( $layout_id ) );
			$result['success'] = true;
			$result['message'] = __( 'Layout activated successfully', 'ulisting' );
			$result['status']  = 'success';
		}
		wp_send_json( $result );
	}

	/**
	 * Save Layout for listing single page
	 */
	public static function save_layout() {
		$result = [
			'success' => false
		];

		$request_body = file_get_contents( 'php://input' );
		$request_data = json_decode( $request_body, true );

		$sections = [];
		if ( isset( $request_data['sections'] ) ) {
			$sections = str_replace( '\\"', "'", ulisting_json_encode( $request_data['sections'] ) );
			$sections = json_decode( $sections, true );
		}
		if ( isset( $request_data['listing_type_id'] ) and $listing_type = StmListingType::find_one( (int) sanitize_text_field( $request_data['listing_type_id'] ) ) ) {
			update_post_meta(
				$listing_type->ID,
				sanitize_text_field( $request_data['id'] ),
				ulisting_json_encode( [ "name" => $request_data['name'], "section" => $sections ] )
			);
			$result['success'] = true;
			$result['data']    = $request_data;
			$style             = UListingBuilder::generation_style( $request_data['sections'] );
			UListingBuilder::generation_css( $request_data['id'], $style );
			$listing_type->save_builder_element( 'ulisting_' . $request_data['id'] . '_element_data', $request_data['sections'] );
		}

		return $result;
	}

	/**
	 * @param $type_id
	 *
	 * @return array
	 */
	public static function get_layout_list( $type_id = null ) {
		$result = [
			'success' => false
		];

		$id = null;
		if ( ! empty( $type_id ) ) {
			$id = $type_id;
		} else {
			$id = isset( $_GET['listing_type_id'] ) ? sanitize_text_field( $_GET['listing_type_id'] ) : null;
		}

		if ( ! is_null( $id ) ) {
			global $wpdb;
			$listing_type_id = (int) $id;
			$layouts         = $wpdb->get_results(
				"
			    SELECT * 
			    FROM {$wpdb->prefix}postmeta 
			    WHERE post_id = " . $listing_type_id . " AND meta_key LIKE 'ulisting_single_page_layout_%'
		    ",
				ARRAY_N
			);

			foreach ( $layouts as $layout ) {
				$value = json_decode( $layout[3], true );
				$image = get_option( $layout[2] . '_image', '' );

				$result['layouts'][] = [
					"image" => ! empty( $image ) ? $image : '',
					"id"    => $layout[2],
					"name"  => isset( $value['name'] ) ? $value['name'] : ''
				];
			}
			$result['success'] = true;
		}

		return $result;
	}

	/**
	 * @return array
	 */
	public static function get_layout_delete() {
		$result = [
			'message' => __( 'Access denied', 'ulisting' ),
			'success' => false,
			'status'  => 'error',
		];
        $listing_type = StmListingType::find_one( sanitize_text_field( $_POST['id'] ) );
		if ( current_user_can( 'manage_options' ) && isset( $_POST['id'] ) && $listing_type && isset( $_POST['nonce'] ) ) {
			StmVerifyNonce::verifyNonce( sanitize_text_field( $_POST['nonce'] ), 'ulisting-ajax-nonce' );
			delete_post_meta( $listing_type->ID, sanitize_text_field( $_POST['layout_id'] ) );
			$result['success'] = true;
			$result['message'] = __( 'Layout deleted successfully', 'ulisting' );
			$result['status']  = 'success';
		}

		wp_send_json( $result );

		return $result;
	}

	public static function get_layout() {
		$result = [
			'message' => __( 'Access denied', 'ulisting' ),
			'success' => false,
			'status'  => 'error',
			'data'    => [],
		];

		StmVerifyNonce::verifyNonce( sanitize_text_field( $_POST['nonce'] ), 'ulisting-ajax-nonce' );
		$post_id   = isset( $_POST['listing_type_id'] ) ? sanitize_text_field( $_POST['listing_type_id'] ) : null;
		$layout_id = isset( $_POST['layout_id'] ) ? sanitize_text_field( $_POST['layout_id'] ) : null;

		if ( ! empty( $post_id ) and $listing_type = StmListingType::find_one( $post_id ) ) {
			$layout = get_post_meta( $listing_type->ID, $layout_id );
			$data   = json_decode( $layout[0], true );
			if ( isset( $layout[0] ) ) {
				$result['data']    = ( is_array( $data ) and is_array( $data['section'] ) ) ? $data : [
					'name'    => $data['name'],
					'section' => []
				];
				$result['success'] = true;
				$result['message'] = __( 'Layout got successfully', 'ulisting' );
				$result['status']  = 'success';
			}
		}

		wp_send_json( $result );
	}

	public static function uListing_import_layout() {
		$result = [
			'message' => __( 'Access denied', 'ulisting' ),
			'success' => false,
			'status'  => 'error',
			'layouts' => [],
		];


		if ( current_user_can( 'manage_options' ) && isset( $_POST['nonce'] ) ) {
			StmVerifyNonce::verifyNonce( sanitize_text_field( $_POST['nonce'] ), 'ulisting-ajax-nonce' );
			$layouts = [];
			$files   = apply_filters( 'ulisting_sanitize_array', $_FILES );

			if ( ! empty( $files['file'] ) && ! empty( $_POST['id'] ) && isset( $_POST['type'] ) && file_exists( $files['file']['tmp_name'] ) ) {
				$content   = file_get_contents( $files['file']['tmp_name'] );
				$layout_id = sanitize_text_field( $_POST['id'] );

				if ( is_array( $content ) ) {
					$content = ulisting__sanitize_array( $content );
				} else {
					$content = sanitize_text_field( $content );
				}
                $listing_type = StmListingType::find_one( sanitize_text_field( $_POST['listing_type_id'] ) );
				if ( isset( $_POST['listing_type_id'] ) && $_POST['type'] === 'single' && $listing_type
				                        && 'ulisting_single_page_layout' === substr( $layout_id, 0, 27 )
				) {
					update_post_meta( $listing_type->ID, $layout_id, apply_filters( 'uListing-sanitize-data', $content ) );
					$layouts = self::get_layout_list( $listing_type->ID );
					$layouts = isset( $layouts['layouts'] ) ? $layouts['layouts'] : [];
				} elseif ( 'ulisting_type_page_layout' === substr( $layout_id, 0, 25 ) && 'inventory' === sanitize_text_field( $_POST['type'] ) ) {
					update_option( $layout_id, apply_filters( 'uListing-sanitize-data', $content ) );
					$layouts = StmInventoryLayout::get_layout_list();
					$layouts = isset( $layouts['layouts'] ) ? $layouts['layouts'] : [];
				}
			}

			$result['success'] = true;
			$result['layouts'] = $layouts;
			$result['message'] = __( 'Layouts Imported successfully', 'ulisting' );
			$result['status']  = 'success';
		}

		wp_send_json( $result );
	}

	public static function uListing_save_single_layout() {
		$result = [
			'message' => __( 'Access denied', 'ulisting' ),
			'success' => false,
			'status'  => 'error',
			'data'    => [],
		];

		$request_body = file_get_contents( 'php://input' );
		$request_data = json_decode( $request_body, true );

		$post_id   = isset( $request_data['listing_type_id'] ) ? (int) sanitize_text_field( $request_data['listing_type_id'] ) : null;
		$layout_id = isset( $request_data['layout_id'] ) ? sanitize_text_field( $request_data['layout_id'] ) : null;
		$name      = isset( $request_data['layout_name'] ) ? sanitize_text_field( $request_data['layout_name'] ) : null;
		$section   = isset( $request_data['sections'] ) ? apply_filters( 'uListing-sanitize-data', $request_data['sections'] ) : null;
		StmVerifyNonce::verifyNonce( sanitize_text_field( $request_data['nonce'] ), 'ulisting-ajax-nonce' );

		if ( ! empty( $section ) ) {
			$section = str_replace( '\\"', "'", ulisting_json_encode( $section ) );
			$section = json_decode( $section, true );
		}

		ulisting_write_log( $layout_id );
		if ( ! empty( $post_id ) and $listing_type = StmListingType::find_one( $post_id ) ) {
			update_post_meta(
				$listing_type->ID,
				sanitize_text_field( $layout_id ),
				ulisting_json_encode( [ "name" => $name, "section" => $section ] )
			);

			$result['data'] = self::get_layout_list( $listing_type->ID );;
			$result['success'] = true;
			$result['message'] = __( 'Layout saved successfully', 'ulisting' );
			$result['status']  = 'success';
			$style             = UListingBuilder::generation_style( $request_data['sections'] );
			ulisting_write_log( $style );
			UListingBuilder::generation_css( $layout_id, $style, $post_id );
			$listing_type->save_builder_element( 'ulisting_' . $layout_id . '_element_data', $request_data['sections'] );
		}

		wp_send_json( $result );
	}


	/**
	 * @return array data for builder
	 */
	public static function get_builder_data() {
		$result = [
			'success' => false
		];

		$request_body = file_get_contents( 'php://input' );
		$request_data = json_decode( $request_body, true );

		if ( isset( $request_data['listing_type_id'] ) and $listingType = \uListing\Classes\StmListingType::find_one( (int) ( sanitize_text_field( $request_data['listing_type_id'] ) ) ) ) {
			$result['data']    = self::get_data_builder( $listingType );
			$result['success'] = true;
		}

		return $result;
	}

	/**
	 * @param $element
	 *
	 * @return string
	 */
	public static function get_element_template( $element ) {
		if ( isset( $element['type'] ) and isset( $element['params'] ) and isset( $element['params']['attribute_type'] ) ) {
			return 'builder/' . $element['type'] . '/' . $element['params']['attribute_type'];
		}

		return null;
	}
}