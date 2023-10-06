<?php

namespace uListing\Classes;

class UlistingSanitize {


	public static function init(){
		add_filter("sanitize_json_meta_accordion_for_json", [self::class, "sanitize_json"], 10, 4);
	}

	/**
	 * @param $meta_value
	 * @param $meta_key
	 * @param $object_type
	 * @param $object_subtype
	 *
	 * @return array|mixed|object
	 */
	public static function sanitize_json($meta_value, $meta_key, $object_type, $object_subtype ){
		if(ulisting_json_encode($meta_value)) {
			$allowed_html = array(
				'a'      => array(
					'href'  => array(),
					'title' => array()
				),
				'img'    => array(
					'src' => [],
					'alt' => [],
				),
				'ul'     => array(),
				'li'     => array(),
				'br'     => array(),
				'hr'     => array(),
				'h1'     => array(),
				'h2'     => array(),
				'h3'     => array(),
				'h4'     => array(),
				'h5'     => array(),
				'h6'     => array(),
				'h7'     => array(),
				'em'     => array(),
				'strong' => array(),
			);
			wp_kses( $meta_value, $allowed_html );
		}else
			$meta_value = sanitize_text_field($meta_value);
		return $meta_value;
	}
}