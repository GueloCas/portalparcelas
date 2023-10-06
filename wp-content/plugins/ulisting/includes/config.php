<?php
function uListing_plugin_new_blog() {
    if ( defined('ULISTING_VERSION') && empty(get_option( 'uListing_inserted_blog' )) ) {
        ulisting_listing_plugin_create_table();
        update_option('uListing_inserted_blog', 1);
    }
}

add_action( 'init', 'uListing_plugin_new_blog', 1 );