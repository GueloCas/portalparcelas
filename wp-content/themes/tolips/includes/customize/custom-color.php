<?php
function tolips_custom_color_theme(){
   $theme_color = tolips_get_option('color_theme', '');
   $theme_color_second = tolips_get_option('color_theme_second', '');
   
   ob_start();
   /* ----- Style Color Theme ----- */
?>

<?php //------ Theme Color ------ ?>
<?php if( !empty($theme_color) ){ ?>
   .btn-link:hover {
      color: <?php echo esc_attr($theme_color) ?>;
   }
   .page-link:hover {
      color: <?php echo esc_attr($theme_color) ?>;
   }
   a:hover {
      color: <?php echo esc_attr($theme_color) ?>;
   }
   .page-links > a:hover, .page-links > span:not(.page-links-title):hover {
      border-color: <?php echo esc_attr($theme_color) ?>;
   }
   .page-links > span:not(.page-links-title) {
      border: 1px solid <?php echo esc_attr($theme_color) ?>;
   }
   .page-links .post-page-numbers:hover {
      border-color: <?php echo esc_attr($theme_color) ?>;
   }
   .page-links span.post-page-numbers {
      border-color: <?php echo esc_attr($theme_color) ?>;
   }
   blockquote {
      border-left: 2px solid <?php echo esc_attr($theme_color) ?> !important;
   }
   blockquote:before {
      color: <?php echo esc_attr($theme_color) ?>;
   }
   blockquote cite:before {
      background-color: <?php echo esc_attr($theme_color) ?>;
   }
   .wp-block-pullquote.is-style-solid-color {
      border-left: 2px solid <?php echo esc_attr($theme_color) ?> !important;
   }
   ul.feature-list > li:after, ul.list-style-1 > li:after {
      color: <?php echo esc_attr($theme_color) ?>;
   }
   ul.list-style-2 > li {
      color: <?php echo esc_attr($theme_color) ?>;
   }
   .pager .paginations a:hover {
      color: <?php echo esc_attr($theme_color) ?>;
      border-color: <?php echo esc_attr($theme_color) ?>;
   }
   .pager .paginations a.active {
      background: <?php echo esc_attr($theme_color) ?>;
      border-color: <?php echo esc_attr($theme_color) ?>;
   }
   .bg-theme {
      background: <?php echo esc_attr($theme_color) ?> !important;
   }
   .bg-theme-2 {
      background: <?php echo esc_attr($theme_color) ?> !important;
   }
   .text-theme {
      color: <?php echo esc_attr($theme_color) ?> !important;
   }
   .hover-color-theme a:hover {
      color: <?php echo esc_attr($theme_color) ?> !important;
   }
   .hover-color-theme-2 a:hover {
      color: <?php echo esc_attr($theme_color) ?> !important;
   }
   .btn, .btn-theme, .btn, .btn-white, .btn-theme-2, .btn-theme-2 input[type*="submit"], .btn-black, input[type*="submit"]:not(.fa):not(.btn-theme), #tribe-events .tribe-events-button, .tribe-events-button, .load_more_jobs {
      background: <?php echo esc_attr($theme_color) ?>;
   }
   .btn-gray-icon:hover, .btn-white-icon:hover {
      background: <?php echo esc_attr($theme_color) ?>;
   }
   .btn-gray-icon.bg-theme, .btn-white-icon.bg-theme {
      background: <?php echo esc_attr($theme_color) ?>;
   }
   .btn-theme-2 {
      background: <?php echo esc_attr($theme_color) ?>;
   }
   #tribe-events .tribe-events-button:hover, .tribe-events-button:hover {
      background: <?php echo esc_attr($theme_color) ?>;
   }
   .btn-inline {
      color: <?php echo esc_attr($theme_color) ?>;
   }
   .socials a i {
      background: <?php echo esc_attr($theme_color) ?>;
   }
   .socials-2 li a i:hover {
      color: <?php echo esc_attr($theme_color) ?>;
   }
   .alert-danger {
      background: <?php echo esc_attr($theme_color) ?>;
   }
   .desc-slider a {
      color: <?php echo esc_attr($theme_color) ?>;
   }
   .header-mobile .topbar-mobile .topbar-right > .content-inner .mobile-user .login-popup .sign-in-link .icon {
      color: <?php echo esc_attr($theme_color) ?>;
   }
   .header-mobile .topbar-mobile .topbar-right > .content-inner .mobile-user .login-account.open-menu .profile .name {
      color: <?php echo esc_attr($theme_color) ?>;
   }
   .header-mobile .header-mobile-content .mini-cart-header a.mini-cart .mini-cart-items {
      background: <?php echo esc_attr($theme_color) ?>;
   }
   .header-default .header-bottom .header-bottom-inner .gsc-search-box .control-search:hover svg {
      fill: <?php echo esc_attr($theme_color) ?>;
   }
   ul.gva-nav-menu > li .submenu-inner li a:hover, ul.gva-nav-menu > li .submenu-inner li a:focus, ul.gva-nav-menu > li .submenu-inner li a:active, ul.gva-nav-menu > li ul.submenu-inner li a:hover, ul.gva-nav-menu > li ul.submenu-inner li a:focus, ul.gva-nav-menu > li ul.submenu-inner li a:active {
      color: <?php echo esc_attr($theme_color) ?>;
   }
   .gavias-off-canvas-toggle {
      background: <?php echo esc_attr($theme_color) ?>;
   }
   #gavias-off-canvas .off-canvas-top .top-social > a:hover {
      background: <?php echo esc_attr($theme_color) ?>;
      border-color: <?php echo esc_attr($theme_color) ?>;
   }
   #gavias-off-canvas .off-canvas-top .gavias-off-canvas-close:hover {
      background: <?php echo esc_attr($theme_color) ?>;
   }
   #gavias-off-canvas ul#menu-main-menu > li > a.active > a {
      color: <?php echo esc_attr($theme_color) ?>;
   }
   #gavias-off-canvas ul#menu-main-menu > li .submenu-inner.dropdown-menu li a:hover, #gavias-off-canvas ul#menu-main-menu > li .submenu-inner.dropdown-menu li a:focus {
      color: <?php echo esc_attr($theme_color) ?>;
   }
   #gavias-off-canvas ul#menu-main-menu > li .submenu-inner.dropdown-menu li.active > a {
      color: <?php echo esc_attr($theme_color) ?>;
   }
   .gva-offcanvas-content a:hover {
      color: <?php echo esc_attr($theme_color) ?>;
   }
   .gva-offcanvas-content .close-canvas a:hover {
      color: <?php echo esc_attr($theme_color) ?>;
   }
   .gva-offcanvas-content #gva-mobile-menu ul.gva-mobile-menu > li a:hover {
      color: <?php echo esc_attr($theme_color) ?>;
   }
   .gva-offcanvas-content #gva-mobile-menu ul.gva-mobile-menu > li.menu-item-has-children .caret:hover {
      color: <?php echo esc_attr($theme_color) ?>;
   }
   .gva-offcanvas-content #gva-mobile-menu ul.gva-mobile-menu > li ul.submenu-inner li a:hover, .gva-offcanvas-content #gva-mobile-menu ul.gva-mobile-menu > li div.submenu-inner li a:hover {
      color: <?php echo esc_attr($theme_color) ?>;
   }
   .megamenu-main .widget.widget-html ul li strong {
      color: <?php echo esc_attr($theme_color) ?>;
   }
   .elementor-sidebar-widget .title:before {
      background: <?php echo esc_attr($theme_color) ?>;
   }
   .bg-row-theme, .bg-col-theme > .elementor-column-wrap {
      background-color: <?php echo esc_attr($theme_color) ?>;
   }
   .row-style-1 > div.elementor-container {
      border-top: 10px solid <?php echo esc_attr($theme_color) ?>;
   }
   .row-style-1 > div.elementor-container:after {
      border-top: 10px solid <?php echo esc_attr($theme_color) ?>;
   }
   .col-bg-theme-inner > .elementor-column-wrap > .elementor-widget-wrap {
      background: <?php echo esc_attr($theme_color) ?>;
   }
   .gva-social-links.style-v2 ul.socials > li > a:hover {
      color: <?php echo esc_attr($theme_color) ?>;
   }
   .gsc-team .team-position {
      color: <?php echo esc_attr($theme_color) ?>;
   }
   .gsc-team.team-horizontal .team-header .social-list a:hover {
      color: <?php echo esc_attr($theme_color) ?> !important;
   }
   .gsc-team.team-horizontal .team-name:after {
      background: <?php echo esc_attr($theme_color) ?>;
   }
   .gsc-team.team-vertical .team-body .info {
      background: <?php echo esc_attr($theme_color) ?>;
   }
   .post-small .post .cat-links a {
      color: <?php echo esc_attr($theme_color) ?>;
   }
   .elementor-accordion .elementor-accordion-item .elementor-tab-title a span {
      color: <?php echo esc_attr($theme_color) ?>;
   }
   .gsc-career .box-content .job-type {
      background: <?php echo esc_attr($theme_color) ?>;
   }
   .gva-hover-box-carousel .hover-box-item .box-content .box-icon {
      background: <?php echo esc_attr($theme_color) ?>;
   }
   .gsc-countdown {
      background: <?php echo esc_attr($theme_color) ?>;
   }
   .gsc-icon-box .highlight-icon .icon-container {
      color: <?php echo esc_attr($theme_color) ?>;
   }
   .gsc-icon-box-group.style-1 .icon-box-item-content .icon-box-item-inner .box-icon i {
      color: <?php echo esc_attr($theme_color) ?>;
   }
   .gsc-icon-box-group.style-1 .icon-box-item-content .icon-box-item-inner .box-icon svg {
      fill: <?php echo esc_attr($theme_color) ?>;
   }
   .gsc-icon-box-group.layout-carousel.style-1 .owl-stage-outer .owl-item.center .icon-box-item-content .icon-box-item-inner {
      border-bottom-color: <?php echo esc_attr($theme_color) ?>;
   }
   .gsc-icon-box-styles.style-1 .icon-box-content .box-icon i, .gsc-icon-box-styles.style-1 .icon-box-content .box-icon svg {
      color: <?php echo esc_attr($theme_color) ?>;
      fill: <?php echo esc_attr($theme_color) ?>;
   }
   .gsc-icon-box-styles.style-1:hover .icon-box-content, .gsc-icon-box-styles.style-1.active .icon-box-content {
      background: <?php echo esc_attr($theme_color) ?>;
   }
   .gsc-icon-box-styles.style-2 .box-icon:after {
      background: <?php echo esc_attr($theme_color) ?>;
   }
   .gsc-icon-box-styles.style-2 .box-icon .box-icon-inner i, .gsc-icon-box-styles.style-2 .box-icon .box-icon-inner svg {
      color: <?php echo esc_attr($theme_color) ?>;
      fill: <?php echo esc_attr($theme_color) ?>;
   }
   .gsc-icon-box-styles:hover .icon-inner .box-icon svg, .gsc-icon-box-styles:hover .icon-inner .box-icon i, .gsc-icon-box-styles.active .icon-inner .box-icon svg, .gsc-icon-box-styles.active .icon-inner .box-icon i{
      color: #fff;
      fill: #fff;
   }
   .gsc-icon-box-styles.style-3 .box-icon:after {
      background: <?php echo esc_attr($theme_color) ?>;
   }
   .gsc-icon-box-styles.style-3 .box-icon .box-icon-inner i, .gsc-icon-box-styles.style-3 .box-icon .box-icon-inner svg {
      color: <?php echo esc_attr($theme_color) ?>;
      fill: <?php echo esc_attr($theme_color) ?>;
   }
   .milestone-block.style-1 .box-content .milestone-icon .icon:after {
      background: <?php echo esc_attr($theme_color) ?>;
   }
   .milestone-block.style-2 .box-content .milestone-icon .icon {
      color: <?php echo esc_attr($theme_color) ?>;
   }
   .milestone-block.style-2 .box-content .milestone-icon .icon svg {
      fill: <?php echo esc_attr($theme_color) ?>;
   }
   .milestone-block.style-3 .box-content {
      background: <?php echo esc_attr($theme_color) ?>;
   }
   .gallery-post a.zoomGallery {
      background: <?php echo esc_attr($theme_color) ?>;
   }
   .gallery-post a.zoomGallery:after {
      background: <?php echo esc_attr($theme_color) ?>;
   }
   .gsc-heading .heading-video .video-link {
      background: <?php echo esc_attr($theme_color) ?>;
   }
   .gsc-heading .title strong {
      color: <?php echo esc_attr($theme_color) ?>;
   }
   .gsc-heading.style-2 .content-inner .title :before {
      background: <?php echo esc_attr($theme_color) ?>;
   }
   .gsc-image-content.skin-v1 .image .number-text {
      background: <?php echo esc_attr($theme_color) ?>;
   }
   .gsc-image-content.skin-v2 .box-content .read-more svg {
      fill: <?php echo esc_attr($theme_color) ?>;
   }
   .gsc-image-content.skin-v3 .box-content {
      background: <?php echo esc_attr($theme_color) ?>;
   }
   .gsc-image-content.skin-v4 .box-content .read-more svg {
      fill: <?php echo esc_attr($theme_color) ?>;
   }
   .gva-posts-grid .posts-grid-filter ul.nav-tabs > li > a.active {
      color: <?php echo esc_attr($theme_color) ?>;
   }
   .gva-testimonial-carousel.style-1 .testimonial-item:hover .testimonial-image {
      border-color: <?php echo esc_attr($theme_color) ?>;
   }
   .gva-testimonial-carousel.style-1 .testimonial-item:hover .testimonial-content-inner .quote-icon {
      color: <?php echo esc_attr($theme_color) ?>;
   }
   .gva-testimonial-carousel.style-1 .owl-item.first .testimonial-image {
      border-color: <?php echo esc_attr($theme_color) ?>;
   }
   .gva-testimonial-carousel.style-1 .owl-item.first .testimonial-content-inner .quote-icon {
      color: <?php echo esc_attr($theme_color) ?>;
   }
   .gva-testimonial-carousel.style-2 .testimonial-item .testimonial-item-content .testimonial-information .icon {
      color: <?php echo esc_attr($theme_color) ?>;
   }
   .gva-testimonial-carousel.style-2 .testimonial-item .testimonial-item-content:hover .icon-quote {
      background: <?php echo esc_attr($theme_color) ?>;
   }
   .gva-testimonial-carousel.style-3 .testimonial-item .testimonial-item-content:hover .icon-quote {
      background: <?php echo esc_attr($theme_color) ?>;
   }
   .gsc-video-box.style-1 .video-inner .video-content .video-action .popup-video {
      color: <?php echo esc_attr($theme_color) ?>;
   }
   .gva-video-carousel .video-item-inner .video-link {
      background: <?php echo esc_attr($theme_color) ?>;
   }
   .gsc-pricing.style-1 .content-inner .plan-price .price-value {
      color: <?php echo esc_attr($theme_color) ?>;
   }
   .gsc-pricing.style-1 .content-inner .plan-price .interval {
      color: <?php echo esc_attr($theme_color) ?>;
   }
   .gsc-pricing.style-1 .content-inner .plan-list li .icon {
      color: <?php echo esc_attr($theme_color) ?>;
   }
   .gsc-pricing.style-2 .content-inner .price-meta .plan-price .price-value {
      color: <?php echo esc_attr($theme_color) ?>;
   }
   .gsc-pricing.style-2 .content-inner .price-meta .plan-price .interval {
      color: <?php echo esc_attr($theme_color) ?>;
   }
   .gva-socials ul.social-links li a {
      background: <?php echo esc_attr($theme_color) ?>;
   }
   .gsc-tabs-content .nav_tabs > li a.active {
      background: <?php echo esc_attr($theme_color) ?>;
   }
   .gsc-tabs-content .nav_tabs > li a.active:after {
      background: <?php echo esc_attr($theme_color) ?>;
   }
   .gsc-tabs-content .tab-content .tab-pane .tab-content-item ul > li:after {
      color: <?php echo esc_attr($theme_color) ?>;
   }
   .gsc-work-process .box-content .number-text {
      background: <?php echo esc_attr($theme_color) ?>;
   }
   .gsc-work-process:hover .box-content .number-text, .gsc-work-process.active .box-content .number-text {
      background: <?php echo esc_attr($theme_color) ?>;
   }
   .gsc-listings-banner-group.style-3 .listings-banner-item-content .banner-hover .number-listings {
      background: <?php echo esc_attr($theme_color) ?>;
   }
   .gva-user .login-account .profile:hover, .topbar-mobile .login-account .profile:hover {
      color: <?php echo esc_attr($theme_color) ?>;
   }
   .gva-user .login-account .user-account .menu-item-logout a:hover, .topbar-mobile .login-account .user-account .menu-item-logout a:hover {
      color: <?php echo esc_attr($theme_color) ?>;
   }
   .gva-user .login-register a .icon, .topbar-mobile .login-register a .icon {
      color: <?php echo esc_attr($theme_color) ?>;
   }
   .gva-user .login-register a:hover, .topbar-mobile .login-register a:hover {
      color: <?php echo esc_attr($theme_color) ?>;
   }
   .user-agent-block.user-agency .user-content .user-meta .user-info .icon {
      color: <?php echo esc_attr($theme_color) ?>;
   }
   .user-agent-block.user-agent .user-content .btn-author-detail {
      background: <?php echo esc_attr($theme_color) ?>;
   }
   .widget .widget-title:after, .widget .widgettitle:after, .widget .wpb_singleimage_heading:after, .wpb_single_image .widget-title:after, .wpb_single_image .widgettitle:after, .wpb_single_image .wpb_singleimage_heading:after, .wpb_content_element .widget-title:after, .wpb_content_element .widgettitle:after, .wpb_content_element .wpb_singleimage_heading:after {
      background: <?php echo esc_attr($theme_color) ?>;
   }
   .color-theme .widget-title, .color-theme .widgettitle {
      color: <?php echo esc_attr($theme_color) ?> !important;
   }
   .wp-sidebar ul li a:hover, .elementor-widget-sidebar ul li a:hover {
      color: <?php echo esc_attr($theme_color) ?>;
   }
   .wp-sidebar .post-author, .wp-sidebar .post-date, .elementor-widget-sidebar .post-author, .elementor-widget-sidebar .post-date {
      color: <?php echo esc_attr($theme_color) ?>;
   }
   #wp-footer .footer-widgets .widget_categories a:hover, #wp-footer .footer-widgets .widget_archive a:hover, #wp-footer .footer-widgets .wp-sidebar .widget_nav_menu a:hover, #wp-footer .footer-widgets #wp-footer .widget_nav_menu a:hover, #wp-footer .footer-widgets .elementor-widget-sidebar .widget_nav_menu a:hover, #wp-footer .footer-widgets .widget_pages a:hover, #wp-footer .footer-widgets .widget_meta a:hover {
      color: <?php echo esc_attr($theme_color) ?> !important;
   }
   #wp-footer .footer-widgets .widget_tag_cloud .tagcloud > a:hover {
      border-color: <?php echo esc_attr($theme_color) ?>;
   }
   .widget_calendar .wp-calendar-table td a {
      color: <?php echo esc_attr($theme_color) ?>;
   }
   .widget_calendar .wp-calendar-table #today {
      color: <?php echo esc_attr($theme_color) ?>;
   }
   .widget_calendar .wp-calendar-table #today:after {
      background: <?php echo esc_attr($theme_color) ?>;
   }
   .widget_tag_cloud .tagcloud > a:hover {
      background: <?php echo esc_attr($theme_color) ?>;
   }
   .widget_categories ul > li > a:hover, .widget_archive ul > li > a:hover, .wp-sidebar .widget_nav_menu ul > li > a:hover, #wp-footer .widget_nav_menu ul > li > a:hover, .elementor-widget-sidebar .widget_nav_menu ul > li > a:hover, .widget_pages ul > li > a:hover, .widget_meta ul > li > a:hover {
      color: <?php echo esc_attr($theme_color) ?>;
   }
   .widget_categories ul > li.current_page_item > a, .widget_archive ul > li.current_page_item > a, .wp-sidebar .widget_nav_menu ul > li.current_page_item > a, #wp-footer .widget_nav_menu ul > li.current_page_item > a, .elementor-widget-sidebar .widget_nav_menu ul > li.current_page_item > a, .widget_pages ul > li.current_page_item > a, .widget_meta ul > li.current_page_item > a {
      color: <?php echo esc_attr($theme_color) ?>;
   }
   .widget_rss ul > li a .post-date, .widget_recent_entries ul > li a .post-date, .gva_widget_recent_entries ul > li a .post-date {
      color: <?php echo esc_attr($theme_color) ?>;
   }
   .gva_widget_recent_entries ul li .post-content .post-comments .icon {
      color: <?php echo esc_attr($theme_color) ?>;
   }
   .widget_rss > ul li .rss-date {
      color: <?php echo esc_attr($theme_color) ?>;
   }
   .opening-time .phone {
      color: <?php echo esc_attr($theme_color) ?>;
   }
   .widget_gva_give_categories_widget ul.categories-listing li:hover {
      color: <?php echo esc_attr($theme_color) ?>;
   }
   .support-box {
      background: <?php echo esc_attr($theme_color) ?>;
   }
   .download-box a:hover {
      color: <?php echo esc_attr($theme_color) ?>;
   }
   .modal-ajax-user-form .close {
      background: <?php echo esc_attr($theme_color) ?>;
   }
   .modal-ajax-user-form .ajax-user-form .title:after {
      background: <?php echo esc_attr($theme_color) ?>;
   }
   .modal-ajax-user-form .ajax-user-form .lost-password a, .modal-ajax-user-form .ajax-user-form .user-registration a {
      color: <?php echo esc_attr($theme_color) ?>;
   }
   .modal-ajax-user-form .ajax-form-content.ajax-preload .form-action:after {
      border: 1px solid <?php echo esc_attr($theme_color) ?>;
   }
   .user-wishlist .wishlist-link:hover .wishlist-icon i {
      color: <?php echo esc_attr($theme_color) ?>;
   }
   .user-wishlist .wishlist-link:hover .wishlist-icon svg {
      fill: <?php echo esc_attr($theme_color) ?>;
   }
   .post .entry-content .entry-date {
      background: <?php echo esc_attr($theme_color) ?>;
   }
   .post .entry-meta .meta-inline > span i {
      color: <?php echo esc_attr($theme_color) ?>;
   }
   .post .tag-links > a:hover {
      background: <?php echo esc_attr($theme_color) ?>;
   }
   .post-style-2 .entry-content .entry-meta .left {
      border: 2px solid <?php echo esc_attr($theme_color) ?>;
   }
   .post-style-2 .entry-content .entry-meta .right i {
      color: <?php echo esc_attr($theme_color) ?>;
   }
   .post-style-2 .entry-content .read-more .icon {
      color: <?php echo esc_attr($theme_color) ?>;
   }
   .post-style-2:hover .entry-content .entry-meta .left {
      border-color: <?php echo esc_attr($theme_color) ?>;
   }
   .single.single-post #wp-content > article.post .entry-content .cat-links i {
      color: <?php echo esc_attr($theme_color) ?>;
   }
   .single.single-post #wp-content > article.post .entry-content .cat-links a:hover {
      color: <?php echo esc_attr($theme_color) ?>;
   }
   .single.single-post #wp-content > article.post .entry-content .post-content input[type="submit"] {
      background: <?php echo esc_attr($theme_color) ?>;
   }
   .post-nav-links span:hover, .post-nav-links a:hover {
      color: <?php echo esc_attr($theme_color) ?>;
      border-color: <?php echo esc_attr($theme_color) ?>;
   }
   .post-nav-links span.active, .post-nav-links span.current, .post-nav-links a.active, .post-nav-links a.current {
      background: <?php echo esc_attr($theme_color) ?>;
      border-color: <?php echo esc_attr($theme_color) ?>;
   }
   .social-networks-post > li:not(.title-share) a:hover {
      background: <?php echo esc_attr($theme_color) ?>;
      border-color: <?php echo esc_attr($theme_color) ?>;
   }
   .post-navigation a:hover {
      background: <?php echo esc_attr($theme_color) ?>;
   }
   .tribe-event-list-block .tribe-event-left .content-inner .tribe-start-date {
      background: <?php echo esc_attr($theme_color) ?>;
   }
   .tribe-event-list-block .tribe-event-right .content-inner .tribe-events-event-meta .icon {
      color: <?php echo esc_attr($theme_color) ?>;
   }
   .tribe-event-list-block.v2 .event-action a {
      background: <?php echo esc_attr($theme_color) ?> !important;
   }
   .tribe-events .tribe-events-c-ical__link {
      border-color: <?php echo esc_attr($theme_color) ?>;
      color: <?php echo esc_attr($theme_color) ?>;
   }
   .tribe-events .tribe-events-c-ical__link:hover, .tribe-events .tribe-events-c-ical__link:active, .tribe-events .tribe-events-c-ical__link:focus {
      background-color: <?php echo esc_attr($theme_color) ?>;
   }
   .tribe-common .tribe-common-c-btn, .tribe-common a.tribe-common-c-btn {
      background: <?php echo esc_attr($theme_color) ?>;
   }
   .tribe-common .tribe-common-c-btn:hover, .tribe-common .tribe-common-c-btn:active, .tribe-common .tribe-common-c-btn:focus, .tribe-common a.tribe-common-c-btn:hover, .tribe-common a.tribe-common-c-btn:active, .tribe-common a.tribe-common-c-btn:focus {
      background: <?php echo esc_attr($theme_color) ?>;
   }
   .tribe-events-single .tribe-events-schedule .icon {
      color: <?php echo esc_attr($theme_color) ?>;
   }
   .tribe-events-single .tribe-events-event-meta .tribe-event-single-detail .tribe-event-single-meta-detail > div .icon {
      color: <?php echo esc_attr($theme_color) ?>;
   }
   .tribe-events-single .tribe-events-event-meta .tribe-event-meta-bottom .event-single-organizer > .content-inner .meta-item .icon svg {
      fill: <?php echo esc_attr($theme_color) ?>;
   }
   .tribe-events-single .tribe-events-event-meta .tribe-event-meta-bottom .event-single-venue > .content-inner {
      background: <?php echo esc_attr($theme_color) ?>;
   }
   .tribe-events-single .tribe-events-event-meta .tribe-event-meta-bottom .event-single-venue > .content-inner:after {
      background: <?php echo esc_attr($theme_color) ?>;
   }
   .post-type-archive-tribe_events #tribe-events-bar #tribe-bar-form .tribe-bar-submit .tribe-events-button {
      background: <?php echo esc_attr($theme_color) ?>;
   }
   .post-type-archive-tribe_events table.tribe-events-calendar tbody td .tribe-events-tooltip .tribe-events-event-body .tribe-event-duration {
      color: <?php echo esc_attr($theme_color) ?>;
   }
   .post-type-archive-tribe_events table.tribe-events-calendar tbody td:hover {
      border-bottom: 2px solid <?php echo esc_attr($theme_color) ?> !important;
   }
   .team-progress-wrapper .team__progress .team__progress-bar {
      background: <?php echo esc_attr($theme_color) ?>;
   }
   .team-progress-wrapper .team__progress .team__progress-bar .percentage {
      background: <?php echo esc_attr($theme_color) ?>;
   }
   .team-progress-wrapper .team__progress .team__progress-bar .percentage:after {
      border-top-color: <?php echo esc_attr($theme_color) ?>;
   }
   .team-block.team-v1 .team-content .socials-team a:hover {
      background: <?php echo esc_attr($theme_color) ?>;
   }
   .team-block.team-v1:hover .team-content {
      border-bottom-color: <?php echo esc_attr($theme_color) ?>;
   }
   .owl-item.center .team-block .team-content {
      border-bottom-color: <?php echo esc_attr($theme_color) ?>;
   }
   .team-block-single .heading:after, .team-block-single .heading-contact:after {
      background: <?php echo esc_attr($theme_color) ?>;
   }
   .team-block-single .team-quote:after {
      color: <?php echo esc_attr($theme_color) ?>;
   }
   .team-block-single .socials-team a:hover {
      background: <?php echo esc_attr($theme_color) ?>;
      border-color: <?php echo esc_attr($theme_color) ?>;
   }
   .pagination .disabled {
      background: <?php echo esc_attr($theme_color) ?>;
   }
   .pagination .current {
      background: <?php echo esc_attr($theme_color) ?>;
   }
   .not-found-wrapper .not-found-home > a {
      background: <?php echo esc_attr($theme_color) ?>;
   }
   .not-found-wrapper .not-found-home > a:hover, .not-found-wrapper .not-found-home > a:active, .not-found-wrapper .not-found-home > a:after {
      background: <?php echo esc_attr($theme_color) ?>;
   }
   .content-page-index .post-masonry-index .post.sticky .entry-content:after {
      color: <?php echo esc_attr($theme_color) ?>;
   }
   .wpcf7-form select {
      background: <?php echo esc_attr($theme_color) ?>;
   }
   #comments .comments-title:after {
      background: <?php echo esc_attr($theme_color) ?>;
   }
   #comments #add_review_button,
    #comments #submit {
      background: <?php echo esc_attr($theme_color) ?>;
   }
   #comments #reply-title {
      color: <?php echo esc_attr($theme_color) ?>;
   }
   #comments ol.comment-list .the-comment .comment-rate .on {
      color: <?php echo esc_attr($theme_color) ?>;
   }
   #comments ol.comment-list .the-comment .comment-info:after {
      background: <?php echo esc_attr($theme_color) ?>;
   }
   #comments ol.comment-list .the-comment .comment-info a:hover {
      color: <?php echo esc_attr($theme_color) ?>;
   }
   #comments ol.comment-list .the-comment .comment-review-avg {
      color: <?php echo esc_attr($theme_color) ?>;
   }
   #comments ol.comment-list .the-comment .comment-action-wrap a {
      color: <?php echo esc_attr($theme_color) ?>;
   }
   .comment-rating .comment-star-rating > li a.active {
      color: <?php echo esc_attr($theme_color) ?>;
   }
   .comment-with-review #lt-comment-reviews .comment-reviews-content .avg-total-tmp span.value {
      color: <?php echo esc_attr($theme_color) ?>;
   }
   .cld-like-dislike-wrap .cld-like-wrap {
      color: <?php echo esc_attr($theme_color) ?>;
   }
   .cld-like-dislike-wrap .cld-like-wrap a {
      color: <?php echo esc_attr($theme_color) ?>;
   }
   .pretty input:checked ~ .state.p-success label:after, .pretty.p-toggle .state.p-success label:after {
      background-color: <?php echo esc_attr($theme_color) ?> !important;
   }
   .pretty input:checked ~ .state.p-success-o label:before, .pretty.p-toggle .state.p-success-o label:before {
      border-color: <?php echo esc_attr($theme_color) ?>;
   }
   .pretty input:checked ~ .state.p-success-o .icon, .pretty input:checked ~ .state.p-success-o .svg, .pretty input:checked ~ .state.p-success-o svg, .pretty.p-toggle .state.p-success-o .icon, .pretty.p-toggle .state.p-success-o .svg, .pretty.p-toggle .state.p-success-o svg {
      color: <?php echo esc_attr($theme_color) ?>;
      stroke: <?php echo esc_attr($theme_color) ?>;
   }
   .pretty.p-default:not(.p-fill) input:checked ~ .state.p-success-o label:after {
      background-color: <?php echo esc_attr($theme_color) ?> !important;
   }
   .pretty.p-switch input:checked ~ .state.p-success:before {
      border-color: <?php echo esc_attr($theme_color) ?>;
   }
   .pretty.p-switch.p-fill input:checked ~ .state.p-success:before {
      background-color: <?php echo esc_attr($theme_color) ?> !important;
   }
   .owl-carousel .owl-nav > div:hover, .owl-carousel .owl-nav > div:focus, .flex-control-nav .owl-nav > div:hover, .flex-control-nav .owl-nav > div:focus, .ctf-tweets .owl-nav > div:hover, .ctf-tweets .owl-nav > div:focus {
      background: <?php echo esc_attr($theme_color) ?>;
   }
   .owl-carousel .owl-dots .owl-dot.active, .flex-control-nav .owl-dots .owl-dot.active, .ctf-tweets .owl-dots .owl-dot.active {
      background: <?php echo esc_attr($theme_color) ?>;
   }
   ul.nav-tabs > li > a:hover, ul.nav-tabs > li > a:focus, ul.nav-tabs > li > a:active {
      color: <?php echo esc_attr($theme_color) ?>;
   }
   ul.nav-tabs > li.active > a {
      background: <?php echo esc_attr($theme_color) ?>;
   }
   .select2-container .select2-dropdown ul.select2-results__options li.select2-results__option--highlighted {
      color: <?php echo esc_attr($theme_color) ?>;
   }
   .select2-container .select2-dropdown ul.select2-results__options li[aria-selected="true"] {
      color: <?php echo esc_attr($theme_color) ?>;
   }
   .select2-container .select2-selection .select2-selection__rendered .select2-selection__clear {
      background: <?php echo esc_attr($theme_color) ?>;
   }
   .select2-selection.select2-selection--multiple .select2-selection__rendered li.select2-selection__choice .select2-selection__choice__remove {
      background: <?php echo esc_attr($theme_color) ?>;
   }
   #ui-datepicker-div button {
      background: <?php echo esc_attr($theme_color) ?>;
   }
   #ui-datepicker-div .ui-widget-header {
      background: <?php echo esc_attr($theme_color) ?>;
   }

   #uListingMainMap .stm-button:hover {
      color: <?php echo esc_attr($theme_color) ?>;
   }
   #uListing-map-right .selected-map-type,
    #uListing-map-right ul li:hover {
      color: <?php echo esc_attr($theme_color) ?>;
   }
   .ulisting_my_listing .listing-button_box .listing-status-box .listing-status-name ul li .status-actions span:hover {
      color: <?php echo esc_attr($theme_color) ?>;
   }
   .ulisting_my_listing .listing-button_box .listing-status-box .listing-status-name ul li .edit-listing span:hover {
      color: <?php echo esc_attr($theme_color) ?>;
   }
   #uListingMainMap .cluster div {
      background-color: <?php echo esc_attr($theme_color) ?>;
   }
   .stm-listing-pagination ul.pagination > li a:hover {
      background: <?php echo esc_attr($theme_color) ?>;
      border-color: <?php echo esc_attr($theme_color) ?>;
   }
   .stm-listing-pagination ul.pagination > li.active a {
      background: <?php echo esc_attr($theme_color) ?>;
      border-color: <?php echo esc_attr($theme_color) ?>;
   }
   .ulisting-element-address .icon {
      color: <?php echo esc_attr($theme_color) ?>;
   }
   .ulisting-el-heading:before {
      background: <?php echo esc_attr($theme_color) ?>;
   }
   .listing-block .listing-thumbnail .listing-category span.listing-featured {
      background: <?php echo esc_attr($theme_color) ?>;
   }
   .listing-block .listing-content .content-inner .listing-price .ulisting-listing-price .ulisting-listing-price-new {
      color: <?php echo esc_attr($theme_color) ?>;
   }
   .listing-block-list-small .listing-content .listing-price .ulisting-listing-price .ulisting-listing-price-new {
      color: <?php echo esc_attr($theme_color) ?>;
   }
   .listing-block-single .listing-thumbnail .listing-category span.listing-featured {
      background: <?php echo esc_attr($theme_color) ?>;
   }
   .ulisting-search-form .nav-tabs li a:hover, .ulisting-search-form .nav-tabs li a.active {
      color: <?php echo esc_attr($theme_color) ?>;
   }
   .ulisting-search-form .tab-content .listing-search-tab-content .basic-search-wrapper .action-advanced-search .btn-advanced-search .icon {
      color: <?php echo esc_attr($theme_color) ?>;
   }
   .ulisting-search-form .tab-content .listing-search-tab-content .basic-search-wrapper .action-advanced-search .btn-advanced-search:hover {
      color: <?php echo esc_attr($theme_color) ?>;
   }
   .ulisting-search-form.style-3 .ulisting-search-form-wrapper > .nav-tabs > li a.active, .ulisting-search-form.style-3 .ulisting-search-form-wrapper > .nav-tabs > li a:hover {
      background: <?php echo esc_attr($theme_color) ?>;
   }
   .ulisting-field-range .irs--round .irs-bar.irs-bar, .ulisting-field-range .irs--round .irs-line.irs-bar {
      background: <?php echo esc_attr($theme_color) ?>;
   }
   .ulisting-field-range .irs--round .irs-handle {
      border: 2px solid <?php echo esc_attr($theme_color) ?>;
   }
   .control-filter-sidebar-mobile {
      background: <?php echo esc_attr($theme_color) ?>;
   }
   .lt-checkbox.pretty .state .icon {
      background: <?php echo esc_attr($theme_color) ?>;
   }
   @media (max-width: 370px) {
      #ulisting-inventory-list .listing-reset-filter a i {
        color: <?php echo esc_attr($theme_color) ?>;
      }
   }
   .form-login-register .nav-tabs li a.active {
      color: <?php echo esc_attr($theme_color) ?>;
   }
   .author-single-page .author-single-block .block-content-inner .user-content .user-info .user-info-item .value a:hover {
      color: <?php echo esc_attr($theme_color) ?>;
   }
   .author-single-page ul.ulisting-tabs > li a.active {
      color: <?php echo esc_attr($theme_color) ?>;
   }
   .lt-single-gallery .gallery-item .image-expand:hover {
      background: <?php echo esc_attr($theme_color) ?>;
   }
   .single-listing .ulisting-listing-price .ulisting-listing-price-new {
      color: <?php echo esc_attr($theme_color) ?>;
   }
   .single-listing .ulisting-listing-category {
      background: <?php echo esc_attr($theme_color) ?>;
   }
   .single-listing .listing-single-meta .listing-meta-inner .listing-meta-left .meta-item .icon {
      color: <?php echo esc_attr($theme_color) ?>;
   }
   .single-listing .listing-single-meta .listing-meta-inner .listing-meta-right .wishlist-icon-content .ajax-wishlist-link:hover {
      background: <?php echo esc_attr($theme_color) ?>;
   }
   .single-listing .listing-single-meta .listing-meta-inner .listing-meta-right .wishlist-icon-content .ajax-wishlist-link.wishlist-added i {
      color: <?php echo esc_attr($theme_color) ?>;
   }
   .single-listing .listing-agent-information .information-content .user-information .btn-display-phone {
      background: <?php echo esc_attr($theme_color) ?>;
   }
   .single-listing .listing-agent-information .information-content .user-information-2 > div .icon {
      color: <?php echo esc_attr($theme_color) ?>;
   }
   .single-listing .listing-agent-information .contact-form .lt-contact-fom-btn:hover {
      color: <?php echo esc_attr($theme_color) ?>;
   }
   #form-listing-contact-popup .mfp-close {
      background: <?php echo esc_attr($theme_color) ?>;
   }
   .ulisting-add-listing-form .listing-steps-form .step-item.active {
      color: <?php echo esc_attr($theme_color) ?>;
   }
   .ulisting-add-listing-form .card-listing-type:hover {
      background: <?php echo esc_attr($theme_color) ?>;
   }
   .ulisting-dashboard-page .ulising-dashboard-main .ulising-dashboard-content .dashboard-topbar .content-left .nickname {
      color: <?php echo esc_attr($theme_color) ?>;
   }
   .ulisting-dashboard-page .ulising-dashboard-main .ulising-dashboard-content .ulising-dashboard-content-wrapper .dashboard-content-inner .ulisting-table tbody tr td a, .ulisting-dashboard-page .ulising-dashboard-main .ulising-dashboard-content .ulising-dashboard-content-wrapper .dashboard-content-inner .ulisting-table tbody tr th a {
      color: <?php echo esc_attr($theme_color) ?>;
   }
   .ulisting-dashboard-page .ulising-dashboard-main .ulising-dashboard-content .ulising-dashboard-content-wrapper .page-dashboard-content .dashboard-card {
      background: <?php echo esc_attr($theme_color) ?>;
   }
   .ulisting-dashboard-page .ulising-dashboard-main .ulising-dashboard-content .ulising-dashboard-content-wrapper #ulisting_my_listing .ulisting-my-listing-sidebar .my-listing-sidebar-wrap > li.is-active {
      color: <?php echo esc_attr($theme_color) ?>;
   }
   #stm-listing-profile-edit .profile-avata .ulisting-form-gruop .input-file input {
      background: <?php echo esc_attr($theme_color) ?>;
   }
   #stm-listing-profile-edit .profile-avata .ulisting-form-gruop .input-file .input-title {
      background: <?php echo esc_attr($theme_color) ?>;
   }
   .user_box-list .users_box .user-box-content .users_box_info .user-meta-item .icon {
      color: <?php echo esc_attr($theme_color) ?>;
   }
   .woocommerce-tabs .nav-tabs > li.active > a {
      color: <?php echo esc_attr($theme_color) ?>;
   }
   .woocommerce-tab-product-info .submit {
      background: <?php echo esc_attr($theme_color) ?>;
   }
   .minibasket.light i {
      color: <?php echo esc_attr($theme_color) ?>;
   }
   table.variations a.reset_variations {
      color: <?php echo esc_attr($theme_color) ?> !important;
   }
   .single-product .social-networks > li a:hover {
      color: <?php echo esc_attr($theme_color) ?>;
   }
   .single-product .image_frame .woocommerce-product-gallery__trigger:hover {
      background: <?php echo esc_attr($theme_color) ?>;
   }
   .single-product .image_frame .onsale {
      background: <?php echo esc_attr($theme_color) ?>;
   }
   .single-product ol.flex-control-nav.flex-control-thumbs .owl-item img.flex-active {
      border: 1px solid <?php echo esc_attr($theme_color) ?>;
   }
   .single-product .product-single-main.product-type-grouped table.group_table tr td.label a:hover, .single-product .product-single-main.product-type-grouped table.group_table tr td label a:hover {
      color: <?php echo esc_attr($theme_color) ?>;
   }
   .single-product .entry-summary .woocommerce-product-rating .woocommerce-review-link:hover {
      color: <?php echo esc_attr($theme_color) ?>;
   }
   .single-product .entry-summary .price {
      color: <?php echo esc_attr($theme_color) ?>;
   }
   .single-product .product-single-inner .cart .button, .single-product .product-single-inner .add-cart .button {
      background: <?php echo esc_attr($theme_color) ?>;
   }
   .single-product .product-single-inner .cart .button:hover, .single-product .product-single-inner .add-cart .button:hover {
      background: <?php echo esc_attr($theme_color) ?>;
   }
   .single-product .product-single-inner .yith-wcwl-add-to-wishlist a {
      background: <?php echo esc_attr($theme_color) ?>;
   }
   .single-product .product-single-inner .yith-wcwl-add-to-wishlist a:hover {
      background: <?php echo esc_attr($theme_color) ?>;
   }
   .single-product .product-single-inner a.compare {
      background: <?php echo esc_attr($theme_color) ?>;
   }
   .single-product .product-single-inner a.compare:hover {
      background: <?php echo esc_attr($theme_color) ?>;
   }
   .single-product .product-single-inner form.cart .table-product-group td.label a:hover {
      color: <?php echo esc_attr($theme_color) ?>;
   }
   .single-product .product-single-inner form.cart .add-cart button {
      background: <?php echo esc_attr($theme_color) ?>;
   }
   .single-product .product-single-inner form.cart .add-cart button:hover {
      background: <?php echo esc_attr($theme_color) ?>;
   }
   .single-product .product_meta > span a:hover {
      color: <?php echo esc_attr($theme_color) ?>;
   }
   .woocommerce-account .woocommerce-MyAccount-navigation ul > li.is-active a {
      color: <?php echo esc_attr($theme_color) ?>;
   }
   .woocommerce #breadcrumb a:hover {
      color: <?php echo esc_attr($theme_color) ?>;
   }
   .woocommerce-page .content-page-inner input.button, .woocommerce-page .content-page-inner a.button {
      background: <?php echo esc_attr($theme_color) ?>;
   }
   .woocommerce-cart-form__contents .woocommerce-cart-form__cart-item td.product-remove a.remove {
      background: <?php echo esc_attr($theme_color) ?>;
   }
   .shop-loop-actions .quickview a:hover, .shop-loop-actions .yith-wcwl-add-to-wishlist a:hover, .shop-loop-actions .yith-compare a:hover, .shop-loop-actions .add-to-cart a:hover {
      background: <?php echo esc_attr($theme_color) ?>;
   }
   .shop-loop-price .price {
      color: <?php echo esc_attr($theme_color) ?>;
   }
   .gva-countdown .countdown-times > div.day {
      color: <?php echo esc_attr($theme_color) ?>;
   }
   .product_list_widget .minicart-close:hover {
      color: <?php echo esc_attr($theme_color) ?>;
   }
   .product_list_widget.cart_list .widget-product .name a:hover {
      color: <?php echo esc_attr($theme_color) ?> !important;
   }
   .product_list_widget.cart_list .widget-product .remove {
      background: <?php echo esc_attr($theme_color) ?>;
   }
   .woo-display-mode > a:hover, .woo-display-mode > a:active, .woo-display-mode > a:focus, .woo-display-mode > a.active {
      background: <?php echo esc_attr($theme_color) ?>;
   }
   .filter-sidebar .filter-sidebar-inner.layout-offcavas .filter-close {
      background: <?php echo esc_attr($theme_color) ?>;
   }
   .woocommerce .button[type*="submit"] {
      background: <?php echo esc_attr($theme_color) ?>;
   }
   .widget.widget_layered_nav ul > li a:hover {
      color: <?php echo esc_attr($theme_color) ?>;
   }
   .widget.widget_product_categories li.current-cat > a {
      color: <?php echo esc_attr($theme_color) ?> !important;
   }
   .widget.widget_product_categories ul.product-categories > li.has-sub .cat-caret:hover {
      color: <?php echo esc_attr($theme_color) ?>;
   }
   .widget.widget_product_categories ul.product-categories > li ul a:hover {
      color: <?php echo esc_attr($theme_color) ?>;
   }

<?php }  ?> 
<?php //------ End Color Theme ------ ?>

<?php //------ Theme Color Second ------ ?>
<?php if( !empty($theme_color_second) ){ ?>
   .btn:hover, .btn-theme:hover, .btn:hover, .btn-white:hover, .btn-theme-2:hover, .btn-theme-2 input[type*="submit"]:hover, .btn-black:hover, input[type*="submit"]:not(.fa):not(.btn-theme):hover, #tribe-events .tribe-events-button:hover, .tribe-events-button:hover, .load_more_jobs:hover {
      background: <?php echo esc_attr($theme_color_second) ?>;
   }
   .btn-inline:hover {
      color: <?php echo esc_attr($theme_color_second) ?>;
   }
   ul.gva-nav-menu > li:after {
      background: <?php echo esc_attr($theme_color_second) ?>;
   }
   ul.gva-nav-menu > li:hover > a, ul.gva-nav-menu > li:active > a, ul.gva-nav-menu > li:focus > a, ul.gva-nav-menu > li.current_page_parent > a {
      color: <?php echo esc_attr($theme_color_second) ?>;
   }
   .gsc-call-to-action .sub-title {
      color: <?php echo esc_attr($theme_color_second) ?>;
   }
   .gsc-icon-box .highlight-icon .box-icon {
      color: <?php echo esc_attr($theme_color_second) ?>;
   }
   .gsc-icon-box .highlight-icon .box-icon svg {
      fill: <?php echo esc_attr($theme_color_second) ?>;
   }
   .gsc-heading .sub-title {
      color: <?php echo esc_attr($theme_color_second) ?>;
   }
   .gsc-image-content.skin-v1:hover .image .number-text, .gsc-image-content.skin-v1.active .image .number-text {
      background: <?php echo esc_attr($theme_color_second) ?>;
   }
   .user-agent-block.user-agency .user-content .user-position {
      color: <?php echo esc_attr($theme_color_second) ?>;
   }
   .user-agent-block.user-agency .user-content .users-socials a:hover {
      background: <?php echo esc_attr($theme_color_second) ?>;
   }
   .user-agent-block.user-agent .user-content .users-socials a:hover {
      background: <?php echo esc_attr($theme_color_second) ?>;
   }
   .ulisting-attribute-template.type-3 .attribute-icon {
      color: <?php echo esc_attr($theme_color_second) ?>;
   }
   .ulisting-attribute-template.type-4 .attributes-list li .ulisting-attribute-template-icon {
      color: <?php echo esc_attr($theme_color_second) ?>;
   }
   .listing-el-accordion .card .card-header .btn-link {
      border: 1px solid <?php echo esc_attr($theme_color_second) ?>;
      background: <?php echo esc_attr($theme_color_second) ?>;
   }
   #app_mortgage_calc .form-mortgage_calc-content .graph rect {
      fill: <?php echo esc_attr($theme_color_second) ?>;
   }
   .listing-block .listing-thumbnail .wishlist-icon-content a.wishlist-added {
      background: <?php echo esc_attr($theme_color_second) ?>;
   }
   .listing-block .listing-thumbnail .wishlist-icon-content a:hover {
      background: <?php echo esc_attr($theme_color_second) ?>;
   }
   .listing-block .listing-content .content-bottom > div .ulisting-attribute-template-icon {
      color: <?php echo esc_attr($theme_color_second) ?>;
   }
   .style-map-1 .listing-thumbnail .listing-price-attributes .listing-attributes > div .ulisting-attribute-template-icon {
      color: <?php echo esc_attr($theme_color_second) ?>;
   }
   .listing-block-single .listing-content .subtitle-text {
      color: <?php echo esc_attr($theme_color_second) ?>;
   }
   .listing-block-single .listing-content .listing-short-description ul li:after {
      color: <?php echo esc_attr($theme_color_second) ?>;
   }
   .listing-block-single .listing-content .listing-attributes > div .ulisting-attribute-template-icon {
      color: <?php echo esc_attr($theme_color_second) ?>;
   }
   #stm-pricing-plan .listing-pricing-plan-content .card .card-body .plan-content ul li:before {
      color: <?php echo esc_attr($theme_color_second) ?>;
   }
   .single-listing .listing-agent-information .information-content .user-socials a:hover {
      background: <?php echo esc_attr($theme_color_second) ?>;
   }
   .single-listing .listing-agent-information .contact-form .lt-contact-fom-btn {
      color: <?php echo esc_attr($theme_color_second) ?>;
   }
   #form-listing-contact-popup .mfp-close:hover {
      background: <?php echo esc_attr($theme_color_second) ?>;
   }
   .ulisting-add-listing-form #stm-listing-form-listing .form-listing-item .card-plan .card-body .badge {
      background: <?php echo esc_attr($theme_color_second) ?>;
   }
   .user_box-list .users_box .users-socials-box a:hover {
      background: <?php echo esc_attr($theme_color_second) ?>;
   }

<?php } ?>
<?php //------ End Color Theme Second ------ ?>


<?php
   $styles = ob_get_clean();
   $styles = preg_replace( '!/\*[^*]*\*+([^/][^*]*\*+)*/!', '', $styles );
   $styles = str_replace( array( "\r\n", "\r", "\n", "\t", '  ', '   ', '    ' ), '', $styles );
   if($styles){
      wp_enqueue_style( 'tolips-custom-style-color', TOLIPS_THEME_URL . '/css/custom_script.css');
      wp_add_inline_style( 'tolips-custom-style-color', $styles );
   }

}

add_action( 'wp_enqueue_scripts', 'tolips_custom_color_theme', 99999 );