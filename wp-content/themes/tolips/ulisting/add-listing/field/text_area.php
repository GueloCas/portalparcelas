<?php
/**
 * Add listing field text_area
 *
 * Template can be modified by copying it to yourtheme/ulisting/add-listing/field/text_area.php.
 *
 * @see     #
 * @package uListing/Templates
 * @version 1.3.8
 */
?>
<div class="ulisting-form-gruop">
	<label><?php echo esc_html($attribute->title)?></label>
	<textarea rows="10" class="form-control" v-model="attributes.<?php echo esc_attr($attribute->name)?>.value"></textarea>
	<span v-if="errors['<?php echo esc_attr($attribute->name)?>']" class="text-danger">{{errors['<?php echo esc_attr($attribute->name)?>']}}</span>
</div>
