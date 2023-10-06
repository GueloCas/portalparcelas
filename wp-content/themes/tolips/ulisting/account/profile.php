<?php
/**
 * Account profile
 *
 * Template can be modified by copying it to yourtheme/ulisting/account/profile.php.
 **
 * @see     #
 * @package uListing/Templates
 * @version 1.0
 */
use uListing\Classes\StmUser;
use uListing\Classes\StmListingTemplate;

$user = new StmUser(get_current_user_id());
?>

	<?php StmUser::get_endpoint_template(ulisting_page_endpoint(), [ "user" => $user ]) ?>
	<?php echo  (ulisting_page_endpoint()) ? StmUser::get_endpoint_template(ulisting_page_endpoint(), [ "user" => $user ]) : StmListingTemplate::load_template("account/dashboard" , ['user' => $user]);?>



