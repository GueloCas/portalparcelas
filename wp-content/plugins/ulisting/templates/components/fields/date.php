<?php
/**
 * Components fields date
 *
 * Template can be modified by copying it to yourtheme/ulisting/components/fields/date.php.
 *
 * @see     #
 * @package uListing/Templates
 * @version 1.3.7
 */
$lang = explode("_", get_locale());
$lang = $lang[0];
?>
<?php if($model):?>

<stm-field-date inline-template
	    data-v-bind_key="generateRandomId()"
		class="ulisting-form-gruop"
		data-v-model='<?php echo esc_attr($model)?>'
		placeholder="<?php echo esc_html($placeholder)?>"
		date_type='<?php echo esc_attr($date_type)?>'
		name='<?php echo esc_attr($name)?>'
		data-v-bind_callback_change='<?php echo esc_attr($callback_change)?>' >
	<div>
		<?php if(isset($field['label'])):?>
			<label><?php echo esc_html($field['label'])?></label>
		<?php endif;?>
		<date-picker data-v-if="date_type=='exact'" clearable=false  confirm data-v-model=value input-class=form-control width=100% class=stm-date-picker  data-v-on_confirm=setValue format=DD/MM/YYYY lang=<?php echo esc_attr($lang)?>></date-picker>
		<date-picker  data-v-if="date_type=='range'" clearable=false range confirm data-v-model=value input-class=form-control width=100% class=stm-date-picker data-v-on_confirm=setValue format=DD/MM/YYYY lang=<?php echo esc_attr($lang)?>></date-picker>
	</div>
</stm-field-date>
<?php endif;?>
