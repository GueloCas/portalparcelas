<?php
use \uListing\Classes\StmListingSettings;
$link = get_page_link( StmListingSettings::getPages(StmListingSettings::PAGE_ACCOUNT_PAGE) );
?>
<a href="<?php echo esc_url($link)?>"><?php echo __('account', 'ulisting')?></a>
