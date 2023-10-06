<?php
/**
 * Add listing field select
 *
 * Template can be modified by copying it to yourtheme/ulisting/add-listing/field/select.php.
 *
 * @see     #
 * @package uListing/Templates
 * @version 2.0.4
 */
?>
<div class="ulisting-form-gruop">
    <label><?php echo esc_html($attribute->title)?></label>
    <ulisting-select2  placeholder="<?php esc_html_e('Select', 'ulisting')?>" :key="<?php $attribute->id?>" :options='attributes.<?php echo esc_attr($attribute->name)?>.options' v-model='attributes.<?php echo esc_attr($attribute->name)?>.value' theme='bootstrap4'></ulisting-select2>
    <span v-if="errors['<?php echo esc_attr($attribute->name)?>']" class="text-danger">{{errors['<?php echo esc_attr($attribute->name)?>']}}</span>
</div>