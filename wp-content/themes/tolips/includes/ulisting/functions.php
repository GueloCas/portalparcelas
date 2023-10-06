<?php

if (!function_exists('tolips_listing_get_attribute')) {
	function tolips_listing_get_attribute($element_tmp, $model, $attribute_name) {
		$element = $element_tmp;
	  	$attribute  = uListing\Classes\StmListingAttribute::query()->where('name', $attribute_name)->findOne();
      if(!$attribute){
         return false;
      }
	  	$values = $model->getAttributeValue($attribute);
	  	$attribute_option_name = $attribute_value = '';
	  	if (is_array($values)) {
			foreach ($values as $id => $value) {
				$attribute_value       = (string)$id;
				$attribute_option_name = $value;
			}
	  	} else {
			$attribute_value = $values;
	  	}
		$model->attribute_elements[$attribute_name] = [
			'attribute_id'           => $attribute->id,
			'attribute_title'        => $attribute->title,
			'attribute_affix'        => $attribute->affix,
			'attribute_icon'         => $attribute->icon,
			'attribute_type'         => $attribute->type,
			'attribute_name'         => $attribute->name,
			'attribute_thumbnail_id' => $attribute->thumbnail_id,
			'attribute_value'        => $attribute_value,
			'attribute_option_name'  => $attribute_option_name
		];

		$element['params']['attribute']           = $attribute_name;
		$element['params']['attribute_type'] = $attribute->type;
		if(!empty($attribute_value)){
			printf('%s', uListing\Classes\StmListingAttribute::render_attribute($model, $element));
		}
	}
}


function tolips_listing_get_price($element, $model){
   $element['type']        = "attribute";
   $element['field_group'] = "price";
   $element['params']['type']           = "attribute";
   $element['params']['attribute']      = "price";
   $element['params']['attribute_type'] = "price";
   $element['params']['style_template'] = "ulisting_style_1";
   $model->title = esc_html__('Price', 'tolips');
   $value        = $model->getAttributeValue('price');
   if( isset($value) && !empty($value) ){
       echo uListing\Classes\StmListingAttribute::render_price($model, $element, $value);
   }
}

function toplip_avatar_url($userid){
   $user_avatar = get_user_meta($userid, 'stm_listing_avatar');
   $avatar_url = isset($user_avatar) && !empty($user_avatar) ? current($user_avatar)['url'] : (get_template_directory_uri() . '/images/placehoder-user.jpg');
   return $avatar_url;
}