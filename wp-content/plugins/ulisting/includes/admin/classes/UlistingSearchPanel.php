<?php
/**
 * Created by PhpStorm.
 * User: jamshid
 * Date: 6/19/19
 * Time: 16:40
 */

namespace uListing\Admin\Classes;


use uListing\Classes\UlistingSearch;

class UlistingSearchPanel {

	static $instance;

	public $object;

	public function __construct() {
		add_filter( 'set-screen-option', [ __CLASS__, 'set_screen' ], 10, 3 );
	}

	public static function set_screen( $status, $option, $value ) {
		return $value;
	}

	public function saved_searches_screen_option() {
		$option = 'per_page';
		$args   = [
			'label'   => 'Saved searches',
			'default' => 5,
			'option'  => 'saved_searches_per_page'
		];

		add_screen_option( $option, $args );
		$this->object = new UlistingSearchListTable();
	}

	public static function get_instance() {
		if ( ! isset( self::$instance ) ) {
			self::$instance = new self();
		}
		return self::$instance;
	}
}

add_action( 'plugins_loaded', function () {
	UlistingSearchPanel::get_instance();
} );