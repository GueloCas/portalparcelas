<?php
/**
 * Add listing add
 *
 * Template can be modified by copying it to yourtheme/ulisting/add-listing/add.php.
 **
 * @see     #
 * @package uListing/Templates
 * @version 1.0
 */
use uListing\Classes\StmListingType;
use uListing\Classes\StmListingTemplate;
use uListing\Classes\StmListingSettings;

$view = 'add-listing/listing-type';
$listingType = null;
if(isset($_GET['listingType']) AND $listingType = StmListingType::find_one(sanitize_text_field($_GET['listingType'])))
	$view = 'add-listing/form';
?>

<h1><?php esc_html_e('Add listing', "ulisting")?></h1>

<?php echo StmListingTemplate::load_template($view, array(
	'user'        => $user,
	'listing'     => null,
	'user_plans'  => $user_plans,
	'listingType' => $listingType,
	'return_url'  =>  get_page_link( StmListingSettings::getPages(StmListingSettings::PAGE_ACCOUNT_PAGE) ),
	'action'      => esc_html__('Create', "ulisting"),
), true );?>





