<?php
Redux::setSection( $opt_name, array(
   'title'     => esc_html__( 'Social Profiles', 'tolips' ),
   'icon'      => 'el-icon-share',
   'fields'    => array(
      array(
         'id'        => 'social_facebook',
         'type'      => 'text',
         'title'     => esc_html__( 'Facebook', 'tolips' ),
         'desc'      => esc_html__( 'Enter your Facebook profile URL.', 'tolips' ),
         'default'   => ''
      ),
      array(
         'id'        => 'social_instagram',
         'type'      => 'text',
         'title'     => esc_html__( 'Instagram', 'tolips' ),
         'desc'      => esc_html__( 'Enter your Instagram profile URL.', 'tolips' ),
         'default'   => ''
      ),
      array(
         'id'        => 'social_twitter',
         'type'      => 'text',
         'title'     => esc_html__( 'Twitter', 'tolips' ),
         'desc'      => esc_html__( 'Enter your Twitter profile URL.', 'tolips' ),
         'default'   => ''
      ),
      array(
         'id'        => 'social_googleplus',
         'type'      => 'text',
         'title'     => esc_html__( 'Google+', 'tolips' ),
         'desc'      => esc_html__( 'Enter your Google+ profile URL.', 'tolips' ),
         'default'   => ''
      ),
      array(
         'id'        => 'social_linkedin',
         'type'      => 'text',
         'title'     => esc_html__( 'LinedIn', 'tolips' ),
         'desc'      => esc_html__( 'Enter your LinkedIn profile URL.', 'tolips' ),
         'default'   => ''
      ),
      array(
         'id'        => 'social_pinterest',
         'type'      => 'text',
         'title'     => esc_html__( 'Pinterest', 'tolips' ),
         'desc'      => esc_html__( 'Enter your Pinterest profile URL.', 'tolips' ),
         'default'   => ''
      ),
      array(
         'id'        => 'social_rss',
         'type'      => 'text',
         'title'     => esc_html__( 'RSS', 'tolips' ),
         'desc'      => esc_html__( 'Enter your RSS feed URL.', 'tolips' ),
         'default'   => ''
      ),
      array(
         'id'        => 'social_tumblr',
         'type'      => 'text',
         'title'     => esc_html__( 'Tumblr', 'tolips' ),
         'desc'      => esc_html__( 'Enter your Tumblr profile URL.', 'tolips' ),
         'default'   => ''
      ),
      array(
         'id'        => 'social_vimeo',
         'type'      => 'text',
         'title'     => esc_html__( 'Vimeo', 'tolips' ),
         'desc'      => esc_html__( 'Enter your Vimeo profile URL.', 'tolips' ),
         'default'   => ''
      ),
      array(
         'id'        => 'social_youtube',
         'type'      => 'text',
         'title'     => esc_html__( 'YouTube', 'tolips' ),
         'desc'      => esc_html__( 'Enter your YouTube profile URL.', 'tolips' ),
         'default'   => ''
      )
   )
));