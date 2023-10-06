<?php

namespace uListing\Classes;

use uListing\Classes\Vendor\StmBaseModel;
use uListing\Classes\Vendor\ArrayHelper;
use uListing\Classes\StmVerifyNonce;

class StmListingAttribute extends StmBaseModel {

	const TYPE_TEXT = 'text';
	const TYPE_TEXT_AREA = 'text_area';
	const TYPE_WP_EDITOR = 'wp_editor';
	const TYPE_NUMBER = 'number';
	const TYPE_DATE = 'date';
	const TYPE_TIME = 'time';
	const TYPE_SELECT = 'select';
	const TYPE_MULTISELECT = 'multiselect';
	const TYPE_CHECKBOX = 'checkbox';
	const TYPE_RADIO_BUTTON = 'radio_button';
	const TYPE_YES_NO = 'yes_no';
	const TYPE_FILE = 'file';
	const TYPE_GALLAEY = 'gallery';
	const TYPE_PRICE = 'price';
	const TYPE_LOCATION = 'location';
	const TYPE_ACCORDION = 'accordion';
	const TYPE_VIDEO = 'video';

	protected $fillable = [
		'id',
		'title',
		'name',
		'type',
		'affix',
		'thumbnail_id',
		'icon'
	];

	public $id;
	public $title;
	public $name;
	public $type;
	public $affix;
	public $icon;
	public $thumbnail_id;

	public static function get_primary_key() {
		return 'id';
	}

	public static function get_table() {
		global $wpdb;

		return $wpdb->prefix . 'ulisting_attribute';
	}

	public static function get_searchable_fields() {
		return [
			'id',
			'title',
			'name',
			'type',
			'affix'
		];
	}

	public static function init() {

	}

	/**
	 * @param null $type
	 *
	 * @return array|mixed
	 */
	public static function getType( $type = null ) {
		$array = array(
			StmListingAttribute::TYPE_TEXT         => __( 'Text', "ulisting" ),
			StmListingAttribute::TYPE_TEXT_AREA    => __( 'Textarea', "ulisting" ),
			StmListingAttribute::TYPE_WP_EDITOR    => __( 'WP editor', "ulisting" ),
			StmListingAttribute::TYPE_NUMBER       => __( 'Number', "ulisting" ),
			StmListingAttribute::TYPE_DATE         => __( 'Date', "ulisting" ),
			StmListingAttribute::TYPE_TIME         => __( 'Time', "ulisting" ),
			StmListingAttribute::TYPE_SELECT       => __( 'Select', "ulisting" ),
			StmListingAttribute::TYPE_MULTISELECT  => __( 'Multiselect', "ulisting" ),
			StmListingAttribute::TYPE_CHECKBOX     => __( 'Checkbox', "ulisting" ),
			StmListingAttribute::TYPE_RADIO_BUTTON => __( 'Radio button', "ulisting" ),
			StmListingAttribute::TYPE_YES_NO       => __( 'Yes/No', "ulisting" ),
			StmListingAttribute::TYPE_FILE         => __( 'File', "ulisting" ),
			StmListingAttribute::TYPE_GALLAEY      => __( 'Gallery', "ulisting" ),
			StmListingAttribute::TYPE_PRICE        => __( 'Price', "ulisting" ),
			StmListingAttribute::TYPE_LOCATION     => __( 'Location', "ulisting" ),
			StmListingAttribute::TYPE_ACCORDION    => __( 'Accordion', "ulisting" ),
			StmListingAttribute::TYPE_VIDEO        => __( 'Video', "ulisting" )
		);

		if ( $type == null ) {
			return $array;
		} else {
			return $array[ $type ];
		}
	}

	/**
	 * @param $id
	 *
	 * @return null|string
	 */
	public function getOptionById( $id ) {
		$options = $this->getOptions();
		$result  = null;
		if ( ! empty( $options ) ) {
			foreach ( $options as $option ) {
				if ( $option->term_id === intval( $id ) ) {
					$result = $option->name;
				}
			}
		}

		return $result;
	}

	public function getOptions() {
		$options  = [];
		$_options = get_terms( array(
			'taxonomy'     => 'listing-attribute-options',
			'hide_empty'   => false,
			'attribute_id' => $this->id
		) );

		foreach ( $_options as $option ) {
			$options[] = ( new StmListingAttributeOption )->loadData( $option );
		}

		return $options;
	}

	public function getOptionsListData() {
		return ArrayHelper::map(
			$this->getOptions(),
			'term_id',
			'name'
		);
	}

	public function getOptionsForType( $type = null ) {
		$options = [];
		foreach ( $this->getOptionsListData() as $key => $val ) {
			$options[] = [
				"id"   => $key,
				"text" => $val,
			];
		}

		return $options;
	}

	public function isOptions() {
		return self::is_options( $this->type );
	}

	public static function is_options( $type ) {
		switch ( $type ) {
			case self::TYPE_SELECT:
				return true;
				break;
			case self::TYPE_MULTISELECT:
				return true;
				break;
			case self::TYPE_RADIO_BUTTON:
				return true;
				break;
			case self::TYPE_CHECKBOX:
				return true;
				break;
			default:
				return false;
		}
	}

	/**
	 *  Run after saves model
	 */
	public function after_save() {
		if ( isset( $_POST['StmListingAttribute'] ) ) {
			$data          = apply_filters( 'ulisting_sanitize_array', $_POST['StmListingAttribute'] );
			$listing_types = isset( $data['listing_type'] ) ? $data['listing_type'] : [];

			foreach ( StmListingType::getDataList() as $key => $val ) {
				$listing_type_attributes = get_post_meta( $key, 'listing_type_attribute', true ) ?: [];

				if ( is_array( $listing_type_attributes ) && in_array( $key, $listing_types ) ) {
					if ( ! in_array( $this->{static::get_primary_key()}, $listing_type_attributes ) ) {
						array_push( $listing_type_attributes, $this->{static::get_primary_key()} );
						update_post_meta( $key, 'listing_type_attribute', apply_filters( 'uListing-sanitize-data', $listing_type_attributes ) );
					}
				} else {
					$index = array_search( $this->{static::get_primary_key()}, $listing_type_attributes );
					if ( isset( $listing_type_attributes[ $index ] ) && $index != false ) {
						unset( $listing_type_attributes[ $index ] );
						update_post_meta( $key, 'listing_type_attribute', apply_filters( 'uListing-sanitize-data', $listing_type_attributes ) );
					}
				}
			}
		}
	}

	public static function ajaxActionCreate() {

		$result = [
			'success' => false,
			'result'  => false,
			'status'  => 'error',
			'message' => __( 'Access denied', 'ulisting' ),
		];

		$status_code = null;
		StmVerifyNonce::verifyNonce( sanitize_text_field( $_POST['wpnonce'] ), 'stm_attribute_ajax_create' );

		if ( current_user_can( 'manage_options' ) ) {
			$_POST['StmListingAttribute']['title'] = StmListingAttribute::deslash( sanitize_text_field( $_POST['StmListingAttribute']['title'] ) );
			$model                                 = StmListingAttribute::create( apply_filters( 'ulisting_sanitize_array', $_POST['StmListingAttribute'] ) )->save();

			if ( $model ) {
				$status_code = 200;
				$result      = self::created_status( $model );
			}
		}

		wp_send_json( $result, $status_code );
	}

	public static function listingTypeAttrCreate() {

		$result = [
			'success' => false,
			'status'  => 'error',
			'message' => __( 'Access denied', 'ulisting' ),
		];

		$data        = ulisting_sanitize_array( $_POST );
		$status_code = null;

		StmVerifyNonce::verifyNonce( sanitize_text_field( $data['wpnonce'] ), 'ulisting-ajax-nonce' );
		if ( current_user_can( 'manage_options' ) ) {
			$data['title'] = StmListingAttribute::deslash( sanitize_text_field( $data['title'] ) );
			$model         = StmListingAttribute::create( apply_filters( 'ulisting_sanitize_array', $data ) )->save();

			if ( $model ) {
				$status_code = 200;
				$result      = self::created_status( $model );
			}
		}

		wp_send_json( $result, $status_code );
	}

	public static function listingTypeAttrUpdate() {
		$result = [
			'success' => false,
			'status'  => 'error',
			'message' => __( 'Access denied', 'ulisting' ),
		];

		$data = ulisting_sanitize_array( $_POST );
		StmVerifyNonce::verifyNonce( sanitize_text_field( $data['wpnonce'] ), 'ulisting-ajax-nonce' );

		if ( current_user_can( 'manage_options' ) ) {
			$data['title'] = StmListingAttribute::deslash( sanitize_text_field( $data['title'] ) );
			$model         = StmListingAttribute::find_one( $data['id'] );

			if ( ! empty( $model ) ) {
				$model->title = apply_filters( 'uListing-sanitize-data', $data['title'] );
				$model->name  = apply_filters( 'uListing-sanitize-data', $data['name'] );
				$model->type  = apply_filters( 'uListing-sanitize-data', $data['type'] );
				$model->icon  = isset( $data['icon'] ) ? apply_filters( 'uListing-sanitize-data', $data['icon'] ) : '';

				$model->thumbnail_id = isset( $data['image'] ) ? apply_filters( 'uListing-sanitize-data', $data['image'] ) : null;
				$model->save();
				$result = self::created_status( $model );
			}
		}

		wp_send_json( $result );
	}

	public static function created_status( $model ) {
		return [
			'success'    => true,
			'result'     => false,
			'status'     => 'success',
			'icon'       => isset( $model->icon ) ? $model->icon : '',
			'image'      => isset( $model->thumbnail_id ) ? $model->thumbnail_id : null,
			'id'         => $model->id,
			'name'       => $model->name,
			'title'      => $model->title,
			'message'    => __( 'Custom field created successfully', 'ulisting' ),
			'is_options' => $model->isOptions(),
		];
	}

	public static function deleteAttribute() {
		$result = [
			'success' => false,
			'status'  => 'success',
			'message' => __( 'Access denied', 'ulisting' )
		];


		if ( current_user_can( 'manage_options' ) && isset( $_POST['id'] ) && isset( $_POST['wpnonce'] ) ) {
			StmVerifyNonce::verifyNonce( sanitize_text_field( $_POST['wpnonce'] ), 'ulisting-ajax-nonce' );
			self::query()
			    ->where( 'id', sanitize_text_field( $_POST['id'] ) )
			    ->delete();

			$result['success'] = true;
			$result['status']  = 'success';
			$result['message'] = __( 'Field Removed Successfully', 'ulisting' );
		}

		wp_send_json( $result );
	}

	public static function getDateTypeField() {
		return [
			'exact' => __( 'Exact Date' ),
			'range' => __( 'Date Range' ),
		];
	}

	public static function getOrderByList() {
		return [
			'name'  => __( 'Slug' ),
			'count' => __( 'Count' ),
		];
	}

	public static function getOrderList() {
		return [
			'ASC'  => __( 'Ascending' ),
			'DESC' => __( 'Descending' )
		];
	}

	public static function getUnits() {
		return [
			'kilometers' => __( 'km' ),
			'miles'      => __( 'mi' )
		];
	}

	public static function getLayoutElements() {
		return array(
			array(
				'type'        => 'filter',
				'name'        => 'Filter',
				'config_open' => false
			),
		);
	}

	/**
	 * @param string $size
	 *
	 * @return array|false
	 */
	public function getIcon( $size = 'thumbnail' ) {
		if ( $this->icon ) {
			return "<i class='" . $this->icon . "'></i>";
		}

		return wp_get_attachment_image( $this->thumbnail_id, $size );
	}

	/*----------------------------------------------------- NEW -------------------------------------------------------*/

	/**
	 * @param StmListing $listing
	 *
	 * @return array|int|null|object
	 */
	public function getValueForListing( StmListing $listing ) {
		global $wpdb;
		$model = StmListingAttributeRelationships::query()
		                                         ->select( 't.*, stm_a.title as attribute_title, attribute_option.name as option_name' )
		                                         ->asTable( 't' )
		                                         ->join( "LEFT JOIN " . StmListingAttribute::get_table() . " as stm_a on stm_a.name = t.attribute " )
		                                         ->join( "LEFT JOIN " . StmListingAttributeOption::get_table() . " as  attribute_option on attribute_option.`term_id` = t.`value`" )
		                                         ->where( 't.listing_id', $listing->ID );
		if ( $this->type == StmListingAttribute::TYPE_LOCATION ) {
			return $model->where_in( 't.attribute', [ 'address', 'latitude', 'longitude', 'postal_code' ] )->find();
		}

		return $model->where_in( 't.attribute', [ $this->name ] )->find();
	}

	/**
	 * @return array
	 */
	protected static function basic_template() {
		$style_templates = [
			0                  => [
				"icon"               => ULISTING_URL . "/assets/img/none.png",
				"name"               => "None",
				"attribute_template" => "",
			],
			"ulisting_style_1" => [
				"icon"               => ULISTING_URL . "/assets/img/attribute-icon.png",
				"name"               => "Style 1",
				"attribute_template" => "<div class='ulisting-attribute-template attribute_[attribute_type]'> <span class='ulisting-attribute-template-icon'>[attribute_icon]</span> [attribute_value] [sub_title]</div> ",
			],
			"ulisting_style_2" => [
				"icon"               => ULISTING_URL . "/assets/img/option-Icon.png",
				"name"               => "Style 2",
				"attribute_template" => "<div class='ulisting-attribute-template attribute_[attribute_type]'> <span class='ulisting-attribute-template-icon'>[attribute_option_icon]</span> [attribute_value] [sub_title]</div> ",
			],
			"ulisting_style_3" => [
				"icon"               => ULISTING_URL . "/assets/img/attribute-multiselect.png",
				"name"               => "Style 3",
				"attribute_template" => "<div class='ulisting-attribute-template attribute_[attribute_type]'>  <span class='ulisting-attribute-template-name'>[attribute_name] </span>  <ul> [option_items] </ul> </div> ",
				"option_template"    => "<li>  <span class='ulisting-attribute-template-icon'> [attribute_option_icon] </span> <span class='ulisting-attribute-template-value'> [attribute_value] </span>  </li>",
			],
		];

		return $style_templates;
	}

	/**
	 * @return mixed|void
	 */
	public static function get_input_block_style_templates() {
		$style_templates = self::basic_template();

		return apply_filters( "ulisting_input_block_style_templates", $style_templates );
	}

	/**
	 * @return mixed|void
	 */
	public static function get_file_block_style_templates() {
		$style_templates = [
			0                  => [
				"icon"               => ULISTING_URL . "/assets/img/none.png",
				"name"               => "None",
				"attribute_template" => "",
			],
			"ulisting_style_1" => [
				"icon"               => ULISTING_URL . "/assets/img/attribute_value.jpg",
				"name"               => "Style 1",
				"attribute_template" => "<a class='btn btn-primary' href='[attribute_value]' download>[label]</a>",
			],
		];

		return apply_filters( "ulisting_file_block_style_templates", $style_templates );
	}

	/**
	 * @return mixed|void
	 */
	public static function get_yesNo_block_style_templates() {
		$style_templates = [
			0                  => [
				"icon"               => ULISTING_URL . "/assets/img/none.png",
				"name"               => "None",
				"attribute_template" => "",
			],
			"ulisting_style_1" => [
				"icon"               => ULISTING_URL . "/assets/img/attribute_value.jpg",
				"name"               => "Style 1",
				"attribute_template" => "<div class='ulisting-attribute-template attribute_[attribute_type]'> <span class='ulisting-attribute-template-icon'>[attribute_option_icon]</span> [attribute_name] - [attribute_value]</div>",
			],
		];

		return apply_filters( "ulisting_yes_no_block_style_templates", $style_templates );
	}

	/**
	 * @return mixed|void
	 */
	public static function get_extra_block_style_templates() {
		$style_templates = self::basic_template();

		return apply_filters( "ulisting_extra_block_style_templates", $style_templates );
	}

	/**
	 * @return mixed|void
	 */
	public static function get_style_templates() {
		/*
		 *  [attribute_name]
		 *  [attribute_icon]
		 *  [attribute_option_icon]
		 *  [attribute_value]
		 *  [sub_title]
		 *  [option_items]
		 *  [attribute_value]
		 *
		 */

		$style_templates = self::basic_template();;

		return apply_filters( "ulisting_attribute_style_templates", $style_templates );
	}

	/**
	 * @return mixed|void
	 */
	public static function get_price_style_templates() {
		$style_templates = [
			"ulisting_style_1" => [
				"icon"               => ULISTING_URL . "/assets/img/price.png",
				"name"               => "Style 1",
				"attribute_template" => "<div class='ulisting-listing-price'> [old_price] [price]</div>",
				"price_template"     => "<span class='ulisting-listing-price-new'>[price] [suffix]</span>",
				"old_price_template" => "<span class='ulisting-listing-price-old'>[old_price] [suffix]</span>",
			]

		];

		return apply_filters( "ulisting_attribute_price_style_templates", $style_templates );
	}

	/**
	 * @return mixed|void
	 */
	public static function get_gallery_style_templates() {
		$style_templates = [
			0 => [
				"icon" => ULISTING_URL . "/assets/img/none.png",
				"name" => "None",
			]
		];

		return apply_filters( "ulisting_attribute_gallery_style_templates", $style_templates );
	}

	/**
	 * @param StmListing $listing
	 * @param $element
	 *
	 * @return mixed|null|string
	 */
	public static function render_attribute( StmListing $listing, $element ) {
		if ( isset( $listing->attribute_elements ) ) {
			return self::_render_attribute( $listing, $element );
		}

		$attribute_options_icon = [];
		if ( ! isset( $element['params']['attribute'] ) ) {
			return null;
		}

		if ( ! ( $attribute = StmListingAttribute::query()->where( 'name', $element['params']['attribute'] )->findOne() ) ) {
			return null;
		}

		$style_templates   = self::get_style_templates();
		$attribute_icon    = $attribute->getIcon();
		$attribute_options = $attribute->getOptions();
		$value             = $listing->getAttributeValue( $attribute );

		// if attribute type price
		if ( $attribute->type == StmListingAttribute::TYPE_PRICE ) {
			return self::render_price( $attribute, $element, $value );
		}

		foreach ( $attribute_options as $attribute_option ) {
			if ( isset( $value[ $attribute_option->term_id ] ) ) {
				$attribute_options_icon[ $attribute_option->term_id ] = $attribute_option->getIcon();
			}
		}

		$style_template_id = $element['params']['style_template'];

		$attribute_template = ( isset( $style_templates[ $style_template_id ]['attribute_template'] ) ) ? $style_templates[ $style_template_id ]['attribute_template'] : '';
		$option_template    = ( isset( $style_templates[ $style_template_id ]['option_template'] ) ) ? $style_templates[ $style_template_id ]['option_template'] : '';

		$additional_class   = str_replace( ' ', '_', strtolower( $attribute->title ) );
		$attribute_template = str_replace( "[attribute_type]", $additional_class, $attribute_template );
		$attribute_template = str_replace( "[attribute_name]", $attribute->title, $attribute_template );
		$attribute_template = str_replace( "[sub_title]", $attribute->affix, $attribute_template );
		$attribute_template = str_replace( "[attribute_icon]", $attribute_icon, $attribute_template );

		if ( is_array( $attribute_options_icon ) ) {
			$_attribute_options_icon = implode( " ", $attribute_options_icon );
		}
		$attribute_template = str_replace( "[attribute_option_icon]", $_attribute_options_icon, $attribute_template );

		if ( is_array( $value ) ) {
			$attribute_template = str_replace( "[attribute_value]", implode( ", ", $value ), $attribute_template );
		} else {
			$attribute_template = str_replace( "[attribute_value]", $value, $attribute_template );
		}

		$option_items_content = '';

		foreach ( $attribute_options as $attribute_option ) {
			$_option_template = $option_template;

			if ( isset( $attribute_options_icon[ $attribute_option->term_id ] ) or isset( $value[ $attribute_option->term_id ] ) ) {
				$_option_template     = str_replace( "[attribute_option_icon]", $attribute_options_icon[ $attribute_option->term_id ], $_option_template );
				$_option_template     = str_replace( "[attribute_value]", $value[ $attribute_option->term_id ], $_option_template );
				$option_items_content .= $_option_template;
			}
		}

		$attribute_template = str_replace( "[option_items]", $option_items_content, $attribute_template );

		return $attribute_template;
	}

	public static function attribute_array_def_val( $listing, $attribute_elements, $element ) {
		$not_check_attributes = [
			self::TYPE_PRICE,
			self::TYPE_GALLAEY,
			self::TYPE_TEXT_AREA,
			self::TYPE_WP_EDITOR,
			self::TYPE_CHECKBOX,
			self::TYPE_FILE,
			self::TYPE_VIDEO,
			self::TYPE_RADIO_BUTTON,
			self::TYPE_YES_NO,
			self::TYPE_LOCATION,
			self::TYPE_MULTISELECT
		];
		$attribute_element    = ( isset( $listing->attribute_elements[ $element['params']['attribute'] ] ) ) ? $listing->attribute_elements[ $element['params']['attribute'] ] : null;
		$attribute_keys_val   = StmListingAttribute::query()->where( 'name', $element['params']['attribute'] )->findOne();
		switch ( $element['params']['attribute_type'] ) {
			case self::TYPE_MULTISELECT:
			case self::TYPE_CHECKBOX:
			case self::TYPE_RADIO_BUTTON:
				if ( empty( $attribute_element ) ) {
					$attribute_element['attribute_title']                           = $attribute_keys_val->title;
					$attribute_element['options'][0]['attribute_option_name']       = __( "N/A", "ulisting" );
					$attribute_element['attribute_icon']                            = ( $element['params']['attribute_type'] == self::TYPE_RADIO_BUTTON ) ? $attribute_keys_val->icon : null;
					$listing->attribute_elements[ $element['params']['attribute'] ] = $attribute_element;
				}
				break;
		}
		foreach ( $attribute_elements as $key => $attribute_element_val ) {
			$attribute_keys_val = StmListingAttribute::query()->where( 'name', $key )->findOne();
			if ( isset( $attribute_keys_val->type ) && ! in_array( $attribute_keys_val->type, $not_check_attributes ) ) {
				if ( empty( $attribute_element_val ) ) {
					$listing->attribute_elements[ $key ] = [
						'attribute_title' => isset( $attribute_keys_val->title ) ? $attribute_keys_val->title : '',
						'attribute_affix' => '',
						'attribute_icon'  => isset( $attribute_keys_val->icon ) ? $attribute_keys_val->icon : '',
						'attribute_value' => null
					];
				} elseif ( isset( $attribute_element_val ) && ( empty( $attribute_element_val['attribute_value'] ) ) ) {
					$listing->attribute_elements[ $key ] = [
						'attribute_title' => isset( $attribute_keys_val->title ) ? $attribute_keys_val->title : '',
						'attribute_affix' => '',
						'attribute_icon'  => isset( $attribute_keys_val->icon ) ? $attribute_keys_val->icon : '',
						'attribute_value' => null
					];
				}
			}
		}

		return $listing;
	}

	public static function set_na_value_attributes( $value, $element ) {
		$not_check_attributes = [
			self::TYPE_PRICE,
			self::TYPE_GALLAEY,
			self::TYPE_TEXT_AREA,
			self::TYPE_WP_EDITOR,
			self::TYPE_CHECKBOX,
			self::TYPE_FILE,
			self::TYPE_VIDEO,
			self::TYPE_RADIO_BUTTON,
			self::TYPE_YES_NO,
			self::TYPE_LOCATION,
			self::TYPE_MULTISELECT
		];
		if ( ! in_array( $element['params']['attribute_type'], $not_check_attributes ) ) {
			if ( empty( $value ) ) {
				$value = __( "N/A", "ulisting" );
			}
		}

		return $value;
	}

	/**
	 * @param StmListing $listing
	 * @param $element
	 *
	 * @return mixed|string
	 */
	public static function _render_attribute( StmListing $listing, $element ) {
		if ( ! isset( $element['params']['attribute'] ) or ! isset( $listing->attribute_elements ) and ! isset( $listing->attribute_elements[ $element['params']['attribute'] ] ) ) {
			return null;
		}
		$listing           = self::attribute_array_def_val( $listing, $listing->attribute_elements, $element );
		$attribute_element = ( isset( $listing->attribute_elements[ $element['params']['attribute'] ] ) ) ? $listing->attribute_elements[ $element['params']['attribute'] ] : null;
		error_log( print_r( $element, true ) );
		if ( empty( $attribute_element ) ) {
			return null;
		}

		if ( isset( $attribute_element['attribute_type'] ) and $attribute_element['attribute_type'] == StmListingAttribute::TYPE_PRICE ) {
			return self::_render_price( $attribute_element, $element );
		}

		$attribute_options_icon = [];
		$type                   = isset( $element['field_group'] ) ? $element['field_group'] : '';

		switch ( $type ) {
			case 'input-block':
				$style_templates = self::get_input_block_style_templates();
				break;
			case 'file-block':
				$style_templates = self::get_file_block_style_templates();
				break;
			case 'extra-block':
				$style_templates = self::get_extra_block_style_templates();
				break;
			case 'yes-no-block':
				$style_templates = self::get_yesNo_block_style_templates();
				break;
			default:
				$style_templates = self::get_style_templates();
				break;
		}

		if ( isset( $attribute_element['attribute_icon'] ) and ! empty( $attribute_element['attribute_icon'] ) ) {
			$attribute_icon = "<i class='" . $attribute_element['attribute_icon'] . "'></i>";
		} else if ( isset( $attribute_element['attribute_thumbnail_id'] ) ) {
			$attribute_icon = wp_get_attachment_image( $attribute_element['attribute_thumbnail_id'] );
		} else {
			$attribute_icon = null;
		}

		$attribute_options = ( isset( $attribute_element['options'] ) ) ? $attribute_element['options'] : [];

		foreach ( $attribute_options as $attribute_option ) {
			if ( isset( $attribute_option['id'] ) ) {
				$attribute_options_icon[ $attribute_option['id'] ] = ( ! empty( $attribute_option['attribute_option_icon'] ) ) ? "<i class='" . $attribute_option['attribute_option_icon'] . "'></i>" : wp_get_attachment_image( $attribute_option['attribute_option_thumbnail'] );
			}
		}

		if ( isset( $attribute_element['attribute_type'] ) && StmListingAttribute::is_options( $attribute_element['attribute_type'] ) ) {
			$value = ( isset( $attribute_element['attribute_option_name'] ) ) ? $attribute_element['attribute_option_name'] : null;
		} else {
			$value = ( isset( $attribute_element['attribute_value'] ) ) ? $attribute_element['attribute_value'] : null;
		}

		if ( ! empty( $attribute_options ) ) {
			foreach ( $attribute_options as $option ) {
				if ( isset( $option['attribute_option_name'] ) ) {
					$value[] = $option['attribute_option_name'];
				}
			}
		}

		$style_template_id = isset( $element['params']['style_template'] ) ? $element['params']['style_template'] : '';
		if ( $style_template_id !== 0 && empty( $style_template_id ) ) {
			if ( ! empty( $attribute_element['attribute_title'] ) ) {
				$additional_class = str_replace( ' ', '_', strtolower( $attribute_element['attribute_title'] ) );
				$output           = "<div class='ulisting-attribute-template attribute_{$additional_class}'><span class='ulisting-attribute-template-name'>{$attribute_element['attribute_title']}</span><span class='ulisting-attribute-template-value card-item'>{$value}</span></div>";

				return apply_filters( 'uListing-sanitize-data', $output );
			}

			ulisting_write_log( $attribute_element );

			return $value;
		}

		$attribute_template = ( isset( $style_templates[ $style_template_id ]['attribute_template'] ) ) ? $style_templates[ $style_template_id ]['attribute_template'] : '';
		$option_template    = ( isset( $style_templates[ $style_template_id ]['option_template'] ) ) ? $style_templates[ $style_template_id ]['option_template'] : '';

		$value = self::set_na_value_attributes( $value, $element );

		$attribute_affix    = isset( $attribute_element['attribute_affix'] ) ? $attribute_element['attribute_affix'] : '';
		$additional_class   = str_replace( ' ', '_', strtolower( $attribute_element['attribute_title'] ) );
		$attribute_template = str_replace( "[attribute_type]", $additional_class, $attribute_template );
		$attribute_template = str_replace( "[attribute_name]", $attribute_element['attribute_title'], $attribute_template );
		$attribute_template = str_replace( "[sub_title]", $attribute_affix, $attribute_template );
		$attribute_template = str_replace( "[attribute_icon]", $attribute_icon, $attribute_template );

		$attribute_template = str_replace( "[attribute_option_icon]", ' ', $attribute_template );

		if ( is_array( $value ) ) {
			$attribute_template = str_replace( "[attribute_value]", implode( ", ", $value ), $attribute_template );
		} else {
			$attribute_template = str_replace( "[attribute_value]", $value, $attribute_template );
		}

		$option_items_content = '';
		foreach ( $attribute_options as $attribute_option ) {
			$_option_template = $option_template;

			if ( isset( $attribute_option['attribute_option_name'] ) ) {
				$attribute_option_id   = isset( $attribute_option['id'] ) ? $attribute_option['id'] : '';
				$attribute_option_icon = isset( $attribute_options_icon[ $attribute_option_id ] ) ? $attribute_options_icon[ $attribute_option_id ] : '';

				$_option_template     = str_replace( "[attribute_option_icon]", $attribute_option_icon, $_option_template );
				$_option_template     = str_replace( "[attribute_value]", $attribute_option['attribute_option_name'], $_option_template );
				$option_items_content .= $_option_template;
			}
		}

		$attribute_template = str_replace( "[option_items]", $option_items_content, $attribute_template );
		if ( isset( $element['params']['label'] ) ) {
			$attribute_template = str_replace( '[label]', $element['params']['label'], $attribute_template );
		}

		return $attribute_template;
	}

	/**
	 * @param $attribute
	 * @param $element
	 * @param $value
	 *
	 * @return mixed|null|string
	 */
	public static function render_price( $attribute, $element, $value ) {
		$style_templates = self::get_price_style_templates();
		$id              = $element['params']['style_template'];

		$price_template  = ( isset( $style_templates[ $id ]['attribute_template'] ) ) ? $style_templates[ $id ]['attribute_template'] : null;
		$price_panel     = ( isset( $style_templates[ $id ]['price_template'] ) ) ? $style_templates[ $id ]['price_template'] : null;
		$old_price_panel = ( isset( $style_templates[ $id ]['old_price_template'] ) ) ? $style_templates[ $id ]['old_price_template'] : null;

		if ( ! $price_template ) {
			return "";
		}

		$price_panel = ( isset( $value['price'] ) and ! empty( $value['price'] ) ) ? str_replace( "[price]", ulisting_currency_format( $value['price'] ), $price_panel ) : null;
		$price_panel = ( isset( $value['price'] ) and ! empty( $value['price'] ) ) ? str_replace( "[suffix]", ( isset( $value['suffix'] ) ) ? $value['suffix'] : '', $price_panel ) : null;

		$old_price_panel = ( isset( $value['old_price'] ) and ! empty( $value['old_price'] ) ) ? str_replace( "[old_price]", ulisting_currency_format( $value['old_price'] ), $old_price_panel ) : null;
		$old_price_panel = ( isset( $value['old_price'] ) and ! empty( $value['old_price'] ) ) ? str_replace( "[suffix]", ( isset( $value['suffix'] ) ) ? $value['suffix'] : '', $old_price_panel ) : null;

		if ( empty( $value['sale'] ) ) {
			$sale_panel = null;
		}

		$price_template = str_replace( "[price]", $price_panel, $price_template );
		$price_template = str_replace( "[old_price]", $old_price_panel, $price_template );
		$price_template = str_replace( "[attribute_name]", $attribute->title, $price_template );

		return $price_template;
	}

	/**
	 * @param StmListing $listing
	 * @param $element
	 */
	public static function _render_price( $attribute, $element ) {
		$style_templates = self::get_price_style_templates();
		$id              = $element['params']['style_template'];

		$price_template  = ( isset( $style_templates[ $id ]['attribute_template'] ) ) ? $style_templates[ $id ]['attribute_template'] : null;
		$price_panel     = ( isset( $style_templates[ $id ]['price_template'] ) ) ? $style_templates[ $id ]['price_template'] : null;
		$old_price_panel = ( isset( $style_templates[ $id ]['old_price_template'] ) ) ? $style_templates[ $id ]['old_price_template'] : null;

		if ( ! $price_template ) {
			return "";
		}

		$price_panel = ( isset( $attribute['attribute_value'] ) and ! empty( $attribute['attribute_value'] ) ) ? str_replace( "[price]", ulisting_currency_format( $attribute['attribute_value'] ), $price_panel ) : null;
		$price_panel = ( isset( $attribute['attribute_value'] ) and ! empty( $attribute['attribute_value'] ) ) ? str_replace( "[suffix]", ( isset( $attribute['meta_suffix'] ) ) ? $attribute['meta_suffix'] : '', $price_panel ) : null;

		$old_price_panel = ( isset( $attribute['meta_genuine'] ) and ! empty( $attribute['meta_genuine'] ) ) ? str_replace( "[old_price]", ulisting_currency_format( $attribute['meta_genuine'] ), $old_price_panel ) : null;
		$old_price_panel = ( isset( $attribute['meta_genuine'] ) and ! empty( $attribute['meta_genuine'] ) ) ? str_replace( "[suffix]", ( isset( $attribute['meta_suffix'] ) ) ? $attribute['meta_suffix'] : '', $old_price_panel ) : null;

		if ( $attribute['meta_genuine'] == $attribute['attribute_value'] or $attribute['meta_genuine'] < $attribute['attribute_value'] ) {
			$old_price_panel = null;
		}

		$price_template = str_replace( "[price]", $price_panel, $price_template );
		$price_template = str_replace( "[old_price]", $old_price_panel, $price_template );
		$price_template = str_replace( "[attribute_name]", $attribute['attribute_title'], $price_template );

		return $price_template;
	}

	public static function deslash( $string ) {
		$string = preg_replace( "/\\\+'/", "'", $string );
		$string = preg_replace( '/\\\+"/', '"', $string );
		$string = preg_replace( '/\\\+/', '\\', $string );

		return $string;
	}

	protected static function basic_quickview_template() {
		$style_templates = [
			0                  => [
				"icon"               => ULISTING_URL . "/assets/img/none.png",
				"name"               => "None",
				"quickview_template" => "",
			],
			"ulisting_style_1" => [
				"icon"               => ULISTING_URL . "/assets/img/quickview_style_1.png",
				"name"               => "Style 1",
				"quickview_template" => "<i class='fa fa-eye' aria-hidden='true'></i>",
			],
		];

		return $style_templates;
	}

	public static function get_quickview_style_templates() {
		$style_templates = self::basic_quickview_template();

		return apply_filters( "ulisting_quickview_style_templates", $style_templates );
	}

	public static function render_quickview( StmListing $listing, $element ) {
		$style_templates    = self::get_quickview_style_templates();
		$style_template_id  = $element['params']['style_template'];
		$quickview_template = ( isset( $style_templates[ $style_template_id ]['quickview_template'] ) ) ? $style_templates[ $style_template_id ]['quickview_template'] : '';

		return $quickview_template;
	}

	/**
	 * @param $term_id
	 *
	 * @return array
	 */
	public static function get_listing_types( $term_id ) {
		$listing_types = get_post_meta( $term_id, "listing_type_attribute" );

		return ( isset( $listing_types[0] ) ) ? $listing_types[0] : [];
	}

	public static function update_attribute() {
		$result = [
			'data'    => [],
			'status'  => 'error',
			'success' => false,
			'message' => __( 'Access denied', 'ulisting' ),
		];

		$data = ulisting_sanitize_array( $_GET );

		if ( current_user_can( 'manage_options' ) && isset( $data['attr_id'] ) ) {
			$attr_id    = $data['attr_id'];
			$editObject = self::find_one( $attr_id );

			$result['data']    = self::render_attr_fields( $editObject );
			$result['status']  = 'success';
			$result['message'] = __( 'Custom Field updated successfully' );
			$result['success'] = true;
		}

		wp_send_json( $result );
	}

	public static function render_attr_fields( $editObject ) {
		$listing_types     = ulisting_sanitize_array( StmListingType::getDataList() );
		$included_types    = [];
		$listing_type_list = [];
		$thumbnail         = get_post( StmListingSettings::isset_helper( $editObject, 'thumbnail_id' ) );

		foreach ( $listing_types as $listing_type_key => $listing_type_value ) {
			$listing_type_attributes = get_post_meta( $listing_type_key, 'listing_type_attribute', true ) ?: [];

			if ( is_array( $listing_type_attributes ) && in_array( $editObject->id, $listing_type_attributes ) ) {
				$included_types[] = $listing_type_key;
			}

			$listing_type_list[] = [
				'id'   => $listing_type_key,
				'text' => $listing_type_value
			];
		}

		return [
			'action' => admin_url( 'admin.php?page=listing_attribute' ),
			'fields' => [
				'id'           => StmListingSettings::settings_input_creator( StmListingSettings::isset_helper( $editObject, 'id' ), '', 'number' ),
				'title'        => StmListingSettings::settings_input_creator( StmListingSettings::isset_helper( $editObject, 'title' ), 'Title', 'text', 'Enter Title' ),
				'name'         => StmListingSettings::settings_input_creator( StmListingSettings::isset_helper( $editObject, 'name' ), 'Slug', 'text', 'Enter Slug' ),
				'affix'        => StmListingSettings::settings_input_creator( StmListingSettings::isset_helper( $editObject, 'affix' ), 'Affix', 'text', 'Enter Affix' ),
				'type'         => StmListingSettings::settings_select_creator( StmListingSettings::isset_helper( $editObject, 'type' ), 'Type', self::getType() ),
				'listing_type' => StmListingSettings::settings_select_creator( $included_types, 'Listing Type(s)', $listing_type_list ),

				'visual' => [
					'title' => __( 'Visual', 'ulisting' ),
					'icon'  => StmListingSettings::isset_helper( $editObject, 'icon', null ),
					'image' => $thumbnail,
				],
			],

			'text_domains' => StmListingSettings::get_all_texts(),
		];
	}
}
