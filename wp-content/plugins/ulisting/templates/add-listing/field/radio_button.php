<?php
/**
 * Add listing field radio_button
 *
 * Template can be modified by copying it to yourtheme/ulisting/add-listing/field/radio_button.php.
 *
 * @see     #
 * @package uListing/Templates
 * @version 1.0
 */
?>
<div class="ulisting-form-gruop">
	<label><?php echo esc_html($attribute->title)?></label>
	<div class="form-check" v-for="(val, key) in attributes.<?php echo esc_attr($attribute->name)?>.options">
		<label  class="form-check-label">
			<input class="form-check-input" type="radio" v-bind:value="val.id" v-model="attributes.<?php echo esc_attr($attribute->name)?>.value">
			{{val.text}}
		</label>
	</div>
	<span v-if="errors['<?php echo esc_attr($attribute->name)?>']" class="text-danger">{{errors['<?php echo esc_attr($attribute->name)?>']}}</span>
</div>
