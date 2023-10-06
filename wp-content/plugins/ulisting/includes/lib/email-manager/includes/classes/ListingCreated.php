<?php

namespace uListing\Lib\Email\Classes;

use uListing\Admin\Classes\StmEmailTemplateManager;
use uListing\Classes\StmListingTemplate;
use uListing\Lib\Email\Classes\Basic\UlistingEmail;

class ListingCreated extends UlistingEmail {
    /**
     * @param $args
     * @return mixed|void
     */
    public function initial_params($args) {
        $user = get_user_by( 'ID', $args['user_id']);
        $user_info = $user->data;

        $this->email     = $user_info->user_email;
        $this->is_active = $this->email_manager['is_active'];
        $this->content   = $this->replace_shortcodes('content', ['listing' => $args['listing'], 'user' => $user_info]);
        $this->subject   = $this->replace_shortcodes('subject', ['listing' => $args['listing'], 'user' => $user_info]);
    }

    /**
     * Replace all short-codes
     * @param $type
     * @param $args
     * @param $content
     * @return mixed
     */
    public function replace_shortcodes($type, $args, $content = '') {
        $user    = $args['user'];
        $listing = $args['listing'];

        if ( empty($content) )
            $content = $this->email_manager[$type];

        $name    = !empty($user->display_name) ? $user->display_name : $user->user_nicename;
        $content = str_replace('\\\"','\"', $content);
        $content = str_replace('[customer_name]', $name, $content);
        $content = str_replace('[listing_id]', $listing->ID, $content);
        $content = str_replace('[listing_title]', $listing->post_title, $content);
        $content = str_replace('[listing_status]', $listing->post_status, $content);
        $content = str_replace('[site_name]', $this->parse_component('site-name', []), $content);
        $content = str_replace(
            "[listing_list]",
            StmListingTemplate::load_template( 'email/saved-searches/notification-saved-searches-listing-list', [
                'single'   => true,
                'listings' => [$listing],
            ])
            , $content);
        return $content;
    }

    /**
     * Send notification to admin
     * @param  $args
     * @return mixed
     */
    public function send_to_admin($args) {
        $user = get_user_by( 'ID', $args['user_id']);
        $user_info = $user->data;

        $content = '<p style="text-align: center;">Hello, the purpose of this email is to notify about a new successful created listing by [customer_name]. The new created listing is [listing_title] with a status [listing_status].</p>
                    <p style="text-align: center;">[listing_list]</p>';

        $subject = $this->replace_shortcodes('subject', ['listing' => $args['listing'], 'user' => $user_info], 'New offset [site_name]');
        $content = $this->replace_shortcodes('content', ['listing' => $args['listing'], 'user' => $user_info], $content);
        return $this->_send_to_admin(
            ulisting_render_template(EMAIL_MANAGER_PATH . '/templates/layouts/info.php', [
                'subject' => $subject,
                'content' => $content,
                'footer'  => false,
                'color'   => StmEmailTemplateManager::COLOR_INFO,
                'header'  => [],
                'banner'  => [],
            ])
        );
    }
}