<?php
use uListing\Classes\StmListingAttribute;
use uListing\Classes\StmListingType;

$form_title =  esc_html__( 'Edit Custom Field', "ulisting");
$options_title =  esc_html__( 'Options', "ulisting");
$submit_btn =  esc_html__( 'Update', "ulisting");
/**
 * Listing Attribute Edit
 */
$data = [
    'attr_id' => isset($_GET['attribute_id']) ? (int)(sanitize_text_field($_GET['attribute_id'])) : null,
];
uListing_load_admin_scripts($data);
?>

<div class="wrap nosubsub ulisting-main">
	<h1><?php echo esc_html($form_title);?></h1>
    <div id="uListing-main">
        <listing-attribute-edit v-if="getApiUrl"></listing-attribute-edit>
    </div>
</div>