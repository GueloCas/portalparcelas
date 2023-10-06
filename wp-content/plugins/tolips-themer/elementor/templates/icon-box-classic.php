<?php
   use Elementor\Icons_Manager;

   $this->add_render_attribute( 'icon', 'class', [ 'icon icon-font elementor-icon'] );
   
   $this->add_render_attribute( 'icon_image', 'class', [ 'icon icon-image elementor-icon' ] );

   $this->add_render_attribute( 'icon_wrapper', 'class', [ 'highlight-icon' ] );

   $icon_tag = 'span';

   $has_icon = ! empty( $settings['selected_icon']['value']);

   if ( ! empty( $settings['link']['url'] ) ) {
      $this->add_render_attribute( 'link', 'href', $settings['link']['url'] );
      $icon_tag = 'a';
      if ( $settings['link']['is_external'] ) {
         $this->add_render_attribute( 'link', 'target', '_blank' );
      }
      if ( $settings['link']['nofollow'] ) {
         $this->add_render_attribute( 'link', 'rel', 'nofollow' );
      }
   }

   $icon_attributes = $this->get_render_attribute_string( 'icon' );
   $link_attributes = $this->get_render_attribute_string( 'link' );

   $this->add_render_attribute( 'description_text', 'class', 'elementor-icon-box-description desc' );
   
   $this->add_render_attribute( 'subtitle_text', 'class', 'sub-title' );

   $this->add_inline_editing_attributes( 'subtitle_text', 'none' );
   $this->add_inline_editing_attributes( 'title_text', 'none' );
   $this->add_inline_editing_attributes( 'description_text' );
   $icon_position = $settings['position'];
   $classes = $icon_position;
   ?>
   
   <?php if($icon_position=='top-center' || $icon_position=='top-left' || $icon_position=='top-right' || $icon_position=='right' || $icon_position=='left'){ ?>
      <div class="widget gsc-icon-box <?php echo esc_attr($classes) ?>">
         
         <?php if ( $has_icon && $icon_position != 'right') : ?>
            <div <?php echo $this->get_render_attribute_string( 'icon_wrapper' ); ?>>
               <?php $this->gva_render_link_begin($settings['link']); ?>
                  <span class="box-icon">
                     <?php Icons_Manager::render_icon( $settings['selected_icon'], [ 'aria-hidden' => 'true' ] ); ?>
                  </span>
               <?php $this->gva_render_link_end($settings['link']); ?>
            </div>
         <?php endif; ?>

         <div class="highlight_content">
            <?php if($settings['subtitle_text']){ ?>
               <div <?php echo $this->get_render_attribute_string( 'subtitle_text' ); ?>><?php echo $settings['subtitle_text'] ?></div>
            <?php } ?>
            <<?php echo $settings['title_size']; ?> class="title">
            <<?php echo implode( ' ', [ $icon_tag, $link_attributes ] ); ?><?php echo $this->get_render_attribute_string( 'title_text' ); ?>><?php echo $settings['title_text']; ?></<?php echo $icon_tag; ?>>
            </<?php echo $settings['title_size']; ?>>
            <?php if($settings['description_text']){ ?>
               <div <?php echo $this->get_render_attribute_string( 'description_text' ); ?>><?php echo $settings['description_text']; ?></div>
            <?php } ?>
         </div>

         <?php if ( $has_icon && $icon_position == 'right') : ?>
            <div class="highlight-icon">
               <?php $this->gva_render_link_begin($settings['link']); ?>
                  <span class="box-icon">
                     <?php Icons_Manager::render_icon( $settings['selected_icon'], [ 'aria-hidden' => 'true' ] ); ?>
                  </span>
               <?php $this->gva_render_link_end($settings['link']); ?>
            </div>
         <?php endif; ?>

      </div>   
   <?php } ?>