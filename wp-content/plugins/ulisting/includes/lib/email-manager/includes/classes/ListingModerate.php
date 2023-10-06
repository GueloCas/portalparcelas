<?php

namespace uListing\Lib\Email\Classes;

use uListing\Admin\Classes\StmEmailTemplateManager;
use uListing\Classes\StmListingTemplate;
use uListing\Lib\Email\Classes\Basic\UlistingEmail;

class ListingModerate extends UlistingEmail {
    /**
     * @param $args
     * @return mixed|void
     */
    public function initial_params($args) {
        $user_info = $args['user_info'];
        $this->email     = $user_info->user_email;
        $this->is_active = $this->email_manager['is_active'];
        $this->content   = $this->replace_shortcodes('content', $args);
        $this->subject   = $this->replace_shortcodes('subject', $args);
    }

    /**
     * Replace all short-codes
     * @param $type
     * @param $args
     * @param $content
     * @return mixed
     */
    public function replace_shortcodes($type, $args, $content = '') {
        $listing = $args['listing'];
        $user = $args['user_info'];
        $name = !empty($user->display_name) ? $user->display_name : $user->user_nicename;

        if ( empty($content) )
            $content = $this->email_manager[$type];

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
     * Content of Email Template
     * @return string
     */
    public function get_content() {
        return  ulisting_render_template(EMAIL_MANAGER_PATH . '/templates/layouts/info.php', [
            'subject' => $this->subject,
            'content' => $this->content,
            'footer'  => $this->footer_status(),
            'color'   => StmEmailTemplateManager::COLOR_SUCCESS,
            'header'  => ['status' => $this->header_status(), 'url' => $this->email_logo_url()],
            'banner'  => ['status' => $this->banner_status(), 'url' => $this->email_banner_url()],
        ]);
    }
}