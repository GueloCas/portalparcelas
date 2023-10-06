<?php

namespace uListing\Classes;

use uListing\Classes\StmListingType;
use uListing\Classes\StmListingTemplate;
use uListing\Classes\Vendor\Validation;

class StmListingFilter {

	public static function render(StmListingType $listingType, $serchFormType, $args = []) {
		$search_fields = $listingType->getSearchFields($serchFormType);
		if(!empty($search_fields))
			return StmListingTemplate::load_template('filter/'.$serchFormType, [
				'search_fields' => $search_fields,
				'listingType' => $listingType,
				'args' => $args
			]);
		return false;
	}

	public static function get_data_api(){
		$request_body = file_get_contents('php://input');
		$data = json_decode($request_body, true);

		$result = [
			'success' => false
		];

		$validator = new Validation();
		$validator->validation_rules(array(
			'listing_type_id' => 'required',
			'search_form_type' => 'required',
		));

		if($validator->run($data) === false) {
			$result['errors'] = $validator->get_errors_array();
			wp_send_json($result);
			die;
		}

		if( !($listingType = StmListingType::find_one($data['listing_type_id'])) ){
			$result['message'] = _e("Object not found");
			wp_send_json($result);
			die;
		}

		$params = (isset($data['query_data'])) ? $data['query_data'] : null;
		$search_fields = $listingType->getSearchFields($data['search_form_type'], $params);
		return self::build_data($listingType, $search_fields, $data['value']);
	}

	/**
	 * @param $listingType
	 * @param $search_fields
	 * @param array $params
	 * @param bool $content
	 *
	 * @return mixed
	 */
	public static function build_data($listingType, $search_fields, $params = [], $content = false){
		$content_html = [];
		$field_data   = [];
		$field_show   = [];
		foreach ($search_fields as $field) {
			$field_type = key($field);
			$field = current($field);

			if(isset($field['attribute_name']))
                $field_show[$field['attribute_name']] = true;

			$condition_field = (isset($field['attribute_name']) AND isset($field['use_field'])) ? true : false;

			if($field_type == StmListingType::SEARCH_FORM_TYPE_SEARCH AND $condition_field){
				$field_data[$field['attribute_name']] = (isset($params[$field['attribute_name']])) ? $params[$field['attribute_name']] : "";
				if($content)
					$content_html[] = StmListingTemplate::load_template( 'components/fields/'.$field['type'],
						[
							"model" => "data.{$field['attribute_name']}",
							"callback_change" => "change",
							"placeholder" => (isset($field['placeholder'])) ? "{$field['placeholder']}" : "" ,
							"field" => $field
						]
					);
			}

			if($field_type == StmListingType::SEARCH_FORM_TYPE_PROXIMITY) {
				$field_data[$field['attribute_name']] = (isset($params[$field['attribute_name']])) ? current($params[$field['attribute_name']]) : $field['default'];
				if($content)
					$content_html[] = StmListingTemplate::load_template( 'components/fields/'.$field['type'],
							array(
								"model"   => "data.{$field['attribute_name']}",
								"callback_change" => "change",
								"units"   => (isset($field['units'])) ? "{$field['units']}" : "",
								"min"     => (isset($field['min'])) ? "{$field['min']}" : "",
								"max"     => (isset($field['max'])) ? "{$field['max']}" : "",
								"default" => (isset($field['default'])) ? $field['default'] : "",
								"field"   => $field
							));
			}

			if($field_type == StmListingType::SEARCH_FORM_TYPE_LOCATION){
				$field_data[$field['attribute_name']]['address'] = (isset($field['address']) AND isset($params[$field['address']])) ? $params[$field['address']] : "";
				$field_data[$field['attribute_name']]['lat'] = (isset($field['lat']) AND isset($params[$field['lat']])) ? $params[$field['lat']] : 0;
				$field_data[$field['attribute_name']]['lng'] = (isset($field['lng']) AND isset($params[$field['lng']])) ? $params[$field['lng']] : 0;
				if($content)
					$content_html[] = StmListingTemplate::load_template( 'components/fields/'.$field['type'],
							array(
								"model" => "data.{$field['attribute_name']}",
								"callback_change" => "change",
								"placeholder" => (isset($field['placeholder'])) ? "{$field['placeholder']}" : "" ,
								"field"   => $field
							));
			}

			if($field_type == StmListingType::SEARCH_FORM_TYPE_RANGE AND $condition_field){
				$field_data[$field['attribute_name']] = [ $field['min'], $field['max'] ];
				if(isset($params['range'][$field['attribute_name']])) {
					$_params = explode(";",$params['range'][$field['attribute_name']]) ;
					$field_data[$field['attribute_name']] = array( $_params[0], $_params[1] );
				}

				if($content)
					$content_html[] = StmListingTemplate::load_template( 'components/fields/'.$field['type'],
							array(
								"model" => "data.{$field['attribute_name']}",
								"callback_change" => "change",
								"suffix" => (isset($field['suffix'])) ? "{$field['suffix']}" : "",
								"prefix" => (isset($field['prefix'])) ? "{$field['prefix']}" : "",
								"min"    => (isset($field['min'])) ? "{$field['min']}" : "",
								"max"    => (isset($field['max'])) ? "{$field['max']}" : "",
								"field"  => $field
							)
						);
			}

			if($field_type == StmListingType::SEARCH_FORM_TYPE_DATE){

				if($field['date_type'] == 'exact')
					$field_data[$field['attribute_name']] = (isset($params[$field['attribute_name']])) ?  date('Y-m-d', strtotime($params[$field['attribute_name']]))  : "";

				if($field['date_type'] == 'range' && isset($field['attribute_name'])) {
					$field_data[$field['attribute_name']] = array();
					if(isset( $params['date_range'][$field['attribute_name']] )) {
						$dateFrom = date('Y-m-d', strtotime($params['date_range'][$field['attribute_name']][0]));
						$dateTo   = date('Y-m-d', strtotime($params['date_range'][$field['attribute_name']][1]));;
						$field_data[$field['attribute_name']] = array(
							$dateFrom,
							$dateTo
						);
					}
				}

				if($content && isset($field['attribute_name']))
					$content_html[] = StmListingTemplate::load_template( 'components/fields/'.$field['type'],
							[
								"model"           => "data.{$field['attribute_name']}",
								"callback_change" => "change",
								"name"            => "{$field['attribute_name']}",
								"date_type"       => (isset($field['date_type'])) ? "{$field['date_type']}" : "",
								"placeholder"     => (isset($field['placeholder'])) ? "{$field['placeholder']}" : "",
								"field"           => $field
							]
					);
			}

			if($field_type == StmListingType::SEARCH_FORM_TYPE_DROPDOWN AND $condition_field){
				$field_data[$field['attribute_name']] = (isset($params[$field['attribute_name']])) ? $params[$field['attribute_name']] : null;
				$field_data["attribute_items"][$field['attribute_name']] = isset($field['items']) ? $field['items'] : [];
				if($content)
					$content_html[] = StmListingTemplate::load_template( 'components/fields/'.$field['type'],
						[
							"model"      => "data.{$field['attribute_name']}",
							"order_by"   => (isset($field['order_by'])) ? "{$field['order_by']}" : "",
							"order"      => (isset($field['order'])) ? "{$field['order']}" : "",
							"callback_change" => "change",
							"items"      => "data.attribute_items.{$field['attribute_name']}",
							"hide_empty" => (isset($field['hide_empty'])) ? "{$field['hide_empty']}" : "",
							"field"      => $field,
							"placeholder" => (isset($field['placeholder'])) ? "{$field['placeholder']}" : "" ,
							"attribute_name" => $field['attribute_name']
						]
					);
			}

			if($field_type == StmListingType::SEARCH_FORM_TYPE_CHECKBOX AND $condition_field){
				$field_data[$field['attribute_name']] = (isset($params[$field['attribute_name']])) ? $params[$field['attribute_name']] : [];
				$field_data["attribute_items"][$field['attribute_name']] = isset($field['items']) ? $field['items'] : [];
				if($content)
					$content_html[] = StmListingTemplate::load_template( 'components/fields/'.$field['type'],
						array(
							"model"           => "data.{$field['attribute_name']}",
							"order_by"        => (isset($field['order_by'])) ? "{$field['order_by']}" : null,
							"order"           => (isset($field['order'])) ? "{$field['order']}" : null,
							"column"          => (isset($field['column'])) ? "{$field['column']}" : 1,
							"callback_change" => "change",
							"items"           => "data.attribute_items.{$field['attribute_name']}",
							"hide_empty"      => (isset($field['hide_empty'])) ? "{$field['hide_empty']}" : null,
							"field"           => $field
						));
			}
		}

		if($content)
			$result['content']       = $content_html;

		$result['field_type']    = StmListingType::getFieldType();
		$result['field_data']    = $field_data;
		$result['search_fields'] = $search_fields;
		$result['field_show'] = $field_show;
		$result['success']       = true;

		return $result;

	}

	public static function build_listing_type_order($listingType){

		$listing_order    = $listingType->getListingsOrder();
		$view_type        = (isset($listing_order['view_type'])) ? $listing_order['view_type'] : 'line';
		$order_by_default = (isset($listing_order['order_by_default'])) ? $listing_order['order_by_default'] : null;
		$items            = (isset($listing_order['items'])) ? $listing_order['items'] : [];
		$listing_order_list = [];

		foreach ($items as $item) {
			if( !isset($item['order_by']) OR !isset($item['order_type']) OR !isset($item['label']))
				return;
			$listing_order_list[] = array(
				'id'        => $item['order_by'].'#'.$item['order_type'],
				'label'     => $item['label'],
				'order_by'  => $item['order_by'],
				'order'     => $item['order_type'],
				'is_open'   => (isset($item['is_open'])) ? $item['is_open'] : null
			);
		}

		$data = array(
			'view_type'        => (isset($listing_order['view_type'])) ? $listing_order['view_type'] : 'line',
			'order_by_default' => (isset($listing_order['order_by_default'])) ? $listing_order['order_by_default'] : null,
			'listing_order'   => $listing_order_list,
		);

		return $data;
	}

	public static function getFilterPreviews()
    {
        $data = [
            'stm_search_form_advanced' => [
                ULISTING_URL."/assets/img/stm_search_form_advanced.jpg",
            ],

            'stm_search_form_type' => [
                ULISTING_URL."/assets/img/stm_search_form_type.jpg",
            ],

            'stm_search_form_category' => [
                ULISTING_URL."/assets/img/stm_search_form_category.jpg",
            ],
        ];

        return apply_filters('ulisting-search-filters-preview', $data);
    }

}