<?php
   $_random = gaviasthemer_random_id();
   $this->add_render_attribute( 'block', 'class', [ 'gva-navigation-menu', ' menu-align-' . $settings['align'] ] );
   $args = [
      'echo'        => false,
      'menu'        => $settings['menu'],
      'menu_class'  => 'gva-nav-menu gva-main-menu',
      'menu_id'     => 'menu-' . $_random,
      'container'   => 'div'
   ];

   if(class_exists('Tolips_Walker')){
      $args['walker' ]     = new Tolips_Walker();
   }
   
   $menu_html = wp_nav_menu($args);
   if (empty($menu_html)) {
      return;
   }
?>
   <div <?php echo $this->get_render_attribute_string( 'block' ) ?>>
      <?php echo $menu_html; ?>
   </div>