<?php

namespace uListing\Lib\Email\Classes;

use uListing\Admin\Classes\StmEmailTemplateManager;
use uListing\Classes\StmListing;
use uListing\Lib\Email\Classes\Basic\UlistingEmail;

class ListingStatusChanged extends UlistingEmail {

    public function initial_params($args) {
        $listing = StmListing::find_one($args['listing_id']);
        $user = $listing->getUser();
        $user_info = $user->data;
        $extra = [
            'listing_status_after'  => $args['listing_status_after'],
            'listing_status_before' => $args['listing_status_before']
        ];

        $this->email     = $user_info->user_email;
        $this->is_active = $this->email_manager['is_active'];
        $this->content   = $this->replace_shortcodes('content', ['listing' => $listing, 'user' => $user_info, 'extra' => $extra]);
        $this->subject   = $this->replace_shortcodes('subject', ['listing' => $listing, 'user' => $user_info, 'extra' => $extra]);
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
        $extra   = $args['extra'];
        $listing = $args['listing'];
        $name = !empty($user->display_name) ? $user->display_name : $user->user_nicename;

        if ( empty( $content ) )
            $content = $this->email_manager[$type];

        $content = str_replace('\\\"','\"', $content);
        $content = str_replace('[customer_name]', $name, $content);
        $content = str_replace('[listing_id]', $listing->ID, $content);
        $content = str_replace('[listing_title]', $listing->post_title, $content);
        $content = str_replace('[listing_status]', $listing->post_status, $content);
        $content = str_replace('[listing_status]', $listing->post_status, $content);
        $content = str_replace('[listing_status_after]', $extra['listing_status_after'], $content);
        $content = str_replace('[listing_status_before]', $extra['listing_status_before'], $content);
        $content = str_replace('[site_name]', $this->parse_component('site-name', []), $content);
        return $content;
    }

    /**
     * Send notification to admin
     * @param $args
     * @return string
     */
    public function send_to_admin( $args ) {
        $listing = StmListing::find_one($args['listing_id']);
        $user = $listing->getUser();
        $user_info = $user->data;
        $extra = [
            'listing_status_after'  => $args['listing_status_after'],
            'listing_status_before' => $args['listing_status_before']
        ];
        $content = '<p>Hello, the purpose of this email is to notify about the update on the user\'s listing status. The listing "[listing_title]" by "[customer_name]" has been successfully changed!</p>';
        $subject = $this->replace_shortcodes('subject', ['listing' => $listing, 'user' => $user_info, 'extra' => $extra], 'New offset [site_name]');
        $content = $this->replace_shortcodes('content', ['listing' => $listing, 'user' => $user_info, 'extra' => $extra], $content);
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