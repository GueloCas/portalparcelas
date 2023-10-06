<?php
namespace uListing\Lib\PayPal\Classes;

use Psr\Log\InvalidArgumentException;
use PayPal\Api\VerifyWebhookSignature;
use uListing\Admin\Classes\StmEmailTemplateManager;
use uListing\Classes\Vendor\Validation;
use uListing\Lib\PayPal\Classes\PayPal;
use PayPal\Exception\PayPalConnectionException;
use uListing\Lib\PricingPlan\Classes\StmPayment;
use  uListing\Lib\PricingPlan\Classes\StmUserPlan;

class WebHook {

	/**
	 * @param $data
	 *
	 * @return array
	 * @throws \uListing\Classes\Vendor\Exception
	 */
	public static function verify_webhook($data) {
		$paypal_data = PayPal::getData();
		$apiContext  = PayPal::getApiContext();
		$headers     = getallheaders();
		$headers     = array_change_key_case($headers, CASE_UPPER);
		$result      = array(
			'success' => false,
			'status'  => null, // SUCCESS, FAILURE
			'message' => "",
		);

		$validator = new Validation();
		$data_for_validate = $validator->sanitize($headers);
		$validator->validation_rules(array(
			'PAYPAL-AUTH-ALGO'         => 'required',
			'PAYPAL-TRANSMISSION-ID'   => 'required',
			'PAYPAL-CERT-URL'          => 'required',
			'PAYPAL-TRANSMISSION-SIG'  => 'required',
			'PAYPAL-TRANSMISSION-TIME' => 'required',
		));

		$validated_data = $validator->run($data_for_validate);
		if($validated_data === false)
			return $result;

		$signatureVerification = new VerifyWebhookSignature();
		$signatureVerification->setAuthAlgo($headers['PAYPAL-AUTH-ALGO']);
		$signatureVerification->setTransmissionId($headers['PAYPAL-TRANSMISSION-ID']);
		$signatureVerification->setCertUrl($headers['PAYPAL-CERT-URL']);
		$signatureVerification->setWebhookId($paypal_data['webhook_id']);
		$signatureVerification->setTransmissionSig($headers['PAYPAL-TRANSMISSION-SIG']);
		$signatureVerification->setTransmissionTime($headers['PAYPAL-TRANSMISSION-TIME']);
		$signatureVerification->setRequestBody($data);

		try {
			$output = $signatureVerification->post($apiContext);
		} catch (Exception $ex) {
			ulisting_log(get_site_url()." #uListing_paypal invalid = " . $data);
			$result['message']   = $ex->getMessage();
			$result['errorData'] = $ex->getData();
			return $result;
		}catch (PayPalConnectionException $ex) {
			ulisting_log(get_site_url()." #uListing_paypal invalid = " . $data);
			$result['message']   = $ex->getMessage();
			$result['errorData'] = $ex->getData();
			return $result;
		} catch (InvalidArgumentException $ex) {
			ulisting_log(get_site_url()." #uListing_paypal invalid = " . $data);
			$result['message']   = $ex->getMessage();
			$result['errorData'] = $ex->getData();
			return $result;
		}

		$status = $output->getVerificationStatus();
		if($status == "SUCCESS") {
			$result['status'] = true;
			$result['success'] = true;
		}
		return $result;
	}

	/**
	 * @throws \uListing\Classes\Vendor\Exception
	 */
	public static function web_hook() {
		if( empty($data = file_get_contents('php://input')) )
			exit();

		// Verify webhook data
//		$verify = self::verify_webhook($data);
//		if(!$verify['success']){
//			http_response_code(400);
//			exit();
//		}
		ulisting_log("#uListing_paypal:".get_site_url() . " = " . $data);
		$data = json_decode($data, true);
		if(isset($data['resource']['billing_agreement_id']) AND !($user_plan = StmUserPlan::getUserPlanByAgreementId($data['resource']['billing_agreement_id'])) )
			exit();
		if(isset($data['resource_type']) AND $data['resource_type'] == "sale") {

			if( !($payment = StmPayment::query()->where("transaction", $data['resource']['id'])->findOne()) )
				$payment = new StmPayment();
			if($payment->status == 'completed')
				exit();
			$payment->user_plan_id   = $user_plan->id;
			$payment->payment_method = StmPayment::PAYMENT_METHOD_PAYPAL;
			$payment->status         = $data['resource']['state'];
			$payment->amount         = $data['resource']['amount']['total'];
			$payment->transaction    = $data['resource']['id'];
			$payment->save();

			if($payment->status == 'pending') {
				$payment->setMeta('data_pending', $data);
			}
			if($payment->status == 'completed') {
				$payment->setMeta('data_completed', $data);
				$user_plan->setActiveStatus();
			}

            $info = $payment->getMeta('user_info');
            if ( !empty( $info ) AND isset( $info->meta_value )) {
                $details = json_decode($info);
                $args = [
                    'payment_method' => 'PayPal',
                    'payment'        => $payment,
                    'user_name'      => isset($details->name) ? $details->name : '',
                    'user_email'     => isset($details->email) ? $details->email : '',
                    'plan_id'        => isset($payment->user_plan_id) ? $payment->user_plan_id : null,
                ];

                StmEmailTemplateManager::uListing_send_email($args, 'payment-received', true);
            }
		}
		http_response_code(200);
		exit();
	}

	public static function getWebHookUrl() {
		return get_site_url(null, 'payment/paypal/web-hook');
	}
}










