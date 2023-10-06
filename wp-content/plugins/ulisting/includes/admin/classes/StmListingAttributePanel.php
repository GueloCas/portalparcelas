<?php

namespace uListing\Admin\Classes;

use uListing\Admin\Classes\StmListingAttributeList;

class StmListingAttributePanel {

	static $instance;

	public $object;

	public function __construct() {
		add_filter( 'set-screen-option', [ __CLASS__, 'set_screen' ], 10, 3 );
		add_action( 'admin_menu', [ $this, 'plugin_menu' ] );
	}

	public static function set_screen( $status, $option, $value ) {
		return $value;
	}

	public function plugin_menu() {
        $hook = add_menu_page(
            esc_html__("Custom Fields", "ulisting"),
            esc_html__("Custom Fields", "ulisting"),
            'manage_options',
            'listing_attribute',
            array($this, 'render_index'),
            'dashicons-menu-alt3', 7
        );

		add_action( "load-$hook", [ $this, 'attribute_screen_option' ] );

        add_submenu_page(
            'listing_attribute',
            esc_html__("Custom Fields Edit", "ulisting"),
            false,
            'manage_options',
            'listing_attribute_edit',
            array($this, 'render_edit')
        );
	}

	public  function render_index() {
		ulisting_render_template(ULISTING_ADMIN_PATH . '/views/listing-attribute/index.php', [], true);
	}

	public  function render_edit() {
		ulisting_render_template(ULISTING_ADMIN_PATH . '/views/listing-attribute/edit.php', [], true);
	}

	public function attribute_screen_option() {
		$option = 'per_page';
		$args   = [
			'label'   => 'Attribute',
			'default' => 5,
			'option'  => 'attributes_per_page'
		];

		add_screen_option( $option, $args );
		$this->object = new StmListingAttributeList();
	}

	public static function get_instance() {
		if ( ! isset( self::$instance ) ) {
			self::$instance = new self();
		}
		return self::$instance;
	}
}

add_action( 'plugins_loaded', function () {
	StmListingAttributePanel::get_instance();
} );

