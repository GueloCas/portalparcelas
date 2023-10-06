<?php

function tolips_themer_mime_types($mimes) {
   $mimes['svg'] = 'image/svg+xml';
   return $mimes;
}
add_filter('upload_mimes', 'tolips_themer_mime_types');

function tolips_themer_share(){
   ob_start();
      require_once(GAVIAS_TOLIPS_PLUGIN_DIR . 'templates/sharebox-post.php');
   echo ob_get_clean();
}
add_action('tolips_share', 'tolips_themer_share', 1);

add_action( 'init', 'tolips_init_options', 1 );
function tolips_init_options(){
   if( empty(get_option( 'tribeEventsTemplate', '' )) ){
      update_option('tribeEventsTemplate', 'default');
   }
   if( empty(get_option( 'views_v2_enabled', '' )) ){
      update_option('views_v2_enabled', '0');
   }
}

add_shortcode( 'gv-page-content', 'tolips_themer_page_content_shortcode' );
function tolips_themer_page_content_shortcode($atts) {
   $thispage = get_page($atts["id"]);
   return do_shortcode( $thispage->post_content );
}
add_shortcode( 'gv-post-content', 'tolips_themer_post_content_shortcode' );
function tolips_themer_post_content_shortcode($atts) {
   $thispost = get_post($atts["id"]);
   return do_shortcode( $thispost->post_content );
}

// Disables the block editor from managing widgets in the Gutenberg plugin.
add_filter( 'gutenberg_use_widgets_block_editor', '__return_false' );
// Disables the block editor from managing widgets.
add_filter( 'use_widgets_block_editor', '__return_false' );

//add_filter( 'tec_events_views_v1_should_display_deprecated_notice', '__return_false' );