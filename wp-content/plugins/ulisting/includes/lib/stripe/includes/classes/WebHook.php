<?php
namespace uListing\Lib\Stripe\Classes;


use uListing\Admin\Classes\StmEmailTemplateManager;
use uListing\Lib\Stripe\Classes\Stripe;
use uListing\Classes\Vendor\Validation;
use uListing\Lib\PricingPlan\Classes\StmPayment;
use uListing\Lib\PricingPlan\Classes\StmUserPlan;

class WebHook {

	public static function getWebHookUrl() {
		return get_site_url(null, 'payment/stripe/web-hook');
	}

	public static function verifyingSignatures($payload){
		$stripe_data =  Stripe::getData();
		$sig_header  = (isset($_SERVER['HTTP_STRIPE_SIGNATURE']))?$_SERVER['HTTP_STRIPE_SIGNATURE']:null;
		if (isset($stripe_data['whsec']))
			$endpoint_secret = $stripe_data['whsec'];

		try {
			$event = \Stripe\Webhook::constructEvent(
				$payload, $sig_header, $endpoint_secret
			);
		} catch(\UnexpectedValueException $e) {
			ulisting_log("#uListing_stripe invalid = " . $payload);
			http_response_code(400); // PHP 5.4 or greater
			exit();
		} catch(\Stripe\Error\SignatureVerification $e) {
			ulisting_log("#uListing_stripe invalid = " . $payload);
			http_response_code(400); // PHP 5.4 or greater
			exit();
		}
		http_response_code(200); // PHP 5.4 or greater
	}

	public static function web_hook(){
		if( empty($data = file_get_contents('php://input')) )
			exit();

		$stripe = new Stripe();
		self::verifyingSignatures($data);

		ulisting_log(get_site_url()." = ".$data);

		$data = json_decode($data, true);
		$data_type = explode('.', $data['type']);

		if($data_type[0] == 'charge' ) {

			$charge = $data['data']['object'];

			if( !($payment = StmPayment::query()->where("transaction", $charge['id'])->findOne()) )
				$payment = new StmPayment();

			if($payment->status == Stripe::PAYMENT_STATUS_SUCCEEDED)
				exit();

			$invoice = Stripe::invoice_retrieve($charge['invoice']);
			if(!$invoice['success'])
				exit();

			$invoice = $invoice['invoice'];
			if ($invoice->collection_method == 'charge_automatically' AND $user_plan = StmUserPlan::getUserPlanByStripeId($invoice->subscription)) {
				$plan_data = $user_plan->getPricingPlan()->getData();
				if( (float) ($plan_data['price']) == (float) $charge['amount']/100){
				    $archived_status         = $payment->status;
					$payment->user_plan_id   = $user_plan->id;
					$payment->payment_method = $stripe->id;
					$payment->status         = $charge['status'];
					$payment->amount         = $plan_data['price'];
					$payment->transaction    = $charge['id'];
					$payment->save();

					//if payment status pending add meta data_pending
					if ( $payment->status == Stripe::PAYMENT_STATUS_PENDING )
						$payment->setMeta('data_pending', $data);

					//if payment status successded add meta data_completed and set active user plan
					if ( $payment->status == Stripe::PAYMENT_STATUS_SUCCEEDED )
                        $payment->setMeta('data_completed', $data);
                        $user_plan->setActiveStatus();

                    $info = $payment->getMeta('user_info');
                    $details = json_decode($info->meta_value);
                    $args = [
                        'date'           => date('Y-m-d H:i:s'),
                        'payment'        => $payment,
                        'user_name'      => isset($details->name) ? $details->name : '',
                        'user_email'     => isset($details->email) ? $details->email : '',
                        'plan_id'        => isset($payment->user_plan_id) ? $payment->user_plan_id : null,

                        'payment_status_before' => $archived_status,
                        'payment_status_after'  => $charge['status'],
                    ];
                    StmEmailTemplateManager::uListing_send_email($args, 'payment-status-changed', true); // payment status changed

                }
			}
		}
	}
}