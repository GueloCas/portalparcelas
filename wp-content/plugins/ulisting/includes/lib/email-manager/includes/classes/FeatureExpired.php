<?php

namespace uListing\Lib\Email\Classes;

use uListing\Classes\StmListing;
use uListing\Classes\StmListingTemplate;
use uListing\Lib\Email\Classes\Basic\UlistingEmail;

class FeatureExpired extends UlistingEmail {
    /**
     * Replace all short-codes
     * @param string $type
     * @param array $args
     * @return mixed
     */
    public function replace_shortcodes($type, $args) {
        $content = $this->email_manager[$type];
        $listing = $args['listing'];
        $content = str_replace('\\\"','\"', $content);
        $content = str_replace('[customer_name]', $args['user_name'], $content);
        $content = str_replace('[listing_id]', $listing->ID, $content);
        $content = str_replace('[listing_title]', $listing->post_title, $content);
        $content = str_replace('[listing_status]', StmListing::STATUS_DRAFT, $content);
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
}