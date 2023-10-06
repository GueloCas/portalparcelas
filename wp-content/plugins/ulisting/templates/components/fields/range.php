<?php
/**
 * Components fields range
 *
 * Template can be modified by copying it to yourtheme/ulisting/components/fields/range.php.
 *
 * @see     #
 * @package uListing/Templates
 * @version 1.0.3
 */
?>
<?php if($model):?>

<stm-field-range
	  inline-template
	  class="ulisting-form-gruop"
	  data-v-bind_key="generateRandomId()"
	  v-model='<?php echo esc_attr($model);?>'
	  data-v-bind_callback_change='<?php echo esc_attr($callback_change);?>'
	  prefix='<?php echo esc_html($prefix);?>'
	  suffix='<?php echo esc_html($suffix);?>'
      data-v-bind_sign='false'
	  min='<?php echo esc_attr($min);?>'
	  max='<?php echo esc_attr($max);?>'>
	<div>
		<?php if(isset($field['label'])):?>
			<label><?php echo esc_html($field['label'])?></label>
		<?php endif;?>
		<vue-range-slider data-v-bind_min="min"
						  data-v-bind_max="max"
						  data-v-bind_from="from"
						  data-v-bind_to="to"
						  type="double"
						  data-v-bind_prefix="prefix"
						  data-v-bind_postfix="suffix"
						  data-v-on_callback='updateValue'
						  data-v-bind_key="generateRandomId()" >
		</vue-range-slider>
	</div>
</stm-field-range>
<?php endif;?>
