<?php
/**
 * Add listing listing type
 *
 * Template can be modified by copying it to yourtheme/ulisting/add-listing/listing-type.php.
 **
 * @see     #
 * @package uListing/Templates
 * @version 1.0
 */
use uListing\Classes\StmListingType;
use uListing\Classes\StmListingSettings;

$addListingPageUrl = StmListingSettings::getAddListingPageUrl();
$listingTypes = StmListingType::getDataList();
?>

<div class="ulisting-main">
	<hr>
	<div class="stm-row">
	<?php foreach ($listingTypes as $key => $value):?>
		<div class="stm-col-3 m-b-15">
			<div class="card">
				<div class="card-body">
					<h6 class="card-title"><?php echo esc_html($value)?></h6>
					<a href="<?php echo esc_url($addListingPageUrl).'?listingType='.$key?>" class="btn btn-primary"><?php _e('Select', "ulisting")?></a>
				</div>
			</div>
		</div>
	<?php endforeach;?>
	</div>
</div>

