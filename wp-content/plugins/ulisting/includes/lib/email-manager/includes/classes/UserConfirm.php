<?php

namespace uListing\Lib\Email\Classes;

use uListing\Admin\Classes\StmEmailTemplateManager;
use uListing\Classes\StmListingSettings;
use uListing\Lib\Email\Classes\Basic\UlistingEmail;

class UserConfirm extends UlistingEmail {
    /**
     * Replace all short-codes
     * @param $type
     * @param $args
     * @return mixed
     */
    public function replace_shortcodes($type, $args) {
        $content = $this->email_manager[$type];

        $code = sha1( $args['user_id'] . time() );
        add_user_meta( $args['user_id'], 'uListing_user_has_to_be_activated', $code, true );
        $account_link = get_page_link( StmListingSettings::getPages(StmListingSettings::PAGE_ACCOUNT_PAGE) ) . '/verify';
        $activation_link = add_query_arg( array( 'key' => $code, 'user' => $args['user_id'] ), $account_link);

        $content = str_replace('\\\"','\"', $content);
        $content = str_replace('[user_id]', $args['user_id'], $content);
        $content = str_replace('[user_role]', $args['user_role'], $content);
        $content = str_replace('[customer_name]', $args['user_name'], $content);
        $content = str_replace('[site_name]', $this->parse_component('site-name', []), $content);
        $content = str_replace('[confirm_button]', $this->parse_component('confirm-button', ['link' => $activation_link]), $content);

        return $content;
    }

    /**
     *
     */
    public static function user_confirm_callback() {
        $data = ulisting__sanitize_array($_GET);
        $user_id = isset($data['user']) ? apply_filters('uListing-sanitize-data', $data['user']) : null;

        if ( $user_id ) {
            $code = get_user_meta( $user_id, 'uListing_user_has_to_be_activated', true );
            $key = isset($data['key']) ? $data['key'] : null;
            if ( $code == $key ) {
                delete_user_meta( $user_id, 'uListing_user_has_to_be_activated' );
                add_user_meta( $user_id, 'verified', 1 );
            }
            $account_link = get_page_link( StmListingSettings::getPages(StmListingSettings::PAGE_ACCOUNT_PAGE) );
            $location = add_query_arg( array( 'verified' => true ), $account_link);
            header( 'Location: '. $location);
        }
    }
}