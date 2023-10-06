<?php
  use Elementor\Icons_Manager;

   $style = $settings['style'];
   $description_text = $settings['description_text'];
   $header_tag = 'h2';
   if(!empty($settings['header_tag'])) $header_tag = $settings['header_tag'];

   $has_icon = ! empty( $settings['selected_icon']['value']);

   $title_html = $settings['title_text'];

   $this->add_render_attribute( 'block', 'class', [ 'widget gsc-icon-box-styles', $settings['style'], $settings['active'] == 'yes' ? 'active' : '' ] );
   $this->add_render_attribute( 'description_text', 'class', 'desc' );
   $this->add_render_attribute( 'title_text', 'class', 'title' );

   $this->add_inline_editing_attributes( 'title_text', 'none' );
   $this->add_inline_editing_attributes( 'description_text' );

   ?>

   <?php if($style == 'style-1'){ ?>
      <div <?php echo $this->get_render_attribute_string( 'block' ) ?>>
         <div class="icon-box-content">
               <?php if ( $has_icon ){ ?>
                  <div class="icon-inner">
                     <?php if ( $has_icon ){ ?>
                        <span class="box-icon">
                           <span class="box-icon-inner">
                              <?php Icons_Manager::render_icon( $settings['selected_icon'], [ 'aria-hidden' => 'true' ] ); ?>
                           </span>
                        </span>
                     <?php } ?>
                  </div>
               <?php } ?>
            <div class="box-content">
               <?php if(!empty($settings['title_text'])){ ?>
                  <<?php echo esc_attr($header_tag) ?> <?php echo $this->get_render_attribute_string( 'title_text' ); ?>>
                     <?php echo $title_html; ?>
                  </<?php echo esc_attr($header_tag) ?>>
               <?php } ?>
               <?php if(!empty($settings['description_text'])){ ?>
                  <div <?php echo $this->get_render_attribute_string( 'description_text' ); ?>><?php echo wp_kses($description_text, true); ?></div>
               <?php } ?>
            </div>
         </div> 
         <?php $this->gva_render_link_html('', $settings['button_url'], 'link-overlay'); ?>
      </div>   
   <?php } ?>


   <?php if($style == 'style-2' || $style == 'style-3'){ ?>
      <div <?php echo $this->get_render_attribute_string( 'block' ) ?>>
         <?php if ( $has_icon ){ ?>
            <div class="icon-inner">
               <?php $this->gva_render_link_begin($settings['button_url']); ?>
                  <?php if ( $has_icon ){ ?>
                     <span class="box-icon">
                        <span class="box-icon-inner">
                           <?php Icons_Manager::render_icon( $settings['selected_icon'], [ 'aria-hidden' => 'true' ] ); ?>
                        </span>
                     </span>
                  <?php } ?>
               <?php $this->gva_render_link_end($settings['button_url']); ?>
            </div>
         <?php } ?>

         <div class="content-inner">
            <?php if(!empty($settings['title_text'])){ ?>
               <<?php echo esc_attr($header_tag) ?> <?php echo $this->get_render_attribute_string( 'title_text' ); ?>>
                  <?php $this->gva_render_link_html($title_html, $settings['button_url']); ?>
               </<?php echo esc_attr($header_tag) ?>>
            <?php } ?>

            <?php if(!empty($settings['description_text'])){ ?>
               <div <?php echo $this->get_render_attribute_string( 'description_text' ); ?>><?php echo wp_kses($description_text, true); ?></div>
            <?php } ?>
         </div>

      </div> 
   <?php } ?>   


