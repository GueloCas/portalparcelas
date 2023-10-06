<?php
if ( ! defined( 'ABSPATH' ) ) exit;

if ( ! class_exists( 'WP_List_Table' ) )
    require_once( ABSPATH . 'wp-admin/includes/class-wp-list-table.php' );

if ( is_admin() ) {
    require_once ULISTING_PATH.'/includes/admin/classes/StmAdminMenu.php';
    require_once ULISTING_PATH.'/includes/admin/classes/StmAdminNotice.php';
    require_once ULISTING_PATH.'/includes/admin/classes/StmListingAttributeList.php';
    require_once ULISTING_PATH.'/includes/admin/classes/StmListingAttributePanel.php';
    require_once ULISTING_PATH.'/includes/admin/classes/UlistingSearchListTable.php';
    require_once ULISTING_PATH.'/includes/admin/classes/UlistingSearchPanel.php';
    require_once ULISTING_PATH.'/includes/install.php';
    require_once ULISTING_PATH.'/includes/admin/enqueue.php';
}
require_once ULISTING_PATH.'/includes/lib/email-manager/email-manager.php';
require_once ULISTING_PATH."/includes/functions.php";
require_once ULISTING_PATH.'/includes/admin/classes/StmEmailTemplateManager.php';
require_once ULISTING_PATH.'/includes/classes/abstract/autoload.php';
require_once ULISTING_PATH.'/includes/classes/builder/autoload.php';
require_once ULISTING_PATH.'/includes/classes/vendor/autoload.php';
require_once ULISTING_PATH.'/includes/enqueue.php';
require_once ULISTING_PATH.'/includes/classes/StmUpdates.php';
require_once ULISTING_PATH.'/includes/classes/Notices.php';
require_once ULISTING_PATH.'/includes/classes/UlistingSanitize.php';
require_once ULISTING_PATH.'/includes/classes/StmAjaxAction.php';
require_once ULISTING_PATH.'/includes/classes/StmAttributeRelationshMeta.php';
require_once ULISTING_PATH.'/includes/classes/StmAttributeTermRelationships.php';
require_once ULISTING_PATH.'/includes/classes/StmComment.php';
require_once ULISTING_PATH.'/includes/classes/StmCron.php';
require_once ULISTING_PATH.'/includes/classes/StmIcons.php';
require_once ULISTING_PATH.'/includes/classes/StmImport.php';
require_once ULISTING_PATH.'/includes/classes/StmInventoryLayout.php';
require_once ULISTING_PATH.'/includes/classes/StmListing.php';
require_once ULISTING_PATH.'/includes/classes/StmListingAttribute.php';
require_once ULISTING_PATH.'/includes/classes/StmListingAttributeOption.php';
require_once ULISTING_PATH.'/includes/classes/StmListingAttributeRelationships.php';
require_once ULISTING_PATH.'/includes/classes/StmListingAuth.php';
require_once ULISTING_PATH.'/includes/classes/StmListingCategory.php';
require_once ULISTING_PATH.'/includes/classes/StmListingFilter.php';
require_once ULISTING_PATH.'/includes/classes/StmListingItemCardLayout.php';
require_once ULISTING_PATH.'/includes/classes/StmListingRegion.php';
require_once ULISTING_PATH.'/includes/classes/StmListingSettings.php';
require_once ULISTING_PATH.'/includes/classes/StmListingSingleLayout.php';
require_once ULISTING_PATH.'/includes/classes/StmListingTemplate.php';
require_once ULISTING_PATH.'/includes/classes/StmListingType.php';
require_once ULISTING_PATH.'/includes/classes/StmListingTypeRelationships.php';
require_once ULISTING_PATH.'/includes/classes/StmListingUserRelations.php';
require_once ULISTING_PATH.'/includes/classes/StmModules.php';
require_once ULISTING_PATH.'/includes/classes/StmPaginator.php';
require_once ULISTING_PATH.'/includes/classes/StmPaymentMethod.php';
require_once ULISTING_PATH.'/includes/classes/StmQuery.php';
require_once ULISTING_PATH.'/includes/classes/StmSystemStatus.php';
require_once ULISTING_PATH.'/includes/classes/StmUser.php';
require_once ULISTING_PATH.'/includes/classes/StmVerifyNonce.php';
require_once ULISTING_PATH.'/includes/classes/UlistingNotifications.php';
require_once ULISTING_PATH.'/includes/classes/UlistingPageStatistics.php';
require_once ULISTING_PATH.'/includes/classes/UlistingPageStatisticsMeta.php';
require_once ULISTING_PATH.'/includes/classes/UlistingSearch.php';
require_once ULISTING_PATH.'/includes/classes/UlistingUserRole.php';
require_once ULISTING_PATH.'/includes/init.php';
require_once ULISTING_PATH.'/includes/route.php';
require_once ULISTING_PATH.'/includes/lib/paypal/paypal.php';
require_once ULISTING_PATH.'/includes/lib/paypal-standard/paypal-standard.php';
require_once ULISTING_PATH.'/includes/lib/pricing-plan/pricing-plan.php';
require_once ULISTING_PATH.'/includes/lib/stripe/stripe.php';
require_once ULISTING_PATH.'/includes/config.php';


foreach ( apply_filters("ulisting_autoload_files", []) as $file ) {
    if(file_exists(ULISTING_PATH."/{$file}"))
        require_once ULISTING_PATH."/{$file}";
}

$stm_query = new \uListing\Classes\StmQuery();
