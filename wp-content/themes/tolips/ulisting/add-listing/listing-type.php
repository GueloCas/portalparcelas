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
	<div class="stm-row">
	<?php foreach ($listingTypes as $key => $value):?>
		<div class="stm-col-lg-3 stm-col-md-4 stm-col-sm-6">
			<div class="card-listing-type">
				<div class="card-body">
					<div class="card-title"><?php echo esc_html($value)?></div>
				</div>
				<a href="<?php echo esc_url($addListingPageUrl).'?listingType='.$key?>" class="card-link"></a>
			</div>
		</div>
	<?php endforeach;?>
	</div>
</div>

