<?php
	use Elementor\Group_Control_Image_Size;

	$image_id = $banner['image']['id']; 
	$image_url = $banner['image']['url'];
	if($image_id){
		$attach_url = Group_Control_Image_Size::get_attachment_image_src($image_id, 'image', $settings);
		if($attach_url) $image_url = $attach_url;
	}

	$image_flag_url = $banner['image_flag']['url'];

	$taxonomy = $settings['taxonomy'] ? $settings['taxonomy'] : 'job_listing_region';
	$term = $link_term = false;
		if( !empty($banner['term_slug']) ){
		$term = get_term_by( 'slug', $banner['term_slug'], $taxonomy );
		if($term){
			$ulisting_pages = get_option("stm_listing_pages");

			if( isset($ulisting_pages['listing_type_page'][$banner['listing_type']]) ){
				$listing_page_id = $ulisting_pages['listing_type_page'][$banner['listing_type']];
				if($taxonomy == 'listing-region'){
					$link_term =  esc_url(get_page_link($listing_page_id) . '?region=' . $term->term_id); 
				}
				if($taxonomy == 'listing-category'){
					$link_term =  esc_url(get_page_link($listing_page_id) . '?category=' . $term->term_id); 
				}
			}
		}
	}
?>

<div class="item listings-banner-item">
	<div class="listings-banner-item-content">

		<div class="banner-content">
			<?php if($image_flag_url){ ?>
				<div class="banner-flag">
					<img src="<?php echo esc_url($image_flag_url) ?>" alt="<?php echo esc_html($banner['title']) ?>" /> 
				</div>   
			<?php } ?>

			<div class="banner-content-inner">
				<?php if($banner['title']){ ?>
					<h3 class="title"><?php echo $banner['title'] ?></h3>
				<?php } ?>

				<?php 
					if ( $settings['show_number_content'] == 'yes' && $term ) {
						
						if( $banner['listing_type'] == '00select' ){
							if(!empty($banner['term_slug'])){
								echo '<div class="number-listings">' . sprintf(_n('%d Propiedad', '%d Propiedades', $term->count, 'tolips-themer'), $term->count) . '</div>';
							}
						}else{
							$type = get_post($banner['listing_type']);
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
							  echo '<div class="number-listings">' . sprintf(_n('%d Propiedad', '%d Propiedades', $listing_count, 'tolips-themer'), $listing_count) . '</div>';
							  wp_reset_postdata();
							}
						}
					}
				?>
			</div>   
		</div>

		<?php if($image_url){ ?>
			<div class="banner-image">
				<img src="<?php echo esc_url($image_url) ?>" alt="<?php echo esc_html($banner['title']) ?>" />
			</div>
		<?php } ?>

		<?php if($link_term){ ?>
			<a class="link-term-overlay" href="<?php echo esc_url($link_term); ?>"></a>
		<?php } ?>
					
	</div>
</div>