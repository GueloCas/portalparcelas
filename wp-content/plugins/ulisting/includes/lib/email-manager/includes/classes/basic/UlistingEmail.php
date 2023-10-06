<?php

namespace uListing\Lib\Email\Classes\Basic;

use uListing\Admin\Classes\StmEmailTemplateManager;

/**
 * Class UlistingEmail
 * @package uListing\Lib\Email\Classes\Basic
 */
abstract class UlistingEmail {
    /**
     * @var
     * UlistingEmail Properties
     */
    protected $email;
    protected $subject;
    protected $banner;
    protected $footer;
    protected $header;
    protected $content;
    protected $is_active;
    protected $slug;
    protected $email_manager;

    /**]
     * UlistingEmail constructor.
     * @param $email_manager
     */
    public function __construct($email_manager)
    {
        $this->email_manager = $email_manager;
    }

    /**
     * Init fields
     * @param $args
     * @return mixed
     */
    /**
     * @param $args
     * @return mixed|void
     */
    public function initial_params($args) {
        $this->email     = $args['user_email'];
        $this->is_active = $this->email_manager['is_active'];
        $this->slug      = $this->email_manager['slug'];
        $this->content   = $this->replace_shortcodes('content', $args);
        $this->subject   = $this->replace_shortcodes('subject', $args);
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
            'color'   => StmEmailTemplateManager::COLOR_INFO,
            'header'  => ['status' => $this->header_status(), 'url' => $this->email_logo_url()],
            'banner'  => ['status' => $this->banner_status(), 'url' => $this->email_banner_url()],
        ]);
    }

    /**
     * Return Email
     * @return mixed
     */
    public function get_email() {
        return $this->email;
    }

    /**
     * Return Subject
     * @return mixed
     */
    public function get_subject() {
        return $this->subject;
    }

    /**
     * Check status of Email Template
     * @return boolean
     */
    public function is_active() {
        return $this->is_active;
    }

    /**
     * Return slug
     * @return boolean
     */
    public function get_slug() {
        return $this->slug;
    }

    /**
     * @return boolean
     */
    public function banner_status() {
        return $this->email_manager['banner'];
    }

    /**
     * @return boolean
     */
    public function header_status() {
        return $this->email_manager['header'];
    }

    /**
     * @return boolean
     */
    public function footer_status() {
        return $this->email_manager['footer'];
    }

    /**
     * Return Logo url
     * @return string
     */
    public function email_logo_url() {
        $email_logo = get_option("ulisting_email_logo");
        $logo = get_post($email_logo);
        return ($email_logo AND $logo) ? $logo->guid : "";
    }

    /**
     * Return Banner url
     * @return string
     */
    public function email_banner_url() {
        $email_banner = get_option("ulisting_email_banner");
        $banner = get_post($email_banner);
        return ($banner AND $email_banner) ? $banner->guid : "";
    }

    /**
     * Get and render component by type
     * @param $type
     * @param $args
     * @return string
     */
    public function parse_component($type, $args) {
        return ulisting_render_template(EMAIL_MANAGER_PATH . '/templates/components/'. $type .'.php', $args);
    }

    /**
     *
     * @param $args
     */
    public function send_to_admin($args) {}

    /**
     * Private helper function
     * @param $content
     * @return boolean
     */
    protected function _send_to_admin($content) {
        $subject = __('New offset', 'ulisting') .  get_bloginfo( 'name', 'display' );
        $headers = $this->_get_email_headers();
        $admin_email = get_option('admin_email');
        @wp_mail(
            $admin_email,
            $subject,
            $content,
            $headers
        );
    }

    /**
     * @return string
     */
    private function _get_email_headers() {
      $headers = 'From: No Reply <noreply@'.$_SERVER['HTTP_HOST'].'>' . "\r\n";
      $headers .= "MIME-Version: 1.0\r\n";
      $headers .= "Content-Type: text/html; charset=UTF-8\r\n";
      return $headers;
    }
}