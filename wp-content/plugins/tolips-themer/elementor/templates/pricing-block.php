<?php
   use Elementor\Icons_Manager;

   if ( empty( $settings['title_text'] ) ) {
      return;
   }
   $title_text = $settings['title_text'];

   $this->add_render_attribute( 'block', 'class', [ 'widget gsc-pricing', $settings['style'] ] );
   
   $header_tag = 'h3';

   $this->add_render_attribute( 'title_text', 'class', 'title' );

   $this->add_inline_editing_attributes( 'title_text', 'none' );

?>
   
<?php if($settings['style'] == 'style-1'){ ?>
   <div <?php echo $this->get_render_attribute_string( 'block' ) ?>>
      <div class="content-inner">
       
         <?php if($title_text){ ?>
            <<?php echo esc_attr($header_tag) ?> <?php echo $this->get_render_attribute_string( 'title_text' ); ?>>
               <span><?php echo $settings['title_text'] ?></span>
            </<?php echo esc_attr($header_tag) ?>>
         <?php } ?>
        
         <div class="plan-price">
            <div class="price-value clearfix">
               <span class="currency"><?php echo esc_html( $settings['currency'] ) ?></span><span class="value"><?php echo esc_html( $settings['price'] ) ?></span>
            </div>
            <div class="interval"><?php echo esc_html( $settings['period'] ) ?></div>
         </div>

         <?php if($settings['pricing_content']){ ?>
            <ul class="plan-list">
               <?php foreach ($settings['pricing_content'] as $key => $item) { ?>
                  <?php 
                     $has_icon = ! empty( $item['selected_icon']['value']);
                  ?>
                  <li>
                     <span class="text"><?php echo $item['pricing_features'] ?></span>
                     <?php if ( $has_icon ){ ?>
                        <span class="icon"><?php Icons_Manager::render_icon( $item['selected_icon'], [ 'aria-hidden' => 'true' ] ); ?></span>
                     <?php } ?>
                  </li>  
               <?php } ?>
            </ul>
         <?php } ?>   


         <?php if($settings['button_url']['url']){ ?>
            <div class="pricing-action">
               <?php $this->gva_render_button('btn-theme'); ?>
            </div>
         <?php } ?>
      </div>
   </div>

   <div class="clearfix"></div>

<?php }else if( $settings['style'] == 'style-2' ){ ?>
   <?php 
      $image_url = $settings['image']['url']; 
   ?>
   <div <?php echo $this->get_render_attribute_string( 'block' ) ?>>
      <div class="content-inner clearfix <?php echo (empty($image_url) ? 'no-image' : '') ?>">
         
         <?php if($image_url){ ?>
            <div class="price-left">
               <div class="content-inner">
                  <img src="<?php echo $image_url ?>" alt="<?php echo esc_html($settings['title_text']) ?>" />
               </div>
            </div>
        <?php } ?>    

         <div class="price-right">

            <?php if($title_text){ ?>
               <<?php echo esc_attr($header_tag) ?> <?php echo $this->get_render_attribute_string( 'title_text' ); ?>>
                  <span><?php echo $settings['title_text'] ?></span>
               </<?php echo esc_attr($header_tag) ?>>
            <?php } ?>

            <?php if($settings['desc_text']){ ?>
               <div class="desc"><?php echo $settings['desc_text']; ?></div>
            <?php } ?>
            
            <div class="price-meta">
               <?php if($settings['subtitle_text']){ ?>
                  <div class="sub-title">
                     <?php echo $settings['subtitle_text'] ?>
                  </div>
               <?php } ?>   
               <div class="plan-price">
                  <div class="price-value clearfix">
                     <span class="currency"><?php echo esc_html( $settings['currency'] ) ?></span><span class="value"><?php echo esc_html( $settings['price'] ) ?></span><span>/</span><span class="interval"><?php echo esc_html( $settings['period'] ) ?></span>
                  </div>
               </div>
            </div>   

            <?php if($settings['button_url']['url']){ ?>
               <div class="pricing-action">
                  <?php $this->gva_render_button('btn-theme btn-small-arrow'); ?>
               </div>
            <?php } ?>

         </div>

      </div>   

   </div>

   <div class="clearfix"></div>

<?php } ?>