<?php
/**
 * Pricing plan payment
 *
 * Template can be modified by copying it to yourtheme/ulisting/pricing-plan/payment.php.
 **
 * @see     #
 * @package uListing/Templates
 * @version 1.5.7
 */

use uListing\Classes\StmPaymentMethod;
use uListing\Classes\StmUser;

$payment_data = array(
    'pricing_plan_id' => $pricing_plan->ID
);
$data = $pricing_plan->getData();
$payment_data['my_plans_url'] = StmUser::getUrl('my-plans');
$payment_script = [
    "selected" => "",
    "buy" => "",
    "send_request" => "",
    "success" => "",
];
$current_user = wp_get_current_user();
$price = isset($data['price']) ? $data['price'] : 0;
$payment_data = apply_filters('ulisting_pricing_plan_payment_method_data', $payment_data);
?>
<h3 class="text-center"><?php echo esc_html($pricing_plan->post_title) ?></h3>
<hr>
<div class="stm-row">
    <div class="stm-col-12">
        <label for="name"></label>
        <input type="text" id="name" :class="{'error': !validate_name}" placeholder="<?php esc_attr_e('Your Name', 'ulisting') ?>" v-model="name">
    </div>
    <div class="stm-col-12">
        <label for="email"></label>
        <input type="email" id="email" :class="{'error': !validate_email}" placeholder="<?php esc_attr_e('Your Email', 'ulisting') ?>" v-model="email">
    </div>
</div>
<?php if ($data['payment_type'] == \uListing\Lib\PricingPlan\Classes\StmPricingPlans::PRICING_PLANS_PAYMENT_TYPE_SUBSCRIPTION AND ulisting_subscription_active()): ?>
    <div class="stm-row">
        <?php
        $payment_methods = StmPaymentMethod::get_active_payment_method_list(StmPaymentMethod::SUPPORT_SUBSCRIPTION);
        foreach ($payment_methods as $payment_method):?>
            <?php
            $payment_script['selected'] .= $payment_method->get_payment_script('selectd');
            $payment_script['buy'] .= $payment_method->get_payment_script('buy');
            $payment_script['send_request'] .= $payment_method->get_payment_script('send_request');
            $payment_script['success'] .= $payment_method->get_payment_script('success');
            ?>

            <div class="stm-col text-center">
                <label>
                    <input type="radio" v-model="payment_method"
                           v-bind:value="'<?php echo esc_attr($payment_method->id); ?>'">
                    <br>
                    <img style="max-width: 200px" src="<?php echo esc_url($payment_method->icon) ?>">
                </label>
                <?php echo html_entity_decode($payment_method->get_payment_form()) ?>
            </div>
        <?php endforeach; ?>
    </div>
<?php endif; ?>

<?php if ($data['payment_type'] == \uListing\Lib\PricingPlan\Classes\StmPricingPlans::PRICING_PLANS_PAYMENT_TYPE_ONE_TIME && esc_attr($price) != 0): ?>
    <div class="stm-row">
        <?php
        $payment_methods = StmPaymentMethod::get_active_payment_method_list(StmPaymentMethod::SUPPORT_ONE_TIME_PAYMENT);
        foreach ($payment_methods as $payment_method):?>
            <?php
            $payment_script['selected'] .= $payment_method->get_payment_script('selectd');
            $payment_script['buy'] .= $payment_method->get_payment_script('buy');
            $payment_script['send_request'] .= $payment_method->get_payment_script('send_request');
            $payment_script['success'] .= $payment_method->get_payment_script('success');
            ?>
            <div class="stm-col text-center">
                <label>
                    <input type="radio" v-model="payment_method"
                           v-bind:value="'<?php echo esc_attr($payment_method->id); ?>'">
                    <br>
                    <img style="max-width: 200px" src="<?php echo esc_url($payment_method->icon) ?>">
                </label>
                <?php echo html_entity_decode($payment_method->get_payment_form()) ?>
            </div>
        <?php endforeach; ?>
    </div>
<?php endif; ?>
<hr>
<div v-if="errors" class="text-center">
    <ul>
        <li v-for="error in errors">
            {{error}}
        </li>
    </ul>
</div>

<div v-if="message" class="text-center">
    <p>{{message}}</p>
</div>

<div v-if="payment_loading" class="text-center">
    <div class="stm-spinner">
        <div></div>
        <div></div>
        <div></div>
        <div></div>
        <div></div>
    </div>
</div>

<div v-if="!payment_loading" class="text-center">
    <template v-if="<?php echo esc_attr($price) ?> != 0">
        <button class="btn btn-success" @click="buy" :disabled="!(validate_name && validate_email)"><?php esc_html_e("Pay", "ulisting") ?></button>
    </template>
    <template v-else>
        <button class="btn btn-success" @click="sendRequest"><?php esc_html_e("Place Order", "ulisting") ?></button>
    </template>
</div>

<?php
wp_add_inline_script(
    "stm-pricing-plan",
    "  
            function ulisting_pricing_plan_payment_selectd(pricing_plan_payment){
                " . $payment_script['selected'] . "
            }
            function ulisting_pricing_plan_payment_buy(pricing_plan_payment){
                " . $payment_script['buy'] . "
            }
            function ulisting_pricing_plan_payment_send_request(pricing_plan_payment){
                " . $payment_script['send_request'] . "
            }
            function ulisting_pricing_plan_payment_success(pricing_plan_payment, response){
                " . $payment_script['success'] . "
            }
           var stm_payment_data = json_parse('" . ulisting_convert_content(json_encode($payment_data)) . "');
           var current_user = json_parse('". ulisting_convert_content(json_encode($current_user->data)) ."');
	     ",
    "before"
); ?>
