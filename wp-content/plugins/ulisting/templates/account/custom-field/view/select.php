<?php
/**
 * Account custom field view select
 *
 * Template can be modified by copying it to yourtheme/ulisting/account/custom-field/view/select.php
 **
 * @see     #
 * @package uListing/Templates
 * @version 1.0
 */

$items = [];
$value = get_user_meta($user->ID, $field['slug']);
if(isset($value[0])) {
	$value = explode(',',$value[0]);
	foreach ($field['items'] as $key => $item)
		if(in_array($item['slug'], $value))
			$items[] = $item['name'];
}
?>
<div class="ulisting-form-gruop">
	<?php echo esc_attr($field['name'])?> : <?php echo implode(',', $items)?>
</div>
