<?php
  use Elementor\Icons_Manager;

   $header_tag = 'h2';
   if(!empty($settings['header_tag'])) $header_tag = $settings['header_tag'];

   $has_icon = ! empty( $settings['selected_icon']['value']);

   $title_html = $settings['title_text'];

   $this->add_render_attribute( 'block', 'class', [ 'widget gsc-work-process' ] );
   $this->add_render_attribute( 'number_text', 'class', 'number-text' );
   $this->add_render_attribute( 'title_text', 'class', 'box-title' );

   $this->add_inline_editing_attributes( 'title_text', 'none' );
   $this->add_inline_editing_attributes( 'number_text' );

   ?>

   <div <?php echo $this->get_render_attribute_string( 'block' ) ?>>
      <div class="box-content">
         <?php if($settings['line_left'] == 'yes'){ echo '<div class="box-line line-left"></div>'; } ?> 
         <?php if($settings['line_right'] == 'yes'){ echo '<div class="box-line line-right"></div>'; } ?> 
         <div class="box-background"></div>
         <div class="content-inner">
            <?php if ( $has_icon ){ ?>
               <div class="icon-inner">
                     <?php if ( $has_icon ){ ?>
                        <?php $this->gva_render_link_begin($settings['button_url']); ?>
                           <span class="box-icon">
                              <?php Icons_Manager::render_icon( $settings['selected_icon'], [ 'aria-hidden' => 'true' ] ); ?>
                           </span>
                        <?php $this->gva_render_link_end($settings['button_url']); ?>
                     <?php } ?>
               </div>
            <?php } ?>
            <?php if(!empty($settings['number_text'])){ ?>
               <div <?php echo $this->get_render_attribute_string( 'number_text' ); ?>><?php echo $settings['number_text']; ?></div>
            <?php } ?>
         </div>  
      </div>   
      <?php if(!empty($settings['title_text'])){ ?>
         <<?php echo esc_attr($header_tag) ?> <?php echo $this->get_render_attribute_string( 'title_text' ); ?>>
            <?php $this->gva_render_link_begin($settings['button_url']); ?>
               <?php echo $title_html; ?>
            <?php $this->gva_render_link_end($settings['button_url']); ?>     
         </<?php echo esc_attr($header_tag) ?>>
      <?php } ?>
   </div>   

