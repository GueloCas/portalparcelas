<?php
/**
 * Account custom fields number
 *
 * Template can be modified by copying it to yourtheme/ulisting/account/custom-field/fields/number.php
 **
 * @see     #
 * @package uListing/Templates
 * @version 1.0
 */
?>
<div class="ulisting-form-gruop">
	<label> <?php echo  esc_html__($field['name'], "ulisting"); ?></label>
	<input type="number"
		v-model="<?php echo esc_attr($model)?>"
		class="form-control"
		placeholder="<?php esc_html_e($field['name'], "ulisting"); ?>"/>
	<span v-if="errors['<?php echo esc_attr($field['slug'])?>']" style="color: red">{{errors['<?php echo esc_attr($field['slug'])?>']}}</span>
</div>