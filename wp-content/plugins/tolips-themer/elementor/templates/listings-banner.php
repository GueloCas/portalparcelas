<?php
	use Elementor\Group_Control_Image_Size;

	$this->add_render_attribute( 'block', 'class', [ 'gsc-listings-banner', 'text-' . $settings['content_align'] ] );
	
	$subtitle_text = $settings['subtitle'];
	$title_text = $settings['title'];

	$this->add_render_attribute( 'subtitle_text', 'class', 'subtitle' );
	$this->add_render_attribute( 'title_text', 'class', 'title' );

	$image_id = $settings['image']['id']; 
	$image_url = $settings['image']['url'];
	if($image_id){
		$attach_url = Group_Control_Image_Size::get_attachment_image_src($image_id, 'image', $settings);
		if($attach_url) $image_url = $attach_url;
	}

	$taxonomy = $settings['taxonomy'] ? $settings['taxonomy'] : 'job_listing_region'; 
	$term = $link_term = false;
	
	if( !empty($settings['term_slug']) ){
		$term = get_term_by( 'slug', $settings['term_slug'], $taxonomy );
		 if($term){
			$ulisting_pages = get_option("stm_listing_pages");

			if( isset($ulisting_pages['listing_type_page'][$settings['listing_type']]) ){
				$listing_page_id = $ulisting_pages['listing_type_page'][$settings['listing_type']];
				if($taxonomy == 'listing-region'){
					$link_term =  esc_url(get_page_link($listing_page_id) . '?region=' . $term->term_id); 
				}
				if($taxonomy == 'listing-category'){
					$link_term =  esc_url(get_page_link($listing_page_id) . '?category=' . $term->term_id); 
				}
			}
		}
	}
	$link = $link_term;

	if( !empty($settings['link_custom']) ) $link = $settings['link_custom'];
?>

<div <?php echo $this->get_render_attribute_string( 'block' ) ?>>
	<div class="listings-banner-content">
		
		<?php 
			if ( $settings['show_number_content'] == 'yes' && $term ) {
			
				if( $settings['listing_type'] == '00select' ){
					if(!empty($settings['term_slug'])){
						echo '<span class="number-listings">' . sprintf(_n('%d Property', '%d Properties', $term->count, 'tolips-themer'), $term->count) . '</span>';
					}
				}else{
					$type = get_post($settings['listing_type']);
					if ($type) {

					  $clauses = \uListing\Classes\StmListing::getClauses($type->ID);
					  $query   = new WP_Query(array(
							'post_type'         => 'listing',
							'orderby'           => 'rand',
							'posts_per_page'    => 1,
							'post_status'       => array('publish'),
							'tax_query'         => array(
								 array(
									  'taxonomy' => $taxonomy,
									  'field' => 'slug',
									  'terms' => $term->slug
								 )
							),
							'stm_listing_query' => $clauses,
					  ));
					  $listing_count = $query->found_posts;
					  echo '<span class="number-listings">' . sprintf(_n('%d Property', '%d Properties', $listing_count, 'tolips-themer'), $listing_count) . '</span>';
					  wp_reset_postdata();
					}
				}

			} 
		?>

		<?php if($image_url){ ?>
			<div class="banner-image">
				<img src="<?php echo esc_url($image_url) ?>" alt="<?php echo esc_html($title_text) ?>" />
			</div>
		<?php } ?>

		<div class="banner-content">
			
			<?php if($subtitle_text){ ?>
				<div class="subtitle"><?php echo esc_html($subtitle_text) ?></div>
			<?php } ?>

			<?php if($title_text){ ?>
				<h3 class="title"><?php echo $title_text ?></h3>
			<?php } ?>

		</div>

		<?php if($link){ ?>
			<a class="link-term-overlay" href="<?php echo esc_url($link); ?>"></a>
		<?php } ?>
					
	</div>
</div>