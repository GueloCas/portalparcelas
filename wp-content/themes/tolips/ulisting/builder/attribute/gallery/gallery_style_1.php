 <?php
   $id = rand(100, 99999);
   $data_carousel = '';
   $carousel_attributes = array(
      'items'               => 4,
      'items_lg'            => 4,
      'items_md'            => 3,
      'items_sm'            => 3,
      'items_xs'            => 2,
      'items_xx'            => 1,
      'loop'                => 1,
      'speed'               => 800,
      'auto_play'           => 0,
      'auto_play_speed'     => 800,
      'auto_play_timeout'   => 3600,
      'auto_play_hover'     => 1,
      'navigation'          => 0,
      'pagination'          => 0,
      'mouse_drag'          => 1,
      'touch_drag'          => 1,
      'stage_padding'       => 0
   );
   foreach ($carousel_attributes as $key => $value) {
      $data_carousel .= 'data-' . esc_attr( $key ) . '="' . esc_attr($value) . '" ';
   }
?>


<?php if (!empty($gallery_items)): ?>
   
   <div class="lt-single-gallery style-1">
      
      <div class="init-carousel-owl-theme owl-carousel" <?php echo trim($data_carousel) ?>>
         <?php foreach ($gallery_items as $item):  ?>
            <div class="item">
               <div class="gallery-item">
                  <a class="photo-gallery-item" href="<?php echo esc_url($item['full'][0]); ?>" data-elementor-lightbox-slideshow="<?php echo esc_attr($id); ?>">
                    <?php echo wp_get_attachment_image($item['value'], 'medium'); ?>
                     <span class="image-expand"><i class="icon las la-expand"></i></span>
                  </a>
               </div>   
            </div>   
         <?php endforeach; ?>
      </div>
   </div>

<?php endif; ?>   
