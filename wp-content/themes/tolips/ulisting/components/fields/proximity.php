<?php
/**
 * Components fields proximity
 *
 * Template can be modified by copying it to yourtheme/ulisting/components/fields/proximity.php.
 *
 * @see     #
 * @package uListing/Templates
 * @version 1.0.3
 */
?>
<?php if($model):?>

<stm-field-proximity inline-template
	data-v-bind_key="generateRandomId()"
	class="ulisting-form-gruop"
	v-model='<?php echo esc_attr($model)?>'
	data-v-bind_callback_change='<?php echo esc_attr($callback_change)?>'
	units='<?php echo esc_attr($units === 'miles' ? 'mi' : 'km')?>'
	min='<?php echo esc_attr($min)?>'
	max='<?php echo esc_attr($max)?>'>
	<div>
		<?php if(isset($field['label'])):?>
			<label><?php echo esc_html($field['label'])?></label>
		<?php endif;?>
		<vue-range-slider data-v-bind_key="generateRandomId()" data-v-bind_min="min" data-v-bind_max="max"  data-v-bind_postfix="' '+units"  data-v-bind_from="value" data-v-on_callback='updateValue'></vue-range-slider>
	</div>
</stm-field-proximity>
<?php endif;?>
