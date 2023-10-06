<?php

$attributeSelect = null;

if (isset($_GET['attribute_id']))
	$attributeSelect = sanitize_text_field($_GET['attribute_id']);

uListing_load_admin_scripts([]);
?>
<div class="form-field form-required term-name-wrap">
	<label for="attribute_id"><?php _e( 'Custom Fields', "ulisting"); ?></label>
	<select id="attribute_id" name="attribute_id">
		<?php foreach (\uListing\Classes\StmListingAttribute::all() as $attribute):?>
			<option value="<?php echo esc_attr($attribute->id)?>" <?php echo ( $attribute->id == $attributeSelect) ? 'selected' : '' ;?> >
				<?php echo esc_html($attribute->title)?>
			</option>
		<?php endforeach;?>
	</select>
</div>

<div id="uListing-main">
    <listing-attr-options-add v-if="getApiUrl"></listing-attr-options-add>
</div>




