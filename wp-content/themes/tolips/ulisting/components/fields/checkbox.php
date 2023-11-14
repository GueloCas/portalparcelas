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
if (!isset($column) || empty($column)) {
	$column = 1;
}

?>
<?php 
if($model): 
	$title = isset($field['label']) ? esc_html__($field['label'], 'tolips') : "";
	if( empty($title) ){
		$title = isset($label) ? $label : '';
	}

	$attribute_name = isset($field['attribute_name']) ? $field['attribute_name'] : '';
	if( empty($attribute_name) ){
		$attribute_name = isset($name) ? $attribute_name : '';
	}
	
	$column_class = 'stm-col-xl-6 stm-col-lg-6 stm-col-md-6 stm-col-sm-12 stm-col-xs-12';
	switch ($column) {
		case '1':
			$column_class = 'stm-col-12';
			break;
		case '2':
			$column_class = 'stm-col-xl-6 stm-col-lg-6 stm-col-md-6 stm-col-sm-6 stm-col-xs-12';
			break;
		case '3':
			$column_class = 'stm-col-xl-4 stm-col-lg-4 stm-col-md-6 stm-col-sm-6 stm-col-xs-12';
			break;
		case '4':
			$column_class = 'stm-col-xl-3 stm-col-lg-3 stm-col-md-4 stm-col-sm-6 stm-col-xs-12';
			break;
		case '6':
			$column_class = 'stm-col-xl-2 stm-col-lg-2 stm-col-md-3 stm-col-sm-4 stm-col-xs-12';
			break;
	}

?>

<stm-field-checkbox inline-template
	    data-v-bind_key="generateRandomId()"
	    v-model='<?php echo esc_attr($model)?>'
		order_by='<?php echo esc_html($order_by)?>'
		order='<?php echo esc_html($order)?>'
		data-v-bind_callback_change='<?php echo esc_attr($callback_change)?>'
		data-v-bind_items='<?php echo esc_attr($items)?>'
		data-v-bind_hide_empty='"<?php echo esc_attr($hide_empty)?>"'
        <?php echo isset($active_tab) ? "data-v-bind_current_tab='". esc_attr(   $active_tab  ) . "'>" : '>' ?>
	
	<div class="ulisting-form-gruop ulisting-field-checkboxs checkbox-filter field-checkbox-<?php echo esc_attr($attribute_name) ?>">
		<?php if( !empty($title) ){ ?>
			<label class="title-field"><?php echo esc_html($title) ?></label>
		<?php } ?>
		
		<div class="show-results">
			<span data-v-if="value.length" class="result-item" data-v-for='item_value in value'><span data-v-for='item_list in list' data-v-if="item_list.value == item_value">{{item_list.name}}</span></span>
			<span data-v-if="!value.length">
				<?php 
					echo esc_html__( "Seleccionar ", "tolips");  
					if( !empty($title) ){
						esc_html($title);
					}else{
						esc_html__("values", "tolips");
					}
				?> 
			</span>
		</div>

		<div class="checkbox-filter-content">
			<div class="content-inner">
				<div class="stm-row">
					<div class='<?php echo esc_attr($column_class) ?> checkbox-input' data-v-for='(item, index) in list'>
						<div class="lt-checkbox pretty p-icon p-curve p-smooth">
							<input data-v-on_change='updateValue' type='checkbox' data-v-bind_value='item.value' data-v-model='value' >
							<div class="state">
								<i class="icon fas fa-check"></i>
								<label><span class="item-name">{{item.name}}</span> <span class="d-none">({{item.count}})</span> </label>
							</div>
						</div>	
					</div>
				</div>
			</div>	
		</div>	
	</div>
</stm-field-checkbox>
<?php endif;?>
