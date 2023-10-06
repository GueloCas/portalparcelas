<?php
/* Save custom theme styles */
if ( ! function_exists( 'tolips_custom_styles_save' ) ) :
function tolips_custom_styles_save() {

   $main_font = false;
   $main_font_enabled = ( tolips_get_option('main_font_source', 0) == 0 ) ? false : true;
   if ( $main_font_enabled ) {
      $font_main = tolips_get_option('main_font', '');
      if(isset($font_main['font-family']) && $font_main['font-family']){
         $main_font = $font_main['font-family'];
      }
   }

   $secondary_font = false;
   $secondary_font_enabled = ( tolips_get_option('secondary_font_source', 0) == 0 ) ? false : true;
   if ( $secondary_font_enabled ) {
      $font_second = tolips_get_option('secondary_font', '');
      if(isset($font_second['font-family']) && $font_second['font-family']){
         $secondary_font = $font_second['font-family'];
      }
   }

   ob_start();
?>


/* Typography */
<?php if ( $main_font_enabled && isset($main_font) && $main_font ) : ?>
body, .tooltip, .popover,
.gsc-icon-box-group.style-1 .icon-box-item-content .icon-box-item-inner .title,
.post-type-archive-tribe_events table.tribe-events-calendar tbody td .tribe-events-month-event-title,
.leaflet-popup .leaflet-popup-content-wrapper .leaflet-popup-content .gva-map-content-popup .content-inner > span,
#comments ol.comment-list > li #respond #reply-title #cancel-comment-reply-link,
.gva-countdown .countdown-times > div b
{
   font-family:<?php echo esc_attr( $main_font ); ?>,sans-serif;
}
<?php endif; ?>

<?php if ( $secondary_font_enabled && isset($secondary_font) && $secondary_font ) : ?>
h1, h2, h3, h4, h5, h6,.h1, .h2, .h3, .h4, .h5, .h6,
#wp-calendar caption,
blockquote, dl,
.text-medium,
.btn-theme, .btn, .btn-white, .btn-theme-2, .btn-theme-2 input[type*="submit"], .btn-black, input[type*="submit"]:not(.fa):not(.btn-theme), #tribe-events .tribe-events-button, .tribe-events-button, .load_more_jobs,
app-button .elementor-button-link .elementor-button-text,
.header-mobile .header-mobile-content .header-right .mobile-user .login-account .user-account .menu-item-logout a,
ul.gva-nav-menu > li > a,
.elementor-accordion .elementor-accordion-item .elementor-tab-title a span,
.elementor-widget-button a,
.milestone-block .milestone-content .milestone-number-inner,
.milestone-block.style-1 .box-content .milestone-content .milestone-text,
.milestone-block.style-1 .box-content .milestone-content .milestone-number-inner,
.milestone-block.style-2 .box-content .milestone-content .milestone-number-inner,
.milestone-block.style-2 .box-content .milestone-content .milestone-text,
.gsc-heading .sub-title,
.gsc-image-content.skin-v1 .box-content .content-inner .title,
.gva-testimonial-carousel.style-1 .testimonial-item .testimonial-content-inner .testimonial-information span.testimonial-name,
.gsc-work-process .box-content .number-text,
.gsc-listings-banner-group.style-3 .listings-banner-item-content .banner-hover .number-listings,
.gva-user .login-account .user-account .menu-item-logout a, .topbar-mobile .login-account .user-account .menu-item-logout a,
.social-networks-post > li.title-share,
.listing-block .listing-image .listing-time,
.listing-block .listing-content .content-footer .lt-avg-review,
.listing-block.job_position_featured .listing-image .lt-featured,
.my-listing-item .listing-content .listing-status,
.lt_block-category .cat-item a .cat-name,
.lt_block-category .more-cat .more-cat-number,
.lt_results-sorting .results-text,
.listing-single-content .listing-top-content .lt-content-top-left .content-right .lt-category,
.listing-single-content .listing-main-content .listing-tags .block-content a.tag-item,
.lt-reviews-text,
#submit-job-form .listing-submit-group .group-title,
#submit-job-form fieldset > label,
#submit-job-form fieldset.fieldset-type-custom-map .custom-map-field label,
#job-manager-job-dashboard .sidebar .content-inner .user-navigation ul li a,
#job-manager-job-dashboard .my-listings #popup-ajax-package .modal-content .ajax-package-form-content .package-item .checkbox-field span,
#job-manager-job-dashboard .my-packages .package-item .package-content .label,
#job-manager-job-dashboard .main-dashboard .dashboard-card .content-inner .value,
#ui-datepicker-div,
#ui-datepicker-div button,
.woocommerce-page .content-page-inner input.button, .woocommerce-page .content-page-inner a.button,
.woocommerce-cart-form__contents thead tr th,
.woocommerce-cart-form__contents .woocommerce-cart-form__cart-item td.product-name,
.woocommerce .button[type*="submit"],
.package-block .product-block-inner .package-top .package-price,
.package-block .product-block-inner .package-content .content-inner .add-to-cart a
{
   font-family:<?php echo esc_attr( $secondary_font ); ?>,sans-serif;
}
<?php endif; ?>

/* ----- Main Color ----- */
<?php if($style = tolips_get_option('main_color', '')){ ?>
   body{
      color:<?php echo esc_attr($style) ?>;
   }
<?php } ?>

/* ----- Background body ----- */
<?php 
   $main_background = tolips_get_option('main_background_image', '');
   if(isset($main_background['url']) && $main_background['url']){
?>
body{
   <?php if ( strlen( $main_background['url'] ) > 0 ) : ?>
   background-image:url("<?php echo esc_url( $main_background['url'] ); ?>");
   <?php if ( tolips_get_option('main_background_image_type', '') == 'fixed' ) : ?>
   background-attachment:fixed;
   background-size:cover;
   <?php else : ?>
   background-repeat:repeat;
   background-position:0 0;
   <?php endif; endif; ?>
   background-color:<?php echo esc_attr( tolips_get_option('main_background_color', '') ); ?>;
}
<?php } ?>

/* ----- Main content ----- */
<?php if(tolips_get_option('content_background_color', '')){ ?>
div.page {
   background: <?php echo esc_attr( tolips_get_option('content_background_color', '') ); ?>!important;
}
<?php } ?>

<?php if(tolips_get_option('content_font_color', '')){ ?>
div.page {
   color: <?php echo esc_attr( tolips_get_option('content_font_color', '') ); ?>;
}
<?php } ?>

<?php if(tolips_get_option('content_font_color_link', '')){ ?>
div.page a{
   color: <?php echo esc_attr( tolips_get_option('content_font_color_link', '') ); ?>;
}
<?php } ?>

<?php if(tolips_get_option('content_font_color_link_hover', '')){ ?>
div.page a:hover, div.page a:active, div.page a:focus {
   background: <?php echo esc_attr( tolips_get_option('content_font_color_link_hover', '') ); ?>!important;
}
<?php } ?>

/* ----- Footer content ----- */
<?php if(tolips_get_option('footer_background_color', '')){ ?>
#wp-footer {
   background: <?php echo esc_attr( tolips_get_option('footer_background_color', '') ); ?>!important;
}
<?php } ?>

<?php if(tolips_get_option('footer_font_color', '')){ ?>
#wp-footer {
   color: <?php echo esc_attr( tolips_get_option('footer_font_color', '') ); ?>;
}
<?php } ?>

<?php if(tolips_get_option('footer_font_color_link', '')){ ?>
#wp-footer a{
   color: <?php echo esc_attr( tolips_get_option('footer_font_color_link', '') ); ?>;
}
<?php } ?>

<?php if(tolips_get_option('footer_font_color_link_hover', '')){ ?>
#wp-footer a:hover, #wp-footer a:active, #wp-footer a:focus {
   background: <?php echo esc_attr( tolips_get_option('footer_font_color_link_hover', '') ); ?>!important;
}
<?php } ?>

/* ----- Breacrumb Style ----- */

<?php
   $styles = ob_get_clean();
   
    $styles = preg_replace( '!/\*[^*]*\*+([^/][^*]*\*+)*/!', '', $styles );
   
   $styles = str_replace( array( "\r\n", "\r", "\n", "\t", '  ', '   ', '    ' ), '', $styles );
      
   update_option( 'tolips_theme_custom_styles', $styles, true );
}
endif;

add_action( 'redux/options/tolips_theme_options/saved', 'tolips_custom_styles_save' );


/* Make sure custom theme styles are saved */
function tolips_custom_styles_install() {
   if ( ! get_option( 'tolips_theme_custom_styles' ) && get_option( 'tolips_theme_options' ) ) {
      tolips_custom_styles_save();
   }
}

add_action( 'redux/options/tolips_theme_options/register', 'tolips_custom_styles_install' );
