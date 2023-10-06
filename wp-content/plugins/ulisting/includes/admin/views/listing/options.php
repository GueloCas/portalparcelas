<div class="m-t-15">
	<?php foreach ($listingType->getAttribute() as $attribute):?>
		<hr>
		<?php
			ulisting_render_template(ULISTING_ADMIN_PATH . '/views/attribute_template/'.$attribute->type.'.php', [
				'name' => 'listing[options]['.$attribute->name.']',
				'attribute' => $attribute,
				'listing' => $listing,
				'options' => ['class' => 'form-control']
			], true);
		?>
	<?php endforeach;?>
	<input type="hidden" name="listing[feature_thumbnail_id]" value="<?php esc_attr_e( get_post_thumbnail_id() ); ?>">
</div>
