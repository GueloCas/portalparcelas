<?php
/**
 * Components fields search
 *
 * Template can be modified by copying it to yourtheme/ulisting/components/fields/search.php.
 *
 * @see     #
 * @package uListing/Templates
 * @version 1.0.4
 */
?>
<?php if($model):?>
	<stm-field-search inline-template
		  class="ulisting-form-gruop "
		  v-model='<?php echo esc_attr($model)?>'
		  placeholder="<?php echo esc_html($placeholder)?>"
		  data-v-bind_callback_change='<?php echo esc_attr($callback_change)?>' >
		<div>
			<?php if(isset($field['label'])):?>
				<label><?php echo esc_html($field['label'])?></label>
			<?php endif;?>
			<input class="form-control" type="text" data-v-model="value" data-v-on_input="updateValue($event.target.value)"  data-v-bind_name="name" data-v-bind_placeholder="placeholder">
		</div>
	</stm-field-search>
<?php endif;?>

