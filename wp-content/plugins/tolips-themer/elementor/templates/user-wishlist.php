<?php
   use Elementor\Icons_Manager;
   $this->add_render_attribute( 'block', 'class', [ 'user-wishlist', ' text-' . $settings['align'] ] );
   $wishlist_text = $settings['wishlist_text'] ? $settings['wishlist_text'] : "Wishlist";
?>

<div <?php echo $this->get_render_attribute_string( 'block' ) ?>>
   <div class="user-wishlist-content">
      
      <?php if(is_user_logged_in()){ ?>

         <?php $this->gva_render_link_begin($settings['link'], 'wishlist-link') ?>
            <span class="wishlist-icon">
               <?php Icons_Manager::render_icon( $settings['selected_icon'], [ 'class' => 'icon', 'aria-hidden' => 'true' ] ); ?>
            </span>
            <span class="wishlist-text"><?php echo $wishlist_text ?></span>
         <?php $this->gva_render_link_end($settings['link']) ?>

      <?php }else{ ?>

         <a class="wishlist-link" href="#" data-toggle="modal" data-target="#form-ajax-login-popup">
            <span class="wishlist-icon">
               <?php Icons_Manager::render_icon( $settings['selected_icon'], [ 'class' => 'icon', 'aria-hidden' => 'true' ] ); ?>
            </span>
            <span class="wishlist-text"><?php echo $wishlist_text ?></span>
         </a>

      <?php } ?>
   </div>
</div>