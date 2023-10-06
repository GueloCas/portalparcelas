<?php
/**
 * Add listing field yes_no
 *
 * Template can be modified by copying it to yourtheme/ulisting/add-listing/field/yes_no.php.
 *
 * @see     #
 * @package uListing/Templates
 * @version 1.0
 */
?>
<div class="form-group">
	<label><?php echo esc_html($attribute->title)?></label>
	<div class="form-check">
		<label class="form-check-label">
			<input class="form-check-input" type="radio" value="1" v-model="attributes.<?php echo esc_attr($attribute->name)?>.value">
			<?php esc_html_e('Yes', "ulisting")?>
		</label>
	</div>
	<div class="form-check">
		<label  class="form-check-label">
			<input class="form-check-input" type="radio" value="0" v-model="attributes.<?php echo esc_attr($attribute->name)?>.value">
			<?php esc_html_e('No', "ulisting")?>
		</label>
	</div>
	<span v-if="errors['<?php echo esc_attr($attribute->name)?>']" class="text-danger">{{errors['<?php echo esc_attr($attribute->name)?>']}}</span>
</div>


