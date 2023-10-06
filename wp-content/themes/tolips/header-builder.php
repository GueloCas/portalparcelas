<?php
/**
 * $Desc
 *
 * @author     Gaviasthemes Team     
 * @copyright  Copyright (C) 2021 gaviasthemes. All Rights Reserved.
 * @license    GNU/GPL v2 or later http://www.gnu.org/licenses/gpl-2.0.html
 * 
 */ 
  $protocol = is_ssl() ? 'https' : 'http';
  $header_slug = apply_filters('tolips_get_header_layout', null );
  $header_id = '';
  $header = get_page_by_path($header_slug, OBJECT, 'gva_header');
  if($header) {
    $header_id = $header->ID;
  }
  $header_position = get_post_meta($header_id, 'tolips_header_position', true);
?>
<!DOCTYPE html>
<html <?php language_attributes(); ?> class="no-js">
<head>
  <meta http-equiv="content-type" content="text/html; charset=<?php bloginfo( 'charset' ); ?>">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <link rel="profile" href="<?php echo esc_attr($protocol) ?>://gmpg.org/xfn/11">
  <?php wp_head(); ?>
</head>

<body <?php body_class() ?>>
  <?php wp_body_open(); ?>
  <div class="wrapper-page"> <!--page-->
    <?php do_action( 'tolips_before_header' );  ?>
    
    <header class="header-builder-frontend header-position-<?php echo esc_attr($header_position) ?>">
      
      <?php do_action( 'tolips_header_mobile' ); ?>

      <div class="header-builder-inner">
        <div class="d-none d-xl-block d-lg-block">
          <?php if(!empty($header_slug) && class_exists('Gavias_Themer_Header')){
            echo '<div class="header-main-wrapper">' .  Gavias_Themer_Header::getInstance()->render_header_builder($header_slug)  . '</div>'; 
          }else{
            get_template_part('header-default');
          }?>
        </div> 
      </div>  
    </header>

    <?php do_action( 'tolips_after_header' );  ?>
    
    <div id="page-content"> <!--page content-->
