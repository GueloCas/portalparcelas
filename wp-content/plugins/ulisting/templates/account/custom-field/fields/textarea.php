<?php
/**
 * Account custom fields textarea
 *
 * Template can be modified by copying it to yourtheme/ulisting/account/custom-field/fields/textarea.php
 **
 * @see     #
 * @package uListing/Templates
 * @version 1.0
 */
?>
<div class="ulisting-form-gruop">
	<label> <?php echo  esc_html__($field['name'], "ulisting"); ?></label>
	<textarea  class="form-control" v-model="<?php echo esc_attr($model)?>"></textarea>
	<span v-if="errors['<?php echo esc_attr($field['slug'])?>']" style="color: red">{{errors['<?php echo esc_attr($field['slug'])?>']}}</span>
</div>


