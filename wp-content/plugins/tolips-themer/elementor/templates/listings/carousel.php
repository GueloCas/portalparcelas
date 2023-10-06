<?php
	$listing_type = $settings['listing_type_id'];
	$per_page = $settings['posts_per_page'];
	$item_style = 'item-' . $settings['style'];
	$sort_type = $settings['sort_type'];
	$this->add_render_attribute( 'wrapper', 'class', ['listings-carousel', $settings['text_color'] ]);

	$this->add_render_attribute('carousel', 'class', 'init-carousel-owl owl-carousel');
  ?>

<div <?php echo $this->get_render_attribute_string('wrapper'); ?>>
	<div <?php echo $this->get_render_attribute_string('carousel') ?> <?php echo $this->get_carousel_settings() ?>>
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
			    if(isset($item_template) && !empty($item_template)){
			        $sections[0]['rows'][0]['columns'][0]['elements'][0]['params']['style_template'] = $item_template;
			    }

			    $item .= \uListing\Classes\StmListingTemplate::load_template('loop/loop', [
			        'model'       => $listing,
			        'view_type'   => $view_type,
			        'listingType' => $listingType,
			        'item_class'  => 'ulisting-' . $sort_type . '-item',
			        'listing_item_card_layout' => $sections,
			        'listing_type' => $listingType
			    ]);
			    echo "<div class='listing-item'>" . $item . "</div>";
			}
		?>

	</div>
</div>

<?php wp_reset_postdata(); ?>