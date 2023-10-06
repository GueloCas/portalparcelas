<?php
namespace uListing\Lib\Stripe\Classes;

use Stripe\Error\InvalidRequest;
use Stripe\Error\Authentication;
use uListing\Lib\Stripe\Classes\Stripe;

class Balance {

	private $srtipe;

	public function __construct() {
		$this->srtipe = new Stripe();
	}

	/**
	 * @return array
	 */
	public function get_balance(){
		$result = array(
			'success' => true,
			'balance' => null,
			'message' => "Success",
		);
		try{
			$result['balance'] = \Stripe\Balance::retrieve();
		}catch (InvalidRequest $e){
			$result['success'] = false;
			$result['message'] = $e->getMessage();
		}catch (Authentication $e){
			$result['success'] = false;
			$result['message'] = $e->getMessage();
		}
		return $result;
	}
}

