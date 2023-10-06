<?php

namespace uListing\Lib\Email\Classes;

use uListing\Admin\Classes\StmEmailTemplateManager;
use uListing\Classes\StmListingSettings;
use uListing\Lib\Email\Classes\Basic\UlistingEmail;

/**
 * Class UserCreated
 * @package uListing\Lib\Email\Classes
 */
class UserCreated extends UlistingEmail {
    /**
     * Replace all short-codes
     * @param $type
     * @param $args
     * @param string $content
     * @return mixed
     */
    public function replace_shortcodes($type, $args, $content = '') {
        if ( empty($content) )
            $content = $this->email_manager[$type];

        $content = str_replace('\\\"','\"', $content);
        $content = str_replace('[user_id]', $args['user_id'], $content);
        $content = str_replace('[user_role]', $args['user_role'], $content);
        $content = str_replace('[customer_name]', $args['user_name'], $content);
        $content = str_replace('[site_name]', $this->parse_component('site-name', []), $content);
        $content = str_replace('[account_link]', $this->parse_component('account-link', []), $content);

        return $content;
    }

    /**
     * Send notification to admin
     * @param  $args
     * @return mixed
     */
    public function send_to_admin($args) {
        $content = '<p>Hello, the purpose of this email is to notify about a new created account by [customer_name] and the new account has been created successfully.</p>';
        $subject = $this->replace_shortcodes('subject', $args, 'New offset [site_name]');
        $content = $this->replace_shortcodes('content', $args, $content);
        $this->_send_to_admin(
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