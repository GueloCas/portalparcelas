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

<div class="ulisting-main">
	<div class="stm-row">
		<div class="stm-col-6">
			<a class="btn btn-default w-full" href="<?php echo ulisting_get_page_link('add_listing')?>"> <?php _e('Add listing', "ulisting")?> </a>
		</div>
		<div class="stm-col-6">
			<a class="btn btn-default w-full" href="<?php echo ulisting_get_page_link('pricing_plan')?>"><?php _e('Buy plan', "ulisting")?> </a>
		</div>
	</div>
	<hr>
	<div class="stm-row">
		<div class="stm-col-12 stm-col-md-12">
			<?php StmUser::get_endpoint_template(ulisting_page_endpoint(), [ "user" => $user ]) ?>
			<?php echo  (ulisting_page_endpoint()) ? StmUser::get_endpoint_template(ulisting_page_endpoint(), [ "user" => $user ]) : StmListingTemplate::load_template("account/dashboard" , ['user' => $user]);?>
		</div>
	</div>
</div>



