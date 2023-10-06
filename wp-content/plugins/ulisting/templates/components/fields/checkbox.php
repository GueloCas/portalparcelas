<?php
/**
 * Components fields checkbox
 *
 * Template can be modified by copying it to yourtheme/ulisting/components/fields/checkbox.php.
 *
 * @see     #
 * @package uListing/Templates
 * @version 2.0.4
 */
?>
<?php if($model):?>
<stm-field-checkbox inline-template
	    data-v-bind_key="generateRandomId()"
	    v-model='<?php echo esc_attr($model)?>'
		order_by='<?php echo esc_html($order_by)?>'
		order='<?php echo esc_html($order)?>'
		data-v-bind_callback_change='<?php echo esc_attr($callback_change)?>'
		data-v-bind_items='<?php echo esc_attr($items)?>'
		data-v-bind_hide_empty='"<?php echo esc_attr($hide_empty)?>"'
        <?php echo isset($active_tab) ? "data-v-bind_current_tab='". esc_attr(   $active_tab  ) . "'>" : '>' ?>
	<div class="ulisting-form-gruop">
		<label><?php echo (isset($field['label'])) ? esc_html__($field['label'], "ulisting") : "" ?></label>
		<div class="stm-row">
			<div class='stm-col-<?php echo 12 / 1; ?> checkbox-input' data-v-for='(item, index) in list'>
				<label>
					<input data-v-on_change='updateValue' type='checkbox' data-v-bind_value='item.value' data-v-model='value' >
					{{item.name}} <span> ( {{item.count}} )</span>
				</label>
			</div>
		</div>
	</div>
</stm-field-checkbox>
<?php endif;?>
