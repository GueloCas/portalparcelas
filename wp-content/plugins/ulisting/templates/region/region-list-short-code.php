<?php
/**
 * Region list short code
 *
 * Template can be modified by copying it to yourtheme/ulisting/region/region-list-short-code.php.
 **
 * @see     #
 * @package uListing/Templates
 * @version 1.0.4
 */
?>
<div class="stm-row">
	<?php foreach ($models as $model):?>
	<?php
		$items_count = 0;
		$min_price = 0;
		$max_price = 0;

		if(isset($data[$model->term_id]['items_count']))
			$items_count = $data[$model->term_id]['items_count'];

		if(isset($data[$model->term_id]['min_price']))
			$min_price = $data[$model->term_id]['min_price'];

		if(isset($data[$model->term_id]['max_price']))
			$max_price = $data[$model->term_id]['max_price'];
	?>
		<?php $paths = get_term_meta($model->term_id, 'stm_listing_region_polygon', true);?>
		<div class="stm-col-12 stm-col-md-4">
			<div class="card">
				<img class="card-img-top" src="<?php echo esc_url($model->get_static_map_image([300,300]))?>">
				<div class="card-body">
					<h5 class="card-title">
						<a href="<?php echo esc_url($listing_type->getPageUrl()."?region=".$model->term_id) ?>">
							<?php echo esc_attr($model->name)?>
						</a>
					</h5>
					<p class="card-text">
						<strong><?php _e("Listing","ulisting");?></strong> : <?php echo esc_attr($items_count)?>
					</p>
					<p class="card-text">
						<strong><?php _e("Price","ulisting");?></strong> :
						<?php echo ulisting_currency_format($min_price)?>
						<?php if( $min_price != $max_price):?>
							- <?php echo ulisting_currency_format($max_price)?>
						<?php endif;?>
					</p>
				</div>
			</div>
		</div>
	<?php endforeach;?>
</div>