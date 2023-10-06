<?php
/**
 * Account account custom field form
 *
 * Template can be modified by copying it to yourtheme/ulisting/account/custom-field/custom-field-form.php.
 **
 * @see     #
 * @package uListing/Templates
 * @version 1.0
 */
use uListing\Classes\StmListingTemplate;
?>
<?php foreach ($custom_fields['items'] as $field):?>
	<div class="stm-col-12 stm-col-md-6">
		<?php StmListingTemplate::load_template( 'account/custom-field/fields/'.$field['type'], ['field' => $field, 'model' => 'custom_fields.'.$field['slug']], true );?>
	</div>
<?php endforeach;?>