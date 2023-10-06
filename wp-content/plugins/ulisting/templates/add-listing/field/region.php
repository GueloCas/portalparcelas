<?php
/**
 * Add listing field region
 *
 * Template can be modified by copying it to yourtheme/ulisting/add-listing/field/region.php.
 *
 * @see     #
 * @package uListing/Templates
 * @version 1.3.7
 */
?>
<?php if(isset($attribute->title)):?>
    <div class="ulisting-form-gruop">
        <label><?php echo esc_html($attribute->title)?></label>
        <div class="stm-listing-select">
            <ulisting-select2 placeholder="<?php esc_html_e('Select', 'ulisting')?>" :options='attributes.<?php echo esc_attr($attribute->name)?>.options' :text="'name'" v-model='attributes.<?php echo esc_attr($attribute->name)?>.value' theme='bootstrap4'></ulisting-select2>
            <span v-if="errors['<?php echo esc_attr($attribute->name)?>']" class="text-danger">{{errors['<?php echo esc_attr($attribute->name)?>']}}</span>
        </div>
    </div>
<?php endif;?>