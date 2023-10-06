<?php
/**
 * Account dashboard
 *
 * Template can be modified by copying it to yourtheme/ulisting/account/dashboard.php.
 **
 * @see     #
 * @package uListing/Templates
 * @version 1.0
 */
use uListing\Classes\StmListingTemplate;
?>

<?php StmListingTemplate::load_template( 'account/navigation', ['user' => $user], true );?>

<h1><?php _e('Dashboard', "ulisting")?></h1>

<?php  do_action('ulisting-account-dashboard-top', [ 'user' => $user] )?>
<?php  do_action('ulisting-account-dashboard-center', [ 'user' => $user ])?>
<?php  do_action('ulisting-account-dashboard-bottom', [ 'user' => $user ])?>
