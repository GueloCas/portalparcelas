<?php

use uListing\Classes\StmListingAttribute;
use uListing\Admin\Classes\StmListingAttributeList;
use uListing\Classes\StmListingType;

$object        = new StmListingAttributeList();

$form_title    =  esc_html__( 'Add New Custom Field', "ulisting");
$options_title =  esc_html__( 'Options', "ulisting");

$listing_type      = StmListingType::getDataList();
$listing_type_list = [];

foreach ($listing_type as $key => $val) {
    $listing_type_list[] = array(
		'id' => $key,
		'text' => $val,
	);
}

/**
 * Listing Attribute data
 */

$data = [
    'listing_type_list' => $listing_type_list,
    'action'            => admin_url('admin.php?page=listing_attribute'),
    'attr_type_list'    => StmListingAttribute::getType(),
    'text_domains'      => [
        'title'             => __('Title', 'ulisting'),
        'title_desc'        => __('The title is how it appears on your site.', 'ulisting'),
        'name'              => __('Slug', 'ulisting'),
        'listing_type_txt'  => __('Listing Type(s)', 'ulisting'),
        'submit_btn'        => __( 'Create', 'ulisting'),
        'choose_type'       => __('Choose on what listing types should this term be available.', 'ulisting'),
    ],
];

uListing_load_admin_scripts($data);
?>



<div class="wrap nosubsub">
	<h1 class="wp-heading-inline"><?php echo esc_html__( 'Custom Field', "ulisting"); ?></h1>

	<div id="col-container" class="wp-clearfix ">
		<div id="col-left">
			<div class="col-wrap ulisting-main">
				<div class="form-wrap">
					<h2><?php echo esc_html($form_title);?></h2>
                    <div id="uListing-main">
                        <listing-attribute-add v-if="getApiUrl"></listing-attribute-add>
                    </div>
				</div>
			</div>
		</div><!-- /col-left -->

		<div id="col-right">
			<div class="col-wrap">
				<form method="post">
					<?php
						$object->prepare_items();
						$object->display();
					?>
				</form>
			</div>
		</div><!-- /col-right -->

	</div><!-- /col-container -->
</div>