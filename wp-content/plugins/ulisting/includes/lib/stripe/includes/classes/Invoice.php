<?php
namespace uListing\Lib\Stripe\Classes;

use Stripe\Error\InvalidRequest;
use uListing\Lib\Stripe\Classes\Stripe;

class Invoice {

	private $srtipe;

	public function __construct() {
		$this->srtipe = new Stripe();
	}

	/**
	 * @param $invoice_id id in stripe billing system
	 *
	 * @return array
	 */
	public function get_invice($invoice_id) {

		$result = array(
			'success' => false,
			'invoice' => null,
			'message' => "",
		);

		try{
			$result['success'] = true;
			$result['invoice'] = \Stripe\Invoice::retrieve($invoice_id);
		}catch (InvalidRequest $e){
			$result['message'] = $e->getMessage();
		}catch (Authentication $e){
			$result['message'] = $e->getMessage();
		}

		return $result;

	}

}

