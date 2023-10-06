<?php
   if (!defined('ABSPATH')) {
      exit; // Exit if accessed directly.
   }
   use Elementor\Icons_Manager;

   extract( $settings );

   $this->add_render_attribute('wrapper', 'class', ['gsc-listings-banner-group layout-carousel', $settings['style']]);
   
   $this->add_render_attribute('carousel', 'class', ['init-carousel-owl owl-carousel']);

   $style = $settings['style'] ? $settings['style'] : 'style-1';
?>

<div <?php echo $this->get_render_attribute_string('wrapper'); ?>>
   <div <?php echo $this->get_render_attribute_string('carousel') ?> <?php echo $this->get_carousel_settings() ?>>
      <?php
         foreach ($settings['content_banners'] as $banner): 
            include $this->get_template('listings-banner-group/item-' . $style . '.php');
         endforeach; 
      ?>
   </div>
</div>
