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
$step = 1;
if(isset($_GET['listingType']) AND $listingType = StmListingType::find_one($_GET['listingType'])){
	$view = 'add-listing/form';
    $step = 2;
}

?>

<h2><?php esc_html_e('Add listing', "tolips")?></h2>

<div class="listing-steps-form">
    <a class="step-item <?php echo esc_attr($step==1||$step==2?'active':'') ?>" href="<?php echo esc_url(ulisting_get_page_link('add_listing')); ?>">
        <span class="number">1.</span>
        <span class="title"><?php esc_html_e('Listing Type', 'tolips'); ?></span>
    </a>
    <span class="step-item <?php echo esc_attr($step==2?'active':'') ?>">
        <span class="number">2.</span>
        <span class="title"><?php esc_html_e('Create Listing', 'tolips') ?></span>
    </span>
    <span class="step-item">
        <span class="number">3.</span>
        <span class="title"><?php esc_html_e('Done', 'tolips') ?></span>
    </span>
</div>

<?php echo StmListingTemplate::load_template($view, array(
	'user'        => $user,
	'listing'     => null,
	'user_plans'  => $user_plans,
	'listingType' => $listingType,
	'return_url'  =>  get_page_link( StmListingSettings::getPages(StmListingSettings::PAGE_ACCOUNT_PAGE) ),
	'action'      => esc_html__('Create', "tolips"),
), true );?>





