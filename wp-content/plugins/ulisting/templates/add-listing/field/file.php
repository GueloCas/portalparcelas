<?php
/**
 * Add listing field text
 *
 * Template can be modified by copying it to yourtheme/ulisting/add-listing/field/file.php.
 *
 * @see     #
 * @package uListing/Templates
 * @version 1.5.3
 */
?>

<div class="ulisting-form-gruop">
	<label><?php echo esc_html($attribute->title)?></label>
    <label v-if="attributes.<?php echo esc_attr($attribute->name)?>.value.length">
        <?php esc_html_e("Current value:", "ulisting"); ?> <a :href="attributes.<?php echo esc_attr($attribute->name)?>.value" download=""><?php echo esc_attr($attribute->title); ?></a>
    </label>
    <input type="file" class="form-control" ref="<?php echo esc_attr($attribute->name); ?>">
	<span v-if="errors['<?php echo esc_attr($attribute->name)?>']" class="text-danger">{{errors['<?php echo esc_attr($attribute->name)?>']}}</span>
</div>