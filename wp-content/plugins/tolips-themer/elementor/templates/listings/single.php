<?php
   $listing_type = $settings['listing_type_id'];
   $per_page = $settings['posts_per_page'];
   $item_style = 'item-' . $settings['style'];
   $sort_type = $settings['sort_type'];
   $this->add_render_attribute( 'wrapper', 'class', ['listings-carousel', $settings['text_color'] ] );

   $this->add_render_attribute('carousel', 'class', 'init-carousel-owl owl-carousel');
  ?>

<div <?php echo $this->get_render_attribute_string('wrapper'); ?>>
   <div <?php echo $this->get_render_attribute_string('carousel') ?> data-items="1" data-items_lg="1" data-items_md="1" data-items_sm="1" data-items_xs="1" data-items_xx="1" data-loop="1" data-speed="800" data-auto_play="0" data-auto_play_speed="800" data-auto_play_timeout="6000" data-auto_play_hover="1" data-navigation="0" data-pagination="0" data-mouse_drag="1" data-touch_drag="1" data-stage_padding="0">
      <?php
         $view_type = 'grid';
         foreach ($listings as $listing){
          $item = "";
          $listingType =  $listing->getType();
          if( ($listing_item_card_layout = get_post_meta($listingType->ID, 'stm_listing_item_card_'.$view_type)) AND isset($listing_item_card_layout[0]) ) {
              $listing_item_card_layout = maybe_unserialize($listing_item_card_layout[0]);
              $config   = $listing_item_card_layout['config'];
              $sections = $listing_item_card_layout['sections'];
          }
         $sections[0]['rows'][0]['columns'][0]['elements'][0]['params']['style_template'] = 'item-single';
          $item .= \uListing\Classes\StmListingTemplate::load_template('loop/loop', [
              'model'       => $listing,
              'view_type'   => $view_type,
              'listingType' => $listingType,
              'item_class'  => 'ulisting' . $sort_type . '-item',
              'listing_item_card_layout' => $sections,
              'subtitle_text'            => $settings['subtitle_text']
          ]);
          echo "<div class='listing-item'>" . $item . "</div>";
      }
   ?>

   </div>
</div>

<?php wp_reset_postdata(); ?>