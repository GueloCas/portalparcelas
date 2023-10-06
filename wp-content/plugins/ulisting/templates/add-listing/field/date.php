<?php
/**
 * Add listing field date
 *
 * Template can be modified by copying it to yourtheme/ulisting/add-listing/field/date.php.
 *
 * @see     #
 * @package uListing/Templates
 * @version 1.3.7
 */
    $lang = explode("_", get_locale());
    $lang = $lang[0];
?>
<div class="ulisting-form-gruop">
	<label><?php echo esc_html($attribute->title)?></label>
	<date-picker placeholder="<?php esc_html_e('Select date', 'ulisting')?>" clearable="false"  confirm v-model="attributes.<?php echo esc_attr($attribute->name)?>.value" input-class="form-control" width="100%" class="stm-date-picker"  format="DD/MM/YYYY" lang="<?php echo esc_attr($lang)?>"></date-picker>
	<span v-if="errors['<?php echo esc_attr($attribute->name)?>']" class="text-danger">{{errors['<?php echo esc_attr($attribute->name)?>']}}</span>
</div>
