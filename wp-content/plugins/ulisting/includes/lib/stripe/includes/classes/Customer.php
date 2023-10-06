<?php

namespace uListing\Lib\Stripe\Classes;

use uListing\Classes\StmUser;
use Stripe\Error\InvalidRequest;
use uListing\Lib\Stripe\Classes\Stripe;

class Customer {

	private $srtipe;
	private $user;

	public function __construct($user_id) {
		$this->srtipe = new Stripe();
		$this->user   = $this->get_user($user_id);
	}

	/**
	 * @param $user_id
	 *
	 * @return array|StmUser
	 */
	public function get_user($user_id) {
		$user = new StmUser($user_id);

		if($user->ID)
			return $user;

		return array(
			'success' => false,
			'message' => __("User not found", "ulisting"),
		);
	}

	/**
	 * @return customer_id string
	 */
	public function get_customer_id() {

		if( !($customer_id = get_user_meta($this->user->ID, 'stripe_customer')) )
			return array(
				'success' => false,
				'message' => __("Customer not found", "ulisting"),
			);

		return $customer_id[0];
	}

	/**
	 * @return array
	 */
	public function get_customer() {
		$result = array(
			'success' => true,
			'customer' => null,
			'message' => "Success",
		);

		if (! $this->user instanceof StmUser)
			return $this->user;

		$customer_id = $this->get_customer_id();
		if(is_array($customer_id))
			return $customer_id;

		try{
			$result['customer'] = \Stripe\Customer::retrieve($customer_id);
		}catch (InvalidRequest $e){
			$result['success'] = false;
			$result['message'] = $e->getMessage();
		}

		return $result;
	}

	/**
	 * Stripe create customer
	 *
	 * @param $user_id
	 * @param $token obtained with Stripe.js
	 *
	 * @return array
	 */
	public function create($token){
		$result = array(
			'success' => true,
			'customer'    => null,
			'message' => "Success",
		);

		if (! $this->user instanceof StmUser)
			return $this->user;

		try{
			$result['customer'] = \Stripe\Customer::create([
				"email" => $this->user->data->user_email,
				"description" => "Customer for ".$this->user->data->user_email,
				"source" => $token
			]);
			// save user meta stripe customer id
			if(isset($result['customer']->id))
				update_user_meta($this->user->ID, 'stripe_customer', $result['customer']->id);

		}catch (InvalidRequest $e){
			$result['success'] = false;
			$result['message'] = $e->getMessage();
		}

		return $result;
	}

	/**
	 *  Delete customer in Stripe
	 *
	 * @param $user_id
	 *
	 * @return array
	 */
	public function delete(){
		$result = array(
			'success' => true,
			'message' => "Success",
		);

		if (! $this->user instanceof StmUser)
			return $this->user;

		$customer_id = $this->get_customer_id();
		if(is_array($customer_id))
			return $customer_id;

		try{
			$customer = \Stripe\Customer::retrieve($customer_id);
			$customer->delete();
			delete_user_meta($this->user->ID, 'stripe_customer');
		}catch (InvalidRequest $e){
			$result['success'] = false;
			$result['message'] = $e->getMessage();
		}

		return $result;
	}

	/**
	 * Add card for customer
	 *
	 * @param $user_id
	 * @param $token obtained with Stripe.js
	 *
	 * @return $result
	 */
	public function add_card($token){

		$result = array(
			'success' => true,
			'customer'    => null,
			'message' => "Success",
		);

		if (! $this->user instanceof StmUser)
			return $this->user;

		$customer_id = $this->get_customer_id();
		if(is_array($customer_id))
			return $customer_id;

		try{

			$customer = \Stripe\Customer::retrieve($customer_id);
			$customer->sources->create(["source" => $token]);
			$result['customer'] = $customer;

		}catch (InvalidRequest $e){
			$result['success'] = false;
			$result['message'] = $e->getMessage();
		}

		return $result;
	}

	/**
	 * Get all card customer
	 *
	 * @param $user_id
	 *
	 * @return array
	 */
	public function get_card(){

		$result = array(
			'success'      => true,
			'default_card' => null,
			'cards'        => null,
			'message'      => "Success",
		);

		if (! $this->user instanceof StmUser)
			return $this->user;

		$customer_id = $this->get_customer_id();
		if(is_array($customer_id))
			return $customer_id;

		try{
			$customer = \Stripe\Customer::retrieve($customer_id);
			if($customer->id AND $customer->deleted != 1) {
				$result['default_card'] = $customer->default_source;
				if (isset($customer->sources))
    				$result['cards'] = $customer->sources->all(['limit'=> 100, 'object' => 'card']);
			}
		}catch (InvalidRequest $e){
			$result['success'] = false;
			$result['message'] = $e->getMessage();
		}
		return $result;
	}

	/**
	 *  Customer set default card
	 *
	 * @param $card_id card id in stripe system
	 *
	 * @return array
	 */
	public function set_default_card($card_id){

		$result = array(
			'success'  => true,
			'customer' => null,
			'message'  => "Success",
		);

		if (! $this->user instanceof StmUser)
			return $this->user;

		$customer_id = $this->get_customer_id();
		if(is_array($customer_id))
			return $customer_id;

		try{
			$customer =  \Stripe\Customer::retrieve($customer_id);
			$customer->default_source = $card_id;
			$customer->save();
			$result['customer'] = $customer;
		}catch (InvalidRequest $e){
			$result['success'] = false;
			$result['message'] = $e->getMessage();
		}
		return $result;
	}

	/**
	 * Delete card in stripe system
	 *
	 * @param $card_id
	 *
	 * @return array|StmUser|customer_id
	 */
	public function delete_card($card_id){

		$result = array(
			'success' => true,
			'response'    => null,
			'message' => "Success",
		);

		if (! $this->user instanceof StmUser)
			return $this->user;

		$customer_id = $this->get_customer_id();
		if(is_array($customer_id))
			return $customer_id;

		try{

			$customer = \Stripe\Customer::retrieve($customer_id);
			$result['response'] = $customer->sources->retrieve($card_id)->delete();

		}catch (InvalidRequest $e){
			$result['success'] = false;
			$result['message'] = $e->getMessage();
		}

		return $result;
	}

}