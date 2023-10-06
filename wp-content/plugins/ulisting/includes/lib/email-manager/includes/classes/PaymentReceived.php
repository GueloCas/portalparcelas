<?php

namespace uListing\Lib\Email\Classes;

use uListing\Admin\Classes\StmEmailTemplateManager;
use uListing\Lib\Email\Classes\Basic\UlistingEmail;

class PaymentReceived extends UlistingEmail {
    /**
     * Replace all short-codes
     * @param $type
     * @param $args
     * @param $content
     * @return mixed
     */
    public function replace_shortcodes($type, $args, $content = '') {
        if ( empty($content) )
            $content = $this->email_manager[$type];

        $content = str_replace('\\\"','\"', $content);
        $content = str_replace('[order_id]', $args['plan_id'], $content);
        $content = str_replace('[customer_name]', $args['user_name'], $content);
        $content = str_replace('[payment_method]', $args['payment_method'], $content);
        $content = str_replace('[site_name]', $this->parse_component('site-name', []), $content);
        $content = str_replace('[payment_info]', $this->parse_component('payment-info', ['payment' => $args['payment']]), $content);
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

    /**
     * Send notification to admin
     * @param $args
     * @return string
     */
    public function send_to_admin($args) {
        $content = '<p style="text-align: left;">Hello, We earnestly acknowledge that user [customer_name]\'s payment of [payment_method] are received in a safe manner. His payment has been authorized and approved.</p>';
        $subject = $this->replace_shortcodes('subject', $args, 'New offset [site_name]');
        $content = $this->replace_shortcodes('content', $args, $content);
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