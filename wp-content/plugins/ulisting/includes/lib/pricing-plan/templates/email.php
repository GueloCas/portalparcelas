<?php
/**
 * Pricing plan email notification
 *
 * Template can be modified by copying it to yourtheme/ulisting/includes/lib/pricing-plan/templates/email.php
 **
 * @see     #
 * @package uListing/Templates
 * @version 1.5.6
 */

$user_plan = \uListing\Lib\PricingPlan\Classes\StmUserPlan::find_one($payment->user_plan_id);
$plan = $user_plan->getPricingPlan();
$meta = $plan->getData();
$plan_status = ! empty( $status ) ? $status : $user_plan->status;
?>
<div style="display: flex; flex-direction: row; justify-content: space-between; width: 400px; margin: 0 auto">
    <div style="margin-right: 20px">
        <div><?php echo  __('Plan', 'ulisting')?>:</div>
        <div><?php echo  __('Status', 'ulisting')?>:</div>
        <div><?php echo  __('Type', 'ulisting')?>:</div>
        <div><?php echo  __('Price', 'ulisting')?>:</div>
        <div><?php echo  __('Count', 'ulisting')?>:</div>
        <div><?php echo  __('Payment Type', 'ulisting')?>:</div>
        <div><?php echo  __('Expired date', 'ulisting')?>:</div>
        <div><?php echo  __('Created date', 'ulisting')?>:</div>
    </div>
    <div>
        <div><?php echo isset( $plan->post_title ) ? esc_attr( $plan->post_title ) : esc_attr( 'Plan not found' )?></div>
        <div><?php echo esc_attr(\uListing\Lib\PricingPlan\Classes\StmUserPlan::getStatus($user_plan->status))?></div>
        <div>
            <?php if ($user_plan->payment_type !== \uListing\Lib\PricingPlan\Classes\StmPricingPlans::PRICING_PLANS_PAYMENT_TYPE_SUBSCRIPTION):?>
                <?php echo esc_attr(\uListing\Lib\PricingPlan\Classes\StmPricingPlans::pricingPlansTypeListData($user_plan->type));?>
            <?php else:;?>
                --/-/--
            <?php endif;?>
        </div>
        <div><?php echo esc_attr( ulisting_currency_format( $meta['price'] ) );?></div>
        <div><?php echo esc_attr($meta['feature_limit']); ?> <?php echo esc_attr( $meta['listing_limit'] ); ?> <?php esc_html_e( 'Listings', 'ulisting' ); ?></div>
        <div><?php echo esc_attr(\uListing\Lib\PricingPlan\Classes\StmPricingPlans::pricingPaymentTypeListData($user_plan->payment_type) );?></div>
        <div>
            <?php echo date_i18n( get_option( 'date_format' ), strtotime( $user_plan->expired_date ) ) ?>
            <?php echo date_i18n( get_option( 'time_format' ), strtotime( $user_plan->expired_date ) )?>
        </div>
        <div>
            <?php echo date_i18n( get_option( 'date_format' ), strtotime( $user_plan->created_date ) )?>
            <?php echo date_i18n( get_option( 'time_format' ), strtotime( $user_plan->created_date ) )?>
        </div>
    </div>
</div>

