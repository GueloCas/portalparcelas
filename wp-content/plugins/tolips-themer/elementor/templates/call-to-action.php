<?php
   if ( empty( $settings['title_text'] ) ) {
      return;
   }
   $title_text = $settings['title_text'];
   $sub_title = $settings['sub_title'];
   $description_text = $settings['description_text'];
   $button_style = $settings['button_style'] ? $settings['button_style'] : 'btn-line-white';
   
   $this->add_render_attribute( 'block', 'class', [ $settings['style'], 'widget gsc-call-to-action' ] );
   $header_tag = 'h2';

   $this->add_render_attribute( 'title_text', 'class', 'title' );
   $this->add_render_attribute( 'description_text', 'class', 'desc' );

   $this->add_inline_editing_attributes( 'title_text', 'none' );
   $this->add_inline_editing_attributes( 'sub_title', 'none' );
   $this->add_inline_editing_attributes( 'description_text' );
   $btn_classes = "btn-cta {$button_style}";
   ?>

   <div <?php echo $this->get_render_attribute_string( 'block' ) ?>>
      <div class="content-inner clearfix" >
         <div class="cta-content">
            <?php if($sub_title){ ?>
               <div class="sub-title"><span><?php echo esc_html($sub_title); ?></span></div>
            <?php } ?>
            <?php if($title_text){ ?>
               <<?php echo esc_attr($header_tag) ?> <?php echo $this->get_render_attribute_string( 'title_text' ); ?>>
                  <span><?php echo $settings['title_text'] ?></span>
               </<?php echo esc_attr($header_tag) ?>>
            <?php } ?>
            <?php if($description_text){ ?>
               <div <?php echo $this->get_render_attribute_string( 'description_text' ); ?>><?php echo wp_kses($description_text, true); ?></div>
            <?php } ?>
         </div>
         <?php if($settings['button_url']['url']){ ?>
            <div class="button-action">
               <?php $this->gva_render_button($btn_classes); ?>
            </div>
         <?php } ?>
      </div>
   </div>
