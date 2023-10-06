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
<div class="stm-row ulisting-payment-box">
    <div class="stm-col-5 plan-information">
        <h3 class="payment-title"><?php esc_html_e('Your plan', 'tolips'); ?></h3>
        <?php 
            $meta = $pricing_plan->getData();
            if( !isset($meta['price']) || empty($meta['price']) ){
                 $meta['price'] = "0.00";
            }
        ?>
        <div class="listing-pricing-plan-content">
            <div class="card">
                <div class="card-body">
                    <?php 
                        if( has_post_thumbnail($pricing_plan->ID) ){ 
                            echo '<div class="pricing-plan-thumbnail">';
                                echo get_the_post_thumbnail( $pricing_plan->ID, 'full', array( 'alt' => $pricing_plan->post_title ) ); 
                            echo '</div>';
                        } 
                    ?>  
                    <h2 class="title"><?php echo esc_html($pricing_plan->post_title)?></h2>
                    <h3 class="pricing"><?php echo ulisting_currency_format($meta['price']);?></h3>
                    <div class="plan-content">
                        <?php echo html_entity_decode($pricing_plan->post_content)?>
                    </div>
                </div>
            </div>
        </div>  
    </div>

    <div class="stm-col-7 payment-information">
        <h3 class="payment-title"><?php esc_html_e('Payment Method', 'tolips'); ?></h3>
        <p><?php esc_html_e('All transactions are secure and encrypted.', 'tolips'); ?></p>
        <div class="stm-row payment-inputs">
            <div class="stm-col-sm-6 stm-col-12">
                <label for="name"><?php echo esc_html__('Name', 'tolips') ?></label>
                <input type="text" id="name" :class="{'error': !validate_name}" placeholder="<?php echo esc_attr__('Your Name', 'tolips') ?>" v-model.trim="name">
            </div>
            <div class="stm-col-sm-6 stm-col-12">
                <label for="email"><?php echo esc_html__('Email', 'tolips') ?></label>
                <input type="email" id="email" :class="{'error': !validate_email}" placeholder="<?php echo esc_attr__('Your Email', 'tolips') ?>" v-model.trim="email">
            </div>
        </div>    

        <?php if ($data['payment_type'] == \uListing\Lib\PricingPlan\Classes\StmPricingPlans::PRICING_PLANS_PAYMENT_TYPE_SUBSCRIPTION AND ulisting_subscription_active()): ?>
            <div class="ulisting-payment-methods">
                <?php
                $payment_methods = StmPaymentMethod::get_active_payment_method_list(StmPaymentMethod::SUPPORT_SUBSCRIPTION);
                foreach ($payment_methods as $payment_method):?>
                    <?php
                    $payment_script['selected'] .= $payment_method->get_payment_script('selectd');
                    $payment_script['buy'] .= $payment_method->get_payment_script('buy');
                    $payment_script['send_request'] .= $payment_method->get_payment_script('send_request');
                    $payment_script['success'] .= $payment_method->get_payment_script('success');
                    ?>
                    <div class="payment-method payment-method-<?php echo esc_attr($payment_method->id); ?>">
                        <label>
                            <input type="radio" v-model="payment_method" v-bind:value="'<?php echo esc_attr($payment_method->id); ?>'">
                            <img style="max-width: 200px" src="<?php echo esc_url($payment_method->icon) ?>">
                        </label>
                        <?php echo html_entity_decode($payment_method->get_payment_form()) ?>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
   
        <?php if ($data['payment_type'] == \uListing\Lib\PricingPlan\Classes\StmPricingPlans::PRICING_PLANS_PAYMENT_TYPE_ONE_TIME && esc_attr($price) != 0): ?>
            <div class="ulisting-payment-methods">
                <?php
                $payment_methods = StmPaymentMethod::get_active_payment_method_list(StmPaymentMethod::SUPPORT_ONE_TIME_PAYMENT);
                foreach ($payment_methods as $payment_method):?>
                    <?php
                    $payment_script['selected'] .= $payment_method->get_payment_script('selectd');
                    $payment_script['buy'] .= $payment_method->get_payment_script('buy');
                    $payment_script['send_request'] .= $payment_method->get_payment_script('send_request');
                    $payment_script['success'] .= $payment_method->get_payment_script('success');
                    ?>
                    <div class="payment-method payment-method-<?php echo esc_attr($payment_method->id); ?>">
                        <label>
                            <input type="radio" v-model="payment_method" v-bind:value="'<?php echo esc_attr($payment_method->id); ?>'">
                            <img style="max-width: 200px" src="<?php echo esc_url($payment_method->icon) ?>">
                        </label>
                        <?php echo html_entity_decode($payment_method->get_payment_form()) ?>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
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
                <button class="btn-theme btn-medium" @click="buy" :disabled="!(validate_name && validate_email)"><?php esc_html_e("Buy Plan", "tolips") ?></button>
            </template>
            <template v-else>
                <button class="btn-theme btn-medium" @click="sendRequest"><?php esc_html_e("Place Order", "tolips") ?></button>
            </template>
        </div>
    </div>    
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
