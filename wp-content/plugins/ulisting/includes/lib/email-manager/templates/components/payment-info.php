<?php
if (empty($user_plan) && !empty($payment))
    $user_plan = \uListing\Lib\PricingPlan\Classes\StmUserPlan::find_one($payment->user_plan_id);

$plan = $user_plan->getPricingPlan();
$meta = null;
if ( !empty( $plan ) )
    $meta = $plan->getData();
$plan_status = ! empty( $status ) ? $status : $user_plan->status;
?>

<table border="1" cellpadding="0" cellspacing="0" width="80%" align="center">
    <tr>
        <td style="padding: 5px 10px;">
            <?php echo  __('Plan', 'ulisting')?>
        </td>
        <td style="padding: 5px 10px;">
            <?php echo isset( $plan->post_title ) ? esc_attr( $plan->post_title ) : esc_attr( 'Plan not found' )?>
        </td>
    </tr>
    <tr>
        <td style="padding: 5px 10px;">
            <?php echo  __('Status', 'ulisting')?>
        </td>
        <td style="padding: 5px 10px;">
            <?php echo esc_attr(\uListing\Lib\PricingPlan\Classes\StmUserPlan::getStatus($user_plan->status))?>
        </td>
    </tr>
    <tr>
        <td style="padding: 5px 10px;">
            <?php echo  __('Type', 'ulisting')?>
        </td>
        <td style="padding: 5px 10px;">
            <?php if ($user_plan->payment_type !== \uListing\Lib\PricingPlan\Classes\StmPricingPlans::PRICING_PLANS_PAYMENT_TYPE_SUBSCRIPTION):?>
                <?php echo esc_attr(\uListing\Lib\PricingPlan\Classes\StmPricingPlans::pricingPlansTypeListData($user_plan->type));?>
            <?php else:?>
                --/-/--
            <?php endif;?>
        </td>
    </tr>
    <tr>
        <td style="padding: 5px 10px;">
            <?php echo  __('Price', 'ulisting')?>
        </td>
        <td style="padding: 5px 10px;">
            <?php echo isset($meta['price']) ? esc_attr( ulisting_currency_format( $meta['price'] ) ) : '';?>
        </td>
    </tr>
    <tr>
        <td style="padding: 5px 10px;">
            <?php echo  __('Payment Type', 'ulisting')?>
        </td>
        <td style="padding: 5px 10px;">
            <?php echo esc_attr(\uListing\Lib\PricingPlan\Classes\StmPricingPlans::pricingPaymentTypeListData($user_plan->payment_type) );?>
        </td>
    </tr>
    <tr>
        <td style="padding: 5px 10px;">
            <?php echo  __('Expired date', 'ulisting')?>
        </td>
        <td style="padding: 5px 10px;">
            <?php echo date_i18n( get_option( 'date_format' ), strtotime( $user_plan->expired_date ) ) ?>
            <?php echo date_i18n( get_option( 'time_format' ), strtotime( $user_plan->expired_date ) )?>
        </td>
    </tr>
    <tr>
        <td style="padding: 5px 10px;">
            <?php echo  __('Created date', 'ulisting')?>
        </td>
        <td style="padding: 5px 10px;">
            <?php echo date_i18n( get_option( 'date_format' ), strtotime( $user_plan->created_date ) )?>
            <?php echo date_i18n( get_option( 'time_format' ), strtotime( $user_plan->created_date ) )?>
        </td>
    </tr>
</table>