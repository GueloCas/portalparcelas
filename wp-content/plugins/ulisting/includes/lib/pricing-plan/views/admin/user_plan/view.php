<?php
use uListing\Lib\PayPal\Classes\PayPal;
use uListing\Lib\PricingPlan\Classes\StmUserPlan;
use uListing\Lib\PricingPlan\Classes\StmPricingPlans;
?>
<div id="stm-paypal-settings" class="ulisting-main">
	<div class="panel-custom p-b-30">
		<h3><?php esc_html_e("User plan view", "ulisting")?></h3>
		<div class="ulisting-main p-t-30 p-b-30 p-l-30 p-r-30 ulisting-bg-white">
			<div class="stm-row">
				<div class="stm-col-12 stm-col-md-3">
					<strong><?php esc_html_e("ID", "ulisting")?></strong>
				</div>
				<div class="stm-col-12 stm-col-md-9">
					<?php echo esc_html($user_plan->id)?>
				</div>
				<hr>
				<div class="stm-col-12 stm-col-md-3">
					<strong><?php esc_html_e("User", "ulisting")?></strong>
				</div>
				<div class="stm-col-12 stm-col-md-9">
					<?php
					$user = $user_plan->getUser();
					echo esc_html($user->ID).' '.esc_html($user->data->user_login).' '.esc_html($user->data->user_email);
					?>
				</div>
				<hr>
				<div class="stm-col-12 stm-col-md-3">
					<strong><?php esc_html_e("Plan", "ulisting")?></strong>
				</div>
				<div class="stm-col-12 stm-col-md-9">
					<?php echo esc_html($user_plan->getPricingPlan()->post_title)?>
				</div>
				<hr>
				<div class="stm-col-12 stm-col-md-3">
					<strong><?php esc_html_e("Status", "ulisting")?></strong>
				</div>
				<div class="stm-col-12 stm-col-md-9">
					<?php
                    $output = '';
					switch ($user_plan->status) {
						case StmUserPlan::STATUS_ACTIVE:
                            $output = '<span class="ulisting-main"><span class="label label-success">'.StmUserPlan::getStatus($user_plan->status).'</span></span> ';
							break;
						case StmUserPlan::STATUS_PENDING:
                            $output = '<span class="ulisting-main"><span class="label label-warning">'.StmUserPlan::getStatus($user_plan->status).'</span></span>';
							break;
						case StmUserPlan::STATUS_INACTIVE:
                            $output = '<span class="ulisting-main"> <span class="label label-default">'.StmUserPlan::getStatus($user_plan->status).'</span></span>';
							break;
						case StmUserPlan::STATUS_CANCELED:
                            $output = '<span class="ulisting-main"><span class="label label-danger">'.StmUserPlan::getStatus($user_plan->status).'</span></span>';
							break;
						default:
							echo '-------';
							break;
					}
				?>
				</div>
                <?php if($user_plan->payment_type !== \uListing\Lib\PricingPlan\Classes\StmPricingPlans::PRICING_PLANS_PAYMENT_TYPE_SUBSCRIPTION):?>
                    <hr>
                    <div class="stm-col-12 stm-col-md-3">
                        <strong><?php esc_html_e("Type", "ulisting")?></strong>
                    </div>
                    <div class="stm-col-12 stm-col-md-9">
                        <?php echo StmPricingPlans::pricingPlansTypeListData($user_plan->type) ?>
                    </div>
                <?php endif;?>
				<hr>
				<div class="stm-col-12 stm-col-md-3">
					<strong><?php esc_html_e("Payment type", "ulisting")?></strong>
				</div>
				<div class="stm-col-12 stm-col-md-9">
					<?php echo StmPricingPlans::pricingPaymentTypeListData($user_plan->payment_type)?>
				</div>
				<hr>
				<?php if($user_plan->payment_type == \uListing\Lib\PricingPlan\Classes\StmPricingPlans::PRICING_PLANS_PAYMENT_TYPE_SUBSCRIPTION):?>
					<div class="stm-col-12 stm-col-md-3">
						<strong><?php esc_html_e("Expired date", "ulisting")?></strong>
					</div>
					<div class="stm-col-12 stm-col-md-9">
						<?php echo  date_i18n( get_option( 'date_format' ), strtotime( $user_plan->expired_date ) );?>
						<?php echo  date_i18n( get_option( 'time_format' ), strtotime( $user_plan->expired_date ) );?>
					</div>

					<hr>
				<?php endif;?>
				<div class="stm-col-12 stm-col-md-3">
					<strong><?php esc_html_e("Created date", "ulisting")?></strong>
				</div>
				<div class="stm-col-12 stm-col-md-9">
					<?php echo  date_i18n( get_option( 'date_format' ), strtotime( $user_plan->created_date ) );?>
					<?php echo  date_i18n( get_option( 'time_format' ), strtotime( $user_plan->created_date ) );?>
				</div>
				<hr>
				<div class="stm-col-12 stm-col-md-3">
					<strong><?php esc_html_e("Updated date", "ulisting")?></strong>
				</div>
				<div class="stm-col-12 stm-col-md-9">
					<?php echo  date_i18n( get_option( 'date_format' ), strtotime( $user_plan->updated_date ) );?>
					<?php echo  date_i18n( get_option( 'time_format' ), strtotime( $user_plan->updated_date ) );?>
				</div>
			</div>
		</div>
		<h3><?php esc_html_e("Payment History", "ulisting")?></h3>
		<div class="ulisting-main p-t-30 p-b-30 p-l-30 p-r-30 ulisting-bg-white">
			<div class="stm-row">
				<?php ulisting_render_template(ULISTING_PATH_LIB_PRICING_PLAN . '/views/admin/user_plan/payment-history.php', [ 'user_plan' => $user_plan ], true);?>
			</div>
		</div>
	</div>
</div>