<?php
   use Elementor\Icons_Manager;

   $this->add_render_attribute( 'icon', 'class', [ 'icon icon-font'] );
   
   $this->add_render_attribute( 'icon_image', 'class', [ 'icon icon-image' ] );
   

   $has_icon = ! empty( $settings['selected_icon']['value']);

  
   $this->add_render_attribute( 'block', 'class', [ $settings['style'], 'widget gsc-search-box' ] );

   ?>
   <div <?php echo $this->get_render_attribute_string( 'block' ) ?>>
      <div class="content-inner">
         
         <div class="main-search gva-search">
            <?php if($has_icon){ ?>
               <a class="control-search">
                  <?php Icons_Manager::render_icon( $settings['selected_icon'], [ 'aria-hidden' => 'true' ] ); ?>
               </a>
            <?php } ?>   

            <div class="gva-search-content search-content">
              <div class="search-content-inner">
                <div class="content-inner"><?php get_search_form(); ?></div>  
              </div>  
            </div>
         </div>
         
      </div>
   </div>
