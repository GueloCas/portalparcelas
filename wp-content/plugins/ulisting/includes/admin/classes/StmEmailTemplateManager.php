<?php

namespace uListing\Admin\Classes;

use uListing\Lib\Email\Classes\Basic\UlistingEmail;

/**
 * Class StmEmailTemplateManager
 * @package uListing\Admin\Classes
 */
class StmEmailTemplateManager {

    const EMAIL_OPTION  = 'uListing-email-store';
    const SOCIAL_OPTION = 'ulisting_email_socials';
    const COLOR_INFO    = '#388CDA';
    const COLOR_SUCCESS = '#27ae61';
    /**
     * Create instance of new Email Template
     * @param $type
     * @return mixed
     */
    public static function create_instance($type) {
        $class_name = '';
        if (strpos($type, '-') !== false)
            $class_name = explode('-', $type);
        if (strpos($type, '_') !== false)
            $class_name = explode('_', $type);

        if ( !empty($class_name) ) {
            foreach ($class_name as $index => $name) {
                $class_name[$index] = ucfirst($name);
            }
        }

        $email_options = self::find_by_slug($type);
        $class_name = implode('', $class_name);
        $path = EMAIL_MANAGER_PATH . '/includes/classes/' . $class_name . '.php';
        if (file_exists($path)) {
            require_once $path;
            $class_name = '\uListing\Lib\Email\Classes\\' . $class_name;
            return new $class_name($email_options);
        }

        return null;
    }

    /**
     * uListing plugin Email Templates list
     * ulisting-email-templates-manager - hook for add template
     * @return mixed|void
     */
    public static function email_templates_list() {
        return [
            'saved-search' => [
                'is_active' => 1,
                'to_admin'  => 0,
                'banner'    => 1,
                'header'    => 1,
                'footer'    => 1,
                'slug'      => 'saved-search',
                'title'     => 'Saved search',
                'subject'   => 'New offset [site_name]',
                'content'   => '<h3 style="text-align: center;">
                                    Hello, [customer_name] You were subscribed to receive new offers from [site_name]. [count] new properties were found matching your search criteria.
                              </h3>
                              <p style="text-align: center;">If you want to receive new suggestions by new criteria, click on “view more”. After redirecting to the inventory page please enter new criteria and click "Find" then "Save Search." This will create a new auto search.</p>
                              <p style="text-align: center;">[listing_list]</p>
                              <p style="text-align: center;"><strong>If you don’t want to be notified please enter your account and delete auto search</strong>.</p>
                ',
                'description' => 'This message is sent to user when he gets offer(s) found in his search criteria.',
            ],

            'payment-status-changed' => [
                'is_active' => 1,
                'banner'    => 1,
                'to_admin'  => 1,
                'header'    => 1,
                'footer'    => 1,
                'slug'      => 'payment-status-changed',
                'title'     => 'User Plan status change',
                'subject'   => 'Payment #[order_id] status has been changed',
                'content'   => '<p>Hello [customer_name], the purpose of this email is to notify about your update on the status of your order payment on [date]. Your status was [payment_status_before] before but now it is [payment_status_after]. </p>
                              <p style="text-align: center;" data-mce-style="text-align: center;"><strong>Please check your changes below:</strong><br></p>
                              <p style="text-align: center;" data-mce-style="text-align: center;">[payment_info]</p>
                              <p style="text-align: center;" data-mce-style="text-align: center;">You can reach out to us in case of any doubt or clearance required from our side.</p>
                ',
                'description' => 'This message is sent when user’s plan status has been changed.',
            ],

            'payment-received' => [
                'is_active' => 1,
                'banner'    => 1,
                'header'    => 1,
                'to_admin'  => 1,
                'footer'    => 1,
                'slug'      => 'payment-received',
                'title'     => 'Payment received',
                'subject'   => 'Payment #[order_id] received',
                'content'   => '<p>Hello [customer_name], We earnestly acknowledge that we received your order payment of [payment_method] in a safe manner.
                              <br>We sincerely appreciate your promptness regarding all payments from your side. We hope that you will never fail to fulfill payment promises by regarding deadlines. Your payment has been authorized and approved. Please check </p>
                              <p style="text-align: center;" data-mce-style="text-align: center;">[payment_info]</p>
                              <p style="text-align: center;" data-mce-style="text-align: center;">We look forward to continue being in business with you in the long run.</p>
                ',
                'description' => 'This message is sent to user when his payment process is correctly done.',
            ],

            'user-created' => [
                'is_active' => 1,
                'banner'    => 1,
                'header'    => 1,
                'to_admin'  => 1,
                'footer'    => 1,
                'slug'      => 'user-created',
                'title'     => 'User Created',
                'subject'   => 'Welcome dear [customer_name]',
                'content'   => '<p>Welcome dear [customer_name] to get started using [site_name]. Thanks for reaching us!
                                 We are really happy that you are joining to our community and we wanted to inform your account has been created successfully. Please log in to your [account_link] using email address and password.</p>
                              <p>We look forward to continue being in business with you in the long run.</p>
                ',
                'description' => 'This message is sent when user registers to the system as a new account.',
            ],

            'user-confirm' => [
                'is_active' => 0,
                'banner'    => 1,
                'to_admin'  => 0,
                'header'    => 1,
                'footer'    => 1,
                'slug'      => 'user-confirm',
                'title'     => 'User Confirm',
                'subject'   => 'Hello, dear [customer_name]',
                'content'   => '<h4 data-mce-style="text-align: center;" style="text-align: center;">Thanks for signing up to [site_name]</h4>
                              <p style="text-align: center;" data-mce-style="text-align: center;">To get started, click the link below to confirm your account.</p>
                              <p style="text-align: center;" data-mce-style="text-align: center;">[confirm_button]</p>',
                'description' => 'This message is sent to a user when all his credentials have been confirmed successfully.',
            ],

            'listing-created' => [
                'is_active' => 1,
                'banner'    => 1,
                'header'    => 1,
                'to_admin'  => 1,
                'footer'    => 1,
                'slug'      => 'listing-created',
                'title'     => 'Listing Created',
                'subject'   => 'New offset [site_name]',
                'content'   => '<p style="text-align: left;" data-mce-style="text-align: left;">Hello, [customer_name]! We are glad to inform you that your listing "[listing_title]" has been created. Now your listing status is [listing_status].<br>All the best!</p>
                ',
                'description' => 'This message is sent to user when creates a new listing.',
            ],

            'listing-status-changed' => [
                'is_active' => 1,
                'banner'    => 1,
                'header'    => 1,
                'to_admin'  => 1,
                'footer'    => 1,
                'slug'      => 'listing-status-changed',
                'title'     => 'Listing Status Changed',
                'subject'   => 'New offset [site_name]',
                'content'   => '<p data-mce-style="text-align: center;" style="text-align: center;">Hello [customer_name], the purpose of this email is to notify about your update on the listing "[listing_title]\'s" status. You changed your listing status from [listing_status_before] to [listing_status_after]. Please be kindly informed that you are always free to return your changes<br>
                                <br>You can reach out to us in case of any doubt or clearance required from our side.</p>
                ',
                'description' => 'This message is sent to user when his listing status changed.',
            ],

            'listing-moderate' => [
                'is_active' => 1,
                'banner'    => 1,
                'header'    => 1,
                'to_admin'  => 1,
                'footer'    => 1,
                'slug'      => 'listing-moderate',
                'title'     => 'Listing Moderate',
                'subject'   => 'New offset [site_name]',
                'content'   => '<p>Hello [customer_name], We earnestly inform you that admin approved the status of your listing "[listing_title]". Now your listing status is changed to [listing_status]. You can begin working on that listing right now.</p>
                                <p>Thanks for choosing our product as a best of choice.<br>Sincerely</p>
                ',
                'description' => 'This message is sent to user when one of his listing approved.',
            ],

            'listing-expired' => [
                'is_active' => 1,
                'banner'    => 1,
                'header'    => 1,
                'to_admin'  => 0,
                'footer'    => 1,
                'slug'      => 'listing-expired',
                'title'     => 'Listing Expired',
                'subject'   => 'New offset [site_name]',
                'content'   => '<p style="text-align: center;" data-mce-style="text-align: center;">Hello [customer_name], We regret to inform you that one of your listings has expired. Unfortunately, the listing you bought is no longer available in your orders. 
                                <br>However, be kindly informed that you are always welcome to buy it for rebuilding.<br></p>
                                <p style="text-align: center;" data-mce-style="text-align: center;"><strong>Please check your current status: </strong></p>
                                <p style="text-align: center;" data-mce-style="text-align: center;">[listing_list]</p>
                                <p style="text-align: center;" data-mce-style="text-align: center;">We look forward to continue being in business with you in the long run.</p>
                ',
                'description' => 'This message is sent to user when one of his listings expired.',
            ],

            'feature-expired' => [
                'is_active' => 1,
                'banner'    => 1,
                'header'    => 1,
                'to_admin'  => 0,
                'footer'    => 1,
                'slug'      => 'feature-expired',
                'title'     => 'Featured Listing Expired',
                'subject'   => 'New offset [site_name]',
                'content'   => '<p style="text-align: center;" data-mce-style="text-align: center;">Hello [customer_name], We regret to inform you that your featured listing "[listing_title]" has expired. Unfortunately, the featured plan you bought is no longer available.
                                <br>However, be kindly informed that you are always welcome to buy a new featured plan.</p>
                                <p style="text-align: center;" data-mce-style="text-align: center;">[listing_list]</p>
                                <p style="text-align: center;" data-mce-style="text-align: center;">We look forward to continue being in business with you in the long run.</p>
                ',
                'description' => 'This message is sent to user when one of his featured listings expired.',
            ],

            'user-plan-expired' => [
                'is_active' => 1,
                'banner'    => 1,
                'header'    => 1,
                'to_admin'  => 0,
                'footer'    => 1,
                'slug'      => 'user-plan-expired',
                'title'     => 'User Plan Expired',
                'subject'   => 'New offset [site_name]',
                'content'   => '<p style="text-align: center;" data-mce-style="text-align: center;">Hello [customer_name], We regret to inform you that your paid plan [plan_type] has expired. Unfortunately, the plan you bought is no longer available.
                                <br>However, be kindly informed that you are always welcome to buy a new plan.</p>
                                <p style="text-align: center;" data-mce-style="text-align: center;">[payment_info]</p>
                                <p style="text-align: center;" data-mce-style="text-align: center;">We look forward to continue being in business with you in the long run.</p>
                ',
                'description' => 'This message is sent to user when his user plan expired.',
            ],
        ];
    }

    /**
     * @param array $store
     * @param array $new_data
     */
    public static function add_email_template($store, $new_data) {
        if (!empty($store) && isset($new_data['slug']) && !isset($store[$new_data['slug']])) {
            $store[$new_data['slug']] = $new_data;
            self::update_email_templates_store($store);
        }
    }

    /**
     * @param $args
     * @param $type
     * @param Bool $to_admin
     * @return boolean
     */
    public static function uListing_send_email($args, $type, $to_admin = false) {
        $sent = false;
        $email_manager = self::create_instance( $type );
        if ( ! empty( $email_manager ) ) {
            $email_manager->initial_params( $args );
            if ( ! empty( $email_manager->get_email() && $email_manager->is_active() ) ) {
                $sent = @wp_mail(
                    $email_manager->get_email(),
                    __('You have new Offer', 'ulisting'),
                    $email_manager->get_content(),
                    self::get_headers()
                );
                if ($sent && $to_admin)
                    $email_manager->send_to_admin( $args );
            }
        }
        return $sent;
    }

    public static function send_email_confirm($args, $data, $userRole, $type) {

	    $email_manager = self::create_instance( $type );
	    $args['subject'] = 'Congratulations';
	    if ( ! empty( $email_manager ) ) {
		    $email_manager->initial_params( $args );

		    if ( ! empty( $email_manager->get_email() && $email_manager->is_active() != 1 && $userRole['email_confirmation'] == 'true') ) {

			    $sent = @wp_mail(
				    $email_manager->get_email(),
				    __('You have new Offer', 'ulisting'),
				    $email_manager->get_content(),
				    self::get_headers()
			    );
		    }

	    }
	    return;
    }

    public static function get_socials() {
        return [
            'facebook'  => ['label' => 'Facebook', 'link' => ''],
            'instagram' => ['label' => 'Instagram', 'link' => ''],
            'twitter'   => ['label' => 'Twitter', 'link' => ''],
            'youtube'   => ['label' => 'Youtube', 'link' => ''],
        ];
    }

    /**
     * Email headers
     * @return string
     */
    public static function get_headers() {
        // Email Sender Name
//      $headers = 'From: No Reply <noreply@'.$_SERVER['HTTP_HOST'].'>' . "\r\n";
//      $headers .= "MIME-Version: 1.0\r\n";
        $headers = "Content-Type: text/html; charset=UTF-8\r\n";
        return $headers;
    }

    /**
     * Get All Email Templates From option
     * @return array|mixed
     */
    public static function get_email_templates_store() {
        return !empty(get_option(self::EMAIL_OPTION)) ? get_option(self::EMAIL_OPTION) : [];
    }

    /** Update Email Template by slug
     * @param $slug
     * @param $data
     */
    public static function update_by_slug($slug, $data) {
        $templates = self::get_email_templates_store();
        if (isset($templates[$slug])) {
            $templates[$slug] = $data;
            self::update_email_templates_store($templates);
        }
    }

    /**
     * Get All Email Templates From option
     * @param array $data
     */
    public static function update_email_templates_store($data) {
        if (is_array($data))
            update_option(self::EMAIL_OPTION, $data);
    }

    /**
     * @param $slug
     * @return array|null
     */
    public static function find_by_slug($slug) {
        $templates = self::get_email_templates_store();
        if (isset($templates[$slug]))
            return $templates[$slug];
        return null;
    }

    /**
     * @param $slug
     * @return string
     */
    public static function render_settings($slug) {
        $current = self::find_by_slug($slug);
        return  ulisting_render_template(EMAIL_MANAGER_PATH . '/templates/single.php', ['email' => $current]);
    }
}
