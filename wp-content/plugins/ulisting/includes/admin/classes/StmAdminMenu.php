<?php

namespace uListing\Admin\Classes;

use uListing\Classes\StmListingSettings;
use uListing\Classes\UlistingSearch;

class StmAdminMenu
{
    public function __construct()
    {
        add_action('admin_menu', [$this, 'settings_menu'], 20);
        add_action('admin_menu', [$this, 'add_settings_submenu_pages'], 20);

	    add_filter('plugin_action_links_' . plugin_basename(ULISTING_PLUGIN_FILE), [$this, 'plugin_action_links']);
        add_action('admin_menu', [$this, 'add_listing_types_submenu_pages'], 25);

    }

    /**
     * @return StmAdminMenu
     */
    public static function init()
    {
        return new StmAdminMenu();
    }

    public function settings_menu()
    {
        add_menu_page(
            'Ulisting',
            'Ulisting',
            'manage_options',
            'settings-page',
            array($this, 'render_settings'),
            ULISTING_URL . '/assets/img/ulisting-logo.png', 5
        );

        add_menu_page(
            'Inventory Page',
            'Inventory Page',
            'manage_options',
            'inventory-list',
            array($this, 'render_inventory'),
            'dashicons-schedule', 9
        );
    }

    public function add_settings_submenu_pages()
    {
        add_submenu_page(
            'settings-page',
            __("Settings", "ulisting"),
            __("Settings", "ulisting"),
            'manage_options',
            'settings-page',
            array($this, 'render_settings')
        );

        add_submenu_page(
            'settings-page',
            __("Extensions", "ulisting"),
            __("Extensions", "ulisting"),
            'manage_options',
            'extensions-page',
            array($this, 'render_settings')
        );

        add_submenu_page(
            'settings-page',
            __("Saved Searches", "ulisting"),
            __("Saved Searches", "ulisting"),
            'manage_options',
            'saved-searches-page',
            array($this, 'render_settings')
        );

        add_submenu_page(
            'settings-page',
            __("Demo Import", "ulisting"),
            __("Demo Import", "ulisting"),
            'manage_options',
            'demo-import-page',
            array($this, 'render_settings')
        );

        add_submenu_page(
            'settings-page',
            __("Status", "ulisting"),
            __("Status", "ulisting"),
            'manage_options',
            'status-page',
            array($this, 'render_settings')
        );

        if ( ! uListing_subscription_active() && ! uListing_user_role_active() && ! uListing_social_login_active() ) {
            add_submenu_page(
                'settings-page',
                __( "Upgrade", "ulisting" ),
                '<span style="color: #adff2f;"><span style="font-size: 14px;" class="dashicons dashicons-star-filled stm_go_pro_menu"></span>' . __( "Upgrade", "ulisting" ) . '</span>',
                'manage_options',
                'ulisting-upgrade',
                array( $this, 'render_go_pro' )
            );
        }

        if( ulisting_compare_active() || uListing_subscription_active() || uListing_user_role_active() || uListing_social_login_active() || ulisting_wishlist_active()) {
			add_submenu_page(
				'settings-page',
				__("Contact Us", "ulisting"),
				__("Contact Us", "ulisting"),
				'manage_options',
				'settings-page-contact',
				array($this, 'render_contact_us')
			);
		}
    }

	public function add_listing_types_submenu_pages()
	{
        if ( function_exists('uListing_exporter_active') && uListing_exporter_active() ) {
            add_submenu_page(
                'edit.php?post_type=listing_type',
                __('uListing exporter ', "ulisting"),
                __('Exporter', "ulisting"),
                'manage_options',
                'exporter',
                [self::class, 'render_exporter']
            );
        }
	}

	public function plugin_action_links($links) {
		$settings_link = sprintf( '<a href="%1$s">%2$s</a>', admin_url( 'edit.php?post_type=listing_type&page=stm_listing_settings' ), __("Settings", "ulisting") );

		array_unshift( $links, $settings_link );

		if (!ulisting_subscription_active() && !ulisting_user_role_active()) {
			$links['get_pro'] = sprintf( '<a href="%1$s" target="_blank" class="ulisting-get-pro">%2$s</a>', esc_url('https://stylemixthemes.com/wordpress-classified-plugin/?utm_source=admin&utm_medium=promo&utm_campaign=2020'), __("Upgrade to Pro Bundle", "ulisting") );
		}

		return $links;
	}

    public static function render_exporter(){
        require ULISTING_EXPORTER_PATH . '/templates/exporter/exporter.php';
    }

	public static function render_settings() {
        ulisting_render_template(ULISTING_ADMIN_PATH . '/views/settings/settings-page.php', [], true);
    }

    public static function render_inventory() {
        ulisting_render_template(ULISTING_ADMIN_PATH . '/views/inventory/inventory-list.php', [], true);
    }

	public static function render_contact_us() {
		ulisting_render_template(ULISTING_ADMIN_PATH . '/views/settings/contact.php', [], true);
	}

    /**
     * Render page go_pro
     */
    public static function render_go_pro() {
		ulisting_render_template(ULISTING_ADMIN_PATH . '/views/go_pro/index.php', [], true);
    }
}