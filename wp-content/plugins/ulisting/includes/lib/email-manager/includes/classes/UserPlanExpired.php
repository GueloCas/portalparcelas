<?php
namespace uListing\Lib\Email\Classes;

use uListing\Lib\Email\Classes\Basic\UlistingEmail;
use uListing\Lib\PricingPlan\Classes\StmPricingPlans;

class UserPlanExpired extends UlistingEmail {
    /**
     * Replace all short-codes
     * @param $type
     * @param $args
     * @return mixed
     */
    public function replace_shortcodes($type, $args) {
        $content   = $this->email_manager[$type];
        $user_plan = $args['user_plan'];

        $plan_type = $user_plan->payment_type !== \uListing\Lib\PricingPlan\Classes\StmPricingPlans::PRICING_PLANS_PAYMENT_TYPE_SUBSCRIPTION;
        $plan_type = $plan_type ? StmPricingPlans::pricingPlansTypeListData($user_plan->type) : '--/--/--';

        $content = str_replace('\\\"','\"', $content);
        $content = str_replace('[order_id]', $user_plan->plan_id, $content);
        $content = str_replace('[customer_name]', $args['user_name'], $content);
        $content = str_replace('[site_name]', $this->parse_component('site-name', []), $content);
        $content = str_replace('[plan_type]', $plan_type, $content);
        $content = str_replace('[payment_info]', $this->parse_component('payment-info', ['user_plan' => $args['user_plan']]), $content);
        return $content;
    }
}