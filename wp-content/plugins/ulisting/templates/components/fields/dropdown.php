<?php
/**
 * Components fields dropdown
 *
 * Template can be modified by copying it to yourtheme/ulisting/components/fields/dropdown.php.
 *
 * @see     #
 * @package uListing/Templates
 * @version 1.0.5
 */

if($model):?>
		<stm-field-dropdown inline-template
			  data-v-bind_key="generateRandomId()"
			  v-model='<?php echo esc_attr($model)?>'
			  placeholder="<?php echo esc_html($placeholder)?>"
			  order_by='<?php echo esc_html($order_by)?>'
			  order='<?php echo esc_html($order)?>'
			  data-v-bind_callback_change='<?php echo esc_attr($callback_change)?>'
			  data-v-bind_items='<?php echo esc_attr($items)?>'
			  hide_empty='<?php echo esc_attr($hide_empty)?>'
			  attribute_name='<?php echo esc_attr($attribute_name)?>' >
			<div class="ulisting-form-gruop">
				<?php if(isset($field['label'])):?>
					<label><?php echo esc_html($field['label'])?></label>
				<?php endif;?>
				<ulisting-select2 data-v-bind_key="generateRandomId()" data-v-bind_options='list' data-v-bind_placeholder="placeholder" data-v-model='value' theme='bootstrap4'></ulisting-select2>
			</div>
		</stm-field-dropdown>
<?php endif;?>


