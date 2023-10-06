<?php
namespace uListing\Lib\PayPal\Classes;

use PayPal\Api\Plan;
use PayPal\Api\Payer;
use PayPal\Api\Agreement;
use PayPal\Exception\PayPalConnectionException;
use PayPal\Api\AgreementStateDescriptor;

class StmPayPalAgreement {

	const PAYMENT_METHOD_PAYPAL = 'paypal';

	public $apiContext;

	public function __construct() {
		$this->apiContext = self::getApiContext();
	}

	public static function getApiContext() {
		return \uListing\Lib\PayPal\Classes\PayPal::getApiContext();
	}

	/**
	 * @param $id_plan
	 * @param array $params
	 * @param string $payment_method
	 *
	 * @return array
	 */
	public static function createAgreement($id_plan, $params = array(), $payment_method = self::PAYMENT_METHOD_PAYPAL) {
		$date_time = new \DateTime();
		$date_time->add(new \DateInterval('PT24H'));

		$apiContext = self::getApiContext();
		$result = array(
			'success'     => true,
			'approvalUrl' => null,
			'agreement'   => null,
			'message'     => "Success",
		);

		$agreement = new Agreement();
		$agreement->setName($params['name'])
		          ->setDescription($params['description'])
		          ->setStartDate($date_time->format(\DateTime::ISO8601));

		$plan = new Plan();
		$plan->setId($id_plan);
		$agreement->setPlan($plan);

		$payer = new Payer();
		$payer->setPaymentMethod($payment_method);
		$agreement->setPayer($payer);

		try {
			$agreement = $agreement->create($apiContext);
			$approvalUrl = $agreement->getApprovalLink();
			$result['agreement'] = $agreement;
			$result['approvalUrl'] = $approvalUrl;
		} catch (Exception $ex) {
			$result['success'] = false;
			$result['message'] = $ex->getMessage();
		} catch (PayPalConnectionException $ex) {
			$result['success'] = false;
			$result['message'] = $ex->getMessage();
			return $result;
		}
		return $result;
	}

	/**
	 * @param $token
	 *
	 * @return array
	 */
	public static function executeAgreement($token) {

		$apiContext = self::getApiContext();
		$agreement  = new \PayPal\Api\Agreement();
		$result     = array(
				'success'   => true,
				'agreement' => null,
				'message'   => "Success",
		);

		try {
			$agreement->execute($token, $apiContext);
		} catch (Exception $ex) {
			$result['success'] = false;
			$result['message'] = $ex->getMessage();
			return $result;
		} catch (PayPalConnectionException $ex) {
			$result['success'] = false;
			$result['message'] = $ex->getMessage();
			return $result;
		}

		try {
			$agreement = \PayPal\Api\Agreement::get($agreement->getId(), $apiContext);
			$result['agreement'] = $agreement;
		} catch (Exception $ex) {
			$result['success'] = false;
			$result['message'] = $ex->getMessage();
			return $result;
		}catch (PayPalConnectionException $ex) {
			$result['success'] = false;
			$result['message'] = $ex->getMessage();
			return $result;
		}

		return $result;
	}

	/**
	 * @param $id
	 *
	 * @return array
	 */
	public static function getAgreement($id) {

		$apiContext = self::getApiContext();
		$result     = array(
			'success'   => true,
			'agreement' => null,
			'message'   => "Success",
		);

		try {
			$result['agreement'] = Agreement::get($id, $apiContext);
		} catch (Exception $ex) {
			$result['success'] = false;
			$result['message'] = $ex->getMessage();
			return $result;
		}catch (PayPalConnectionException $ex) {
			$result['success'] = false;
			$result['message'] = $ex->getMessage();
			$result['error_data'] = $ex->getData();
			return $result;
		}
		return $result;
	}

	/**
	 * @param $id
	 *
	 * @return array
	 */
	public static function cancel($agreement_id) {
		$apiContext = self::getApiContext();
		$result     = array(
			'success'   => true,
			'agreement' => null,
			'message'   => "Success",
		);
		try {

			$agreementStateDescriptor = new AgreementStateDescriptor();
			$agreementStateDescriptor->setNote('cancel');
			$agreement = Agreement::get($agreement_id, $apiContext);
			$agreement->cancel($agreementStateDescriptor, $apiContext);
			$result['agreement'] = $agreement;

		} catch (Exception $ex) {
			$result['success'] = false;
			$result['message'] = $ex->getMessage();
			return $result;
		}catch (PayPalConnectionException $ex) {
			$result['success'] = false;
			$result['message'] = $ex->getMessage();
			$result['error_data'] = $ex->getData();
			return $result;
		}
		return $result;
	}


}