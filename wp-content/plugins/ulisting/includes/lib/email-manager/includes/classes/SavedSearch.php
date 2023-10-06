<?php

namespace uListing\Lib\Email\Classes;

use uListing\Classes\StmListingTemplate;
use uListing\Lib\Email\Classes\Basic\UlistingEmail;

class SavedSearch extends UlistingEmail {
    /**
     * Replace all short-codes
     * @param $type
     * @param $args
     * @return mixed
     */
    public function replace_shortcodes($type, $args) {
        $content = $this->email_manager[$type];
        $content = str_replace("\\\"","\"",$content);
        $content = str_replace("[count]", $args['listing_count'], $content);
        $content = str_replace("[customer_name]",$args['user_name'], $content);
        $content = str_replace('[site_name]', $this->parse_component('site-name', []), $content);
        $content = str_replace(
            "[listing_list]",
            StmListingTemplate::load_template( 'email/saved-searches/notification-saved-searches-listing-list', [
                'search'        => $args['search'],
                'listings'      => $args['listings'], ])
            , $content);
        return $content;
    }

    public function get_content() {
        return $this->content;
    }
}