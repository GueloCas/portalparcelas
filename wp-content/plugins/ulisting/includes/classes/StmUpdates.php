<?php

namespace uListing\Classes;

use uListing\Admin\Classes\StmEmailTemplateManager;

/**
 * Class StmUpdates
 * @package uListing\Classes
 */
class StmUpdates {
    private static $updates = [
        '1.0.2' => [
            'update_email_templates',
        ],

        '1.0.3' => [
            'update_email_templates',
        ],

        '2.0,0' => [
            'uListing_next_updates'
        ],
        '2.0.8' => [
            'uListing_admin_notification_transient'
        ]
    ];

    public static function init() {
        if (version_compare( get_option( 'ulisting-version' ), ULISTING_VERSION, '<' ) )
            self::update_version();
    }

    public static function get_updates() {
        return self::$updates;
    }

    public static function needs_to_update() {
        $current_db_version = get_option( 'ulisting-db-version', 1 );
        $update_versions    = array_keys( self::get_updates() );
        usort( $update_versions, 'version_compare' );
        return ! is_null( $current_db_version ) && version_compare( $current_db_version, end( $update_versions ), '<' );
    }

    private static function maybe_update_db_version() {
        if ( self::needs_to_update() ) {
            $updates = self::get_updates();
            $db_version = get_option('ulisting-db-version');

            foreach ( $updates as $version => $callback_arr) {
                if ( version_compare( $db_version, $version, '<' )) {
                    foreach ($callback_arr as $callback) {
                        call_user_func( [self::class, $callback] );
                    }
                }
            }
        }

        update_option('ulisting-db-version', ULISTING_DB_VERSION, true);
    }

    public static function update_version() {
        update_option('ulisting-version', ULISTING_VERSION, true);
        self::maybe_update_db_version();
    }

    public static function update_email_templates() {
        if (empty(get_option(StmEmailTemplateManager::EMAIL_OPTION)))
            StmEmailTemplateManager::update_email_templates_store(StmEmailTemplateManager::email_templates_list());

        if (!empty(get_option('ulisting_saved_searches'))) {
            $saved_search = get_option('ulisting_saved_searches');
            $new_saved_search = StmEmailTemplateManager::find_by_slug('saved-search');
            $new_saved_search['subject'] = $saved_search['subject'];
            $new_saved_search['content'] = $saved_search['content'];
            StmEmailTemplateManager::update_by_slug('saved-search', $new_saved_search);
        }

        if (empty(get_option(StmEmailTemplateManager::SOCIAL_OPTION)))
            update_option(StmEmailTemplateManager::SOCIAL_OPTION, StmEmailTemplateManager::get_socials());

    }

    public static function uListing_next_updates() {
        /**
         * Update social login data
         */
        $networks      = !empty(get_option('ulisting_social_networks')) ? get_option('ulisting_social_networks') : [];
        $networks_data = StmListingSettings::social_login_networks_data();
        if ( !empty( $networks ) && is_array( $networks ) ) {
            foreach ($networks as $key => $network) {
                $network_data = isset($networks_data[$key]) ? $networks_data[$key] : [];
                if ( !empty( $network_data ) ) {
                    $network['title'] = $network_data['title'];

                    if ( !isset( $network['description'] ) && isset($network['social_links']['social_url']) ) {
                        $desc  = sprintf(
                            esc_html__( 'Here you can get more information about how to create OAuth APP for - %1$s Documentation. Don`t forget about setting up Authentication Redirect URL like -', 'ulisting' ),
                            sprintf(
                                '<a href="%s">%s</a>',
                                $network['social_links']['social_url'],
                                $network['title']
                            ), true
                        );
                        $network['description'] = $desc;
                    }

                    $networks[$key] = $network;
                }
            }
        }

        update_option('ulisting_social_networks', ulisting_sanitize_array($networks));
    }

    public static function uListing_admin_notification_transient() {
        $data = [ 'show_time' => DAY_IN_SECONDS * 3 + time(), 'step' => 0, 'prev_action' => '' ];
        set_transient( 'stm_ulisting_notice_setting', $data );
    }
}