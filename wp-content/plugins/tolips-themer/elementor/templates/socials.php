<?php
   if (!defined('ABSPATH')) {
      exit; // Exit if accessed directly.
   }
   $this->add_render_attribute('wrapper', 'class', [ 'gva-socials', $settings['style'], 'align-' . $settings['align'] ]);
?>
<div <?php echo $this->get_render_attribute_string('wrapper'); ?>>
   <ul class="social-links">
      <?php foreach ($settings['socials'] as $social): ?>
         <li class="social-item elementor-repeater-item-<?php echo esc_attr($social['_id']) ?>">
            <a href="<?php echo esc_url($social['social_link']) ?>" title="<?php echo esc_attr($social['social_title']) ?>"><i class="<?php echo esc_attr($social['social_icon']) ?>"></i></a>
         </li>
      <?php endforeach; ?>
   </ul>
</div>
