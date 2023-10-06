<?php
   if (!defined('ABSPATH')) {
      exit; // Exit if accessed directly.
   }
   use Elementor\Group_Control_Image_Size;
?>
   
<?php if( $settings['style'] == 'style-2' ){ 

   $this->add_render_attribute('wrapper', 'class', ['gva-testimonial-carousel' , $settings['style'] ]);
   $this->add_render_attribute('carousel', 'class', ['init-carousel-owl owl-carousel']);

   ?>
   <div <?php echo $this->get_render_attribute_string('wrapper'); ?>>
      <div <?php echo $this->get_render_attribute_string('carousel') ?> <?php echo $this->get_carousel_settings() ?>>
         <?php
         foreach ($settings['testimonials'] as $testimonial): ?>
            <?php 
               $avatar = (isset($testimonial['testimonial_image']['url']) && $testimonial['testimonial_image']['url']) ? $testimonial['testimonial_image']['url'] : '';
            ?>
            <div class="item">
               <div class="testimonial-item">
                  <div class="testimonial-item-content">
                    
                     <div class="testimonial-content">
                        <?php echo $testimonial['testimonial_content']; ?>
                     </div>

                     <div class="testimonial-meta">
                        <div class="testimonial-information">
                           <span class="testimonial-name"><?php echo $testimonial['testimonial_name']; ?></span>
                           <span class="testimonial-job"><?php echo $testimonial['testimonial_job']; ?></span>
                           <span class="icon fi flaticon-quote"></span>
                        </div>
                        <div class="testimonial-image">
                           <img src="<?php echo esc_url($avatar) ?>" alt="<?php echo $testimonial['testimonial_name']; ?>" />
                        </div>
                     </div>

                  </div>   
               </div>
            </div>   
         <?php endforeach; ?>
      </div>
   </div>
   <?php
}