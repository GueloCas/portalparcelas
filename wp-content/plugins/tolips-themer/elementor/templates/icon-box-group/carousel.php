<?php
   if (!defined('ABSPATH')) {
      exit; // Exit if accessed directly.
   }
   use Elementor\Icons_Manager;

   $this->add_render_attribute('wrapper', 'class', ['gsc-icon-box-group layout-carousel no-gutter', $settings['style']]);
   
   $this->add_render_attribute('carousel', 'class', ['init-carousel-owl owl-carousel']);
   
?>

<div <?php echo $this->get_render_attribute_string('wrapper'); ?>>
   <div <?php echo $this->get_render_attribute_string('carousel') ?> <?php echo $this->get_carousel_settings() ?>>
      <?php
      foreach ($settings['icon_boxs'] as $item): 
         $has_icon = ! empty( $item['selected_icon']['value']);
      ?>
         <div class="item icon-box-item">
            
            <div class="icon-box-item-content <?php echo ($item['active'] == 'yes' ? 'active' : ''); ?>">
               <div class="icon-box-item-inner">
                  
                  <?php if ( $has_icon ){ ?>
                     <div class="icon-inner">
                        <?php if ( $has_icon ){ ?>
                           <span class="box-icon">
                              <?php Icons_Manager::render_icon( $item['selected_icon'], [ 'aria-hidden' => 'true' ] ); ?>
                           </span>
                        <?php } ?>
                     </div>
                  <?php } ?>

                  <?php if($item['title']){ ?>
                     <h3 class="title"><?php echo $item['title'] ?></h3>
                  <?php } ?>

                  <?php if($settings['style'] == 'style-2'){ ?>
                     <div class="content-inner">
                        <?php if($item['desc']){ ?>
                           <div class="desc"><?php echo $item['desc'] ?></div>
                        <?php } ?>
                     </div>
                  <?php } ?>
                        
               </div>
               <?php $this->gva_render_link_html('', $item['link'], 'link-overlay'); ?>
            </div>
         </div>
      <?php endforeach; ?>
   </div>
</div>
