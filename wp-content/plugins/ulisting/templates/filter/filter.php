<?php
/**
 * Filter
 *
 * Template can be modified by copying it to yourtheme/ulisting/filter/filter.php.
 *
 * @see     #
 * @package uListing/Templates
 * @version 1.0
 */
use uListing\Classes\StmListingType;
use uListing\Classes\StmListingFilter;
?>
<?php echo StmListingFilter::render($listingType, StmListingType::SEARCH_FORM_ADVANCED); ?>


