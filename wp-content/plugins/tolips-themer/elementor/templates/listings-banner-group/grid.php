<?php
   if (!defined('ABSPATH')) {
      exit; // Exit if accessed directly.
   }
   use Elementor\Icons_Manager;

   $this->add_render_attribute('wrapper', 'class', ['gsc-listings-banner-group layout-grid', $settings['style']]);
   
   $style = $settings['style'] ? $settings['style'] : 'style-1';

   //add_render_attribute grid
   $this->get_grid_settings();
?>

<div <?php echo $this->get_render_attribute_string('wrapper'); ?>>
   <div <?php echo $this->get_render_attribute_string('grid') ?>>
      <?php
         foreach ($settings['content_banners'] as $banner): 
            include $this->get_template('listings-banner-group/item-' . $style . '.php');
         endforeach; 
      ?>
   </div>   
</div>
