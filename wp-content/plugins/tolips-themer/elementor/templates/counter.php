<?php
  use Elementor\Icons_Manager;
   

   $style = $settings['style'];
   $header_tag = 'h2';
   if(!empty($settings['title_size'])) $header_tag = $settings['title_size'];

   $has_icon = ! empty( $settings['selected_icon']['value']);

   $title_html = $settings['title_text'];

   $this->add_render_attribute( 'block', 'class', [ 'widget', 'milestone-block', $settings['style'] ] );
   $this->add_render_attribute( 'title_text', 'class', 'milestone-text' );

   $this->add_inline_editing_attributes( 'title_text', 'none' );

   ?>

   <?php if($style == 'style-1' || $style == 'style-2' || $style == 'style-3'){ ?>
      <div <?php echo $this->get_render_attribute_string( 'block' ) ?>>
         <div class="box-content">
            <?php if ( $has_icon ){ ?>
               <div class="milestone-icon">
                  <span class="icon">
                     <?php Icons_Manager::render_icon( $settings['selected_icon'], [ 'aria-hidden' => 'true' ] ); ?>
                  </span>
               </div>
            <?php } ?>

            <div class="milestone-content">
               <div class="milestone-number-inner">
                  <?php if($settings['text_before']) echo ('<span class="symbol before">' . $settings['text_before'] . '</span>'); ?>
                  <span class="milestone-number"><?php echo esc_attr($settings['number']); ?></span>
                  <?php if($settings['text_after']) echo ('<span class="symbol after">' . $settings['text_after'] . '</span>'); ?>
               </div>
               <?php if(!empty($title_html)){ ?>
                  <<?php echo esc_attr($header_tag) ?> <?php echo $this->get_render_attribute_string( 'title_text' ); ?>>
                      <?php echo $title_html ?>
                  </<?php echo esc_attr($header_tag) ?>>
               <?php } ?>
            </div>
            
            <?php $this->gva_render_link_html('', $settings['link'], 'link-overlay'); ?>

            <?php if($style == 'style-3'){ ?>
               <?php if ( $has_icon ){ ?>
                  <div class="milestone-icon-background">
                     <span class="icon">
                        <?php Icons_Manager::render_icon( $settings['selected_icon'], [ 'aria-hidden' => 'true' ] ); ?>
                     </span>
                  </div>
               <?php } ?>
            <?php } ?>

         </div>   
      </div> 
   <?php } ?>

