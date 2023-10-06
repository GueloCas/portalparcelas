<?php
namespace uListing\Classes;

use uListing\Lib\PayPal\Classes\PayPal;

class StmPaymentMethod {

	const ENABLED   = "yes";
	const DISABLED  = "no";

	const SUPPORT_SUBSCRIPTION = "subscription";
	const SUPPORT_ONE_TIME_PAYMENT = "one_time_payment";

	public  $payment_methods = [];

	public function __construct() {
		$this->payment_methods = self::get_payment_method_list();
	}

	/**
	 * @param $payment_method
	 *
	 * @return null
	 */
	public static function get_payment_method($payment_method) {
		$payment_methods = self::get_payment_method_list();
		if(isset($payment_methods[$payment_method]))
			return $payment_methods[$payment_method];
		return null;
	}

	/**
	 * @return mixed|void
	 */
	public static function get_payment_method_list() {
		$payment_methods = [];
		return apply_filters("ulisting_get_payment_methods", $payment_methods);
	}

	/**
	 * @return mixed|void
	 */
	public static function get_active_payment_method_list($support = null) {
		$payment_methods = [];
		foreach (self::get_payment_method_list() as $payment_method){

			if($payment_method->enabled != self::ENABLED)
				continue;

			if($support != null AND !in_array($support, $payment_method->supports))
				continue;

			$payment_methods[] = $payment_method;

		}
		return $payment_methods;
	}

	/**
	 * @param $payment_method
	 *
	 * @return bool
	 */
	public static function check_active($payment_method) {
		$payment_methods = self::get_payment_method_list();
		if(isset($payment_methods[$payment_method]) AND $payment_method = $payment_methods[$payment_method] AND $payment_method->enabled == "on") {
			return true;
		}
		return false;
	}

	/**
	 * Ajax settings payment method
	 */
	public static function settings_payment_method() {

		$payment_method = new StmPaymentMethod();

		$result = array(
			'message' => esc_html__('Something went wrong.', "ulisting"),
			'status'  => 'error',
            'success' => true,
		);

		if (current_user_can('manage_options') && isset($_POST['type']) && isset($_POST['nonce'])) {

            StmVerifyNonce::verifyNonce(sanitize_text_field($_POST['nonce']), 'ulisting-ajax-nonce');

            if ( sanitize_text_field($_POST['type']) == 'install') {
                if ( $payment_method->install_payment_method(sanitize_text_field($_POST['payment_method'])) ) {
                    $result['status']   = 'success';
                    $result['payments'] = StmListingSettings::tab_payments();
                    $result['message']  = esc_html__('Installing payment completed.', "ulisting");
                }
                wp_send_json($result);
                die;
            }

            if ( sanitize_text_field($_POST['type']) == 'uninstall' ) {
                if ( $payment_method->uninstall_payment_method(sanitize_text_field($_POST['payment_method'])) ) {
                    $result['status'  ] = 'success';
                    $result['payments'] = StmListingSettings::tab_payments();
                    $result['message' ] = esc_html__('Uninstalling payment completed.', "ulisting");
                }
                wp_send_json($result);
                die;
            }
        }

		wp_send_json($result);
		die;
	}

	/**
	 * @param $payment_method
	 *
	 * @return bool
	 */
	public function install_payment_method($payment_method) {

		if(isset($this->payment_methods[$payment_method]) AND $payment = $this->payment_methods[$payment_method] ) {
			$payment->install();
			$payment->update_option("enabled", "yes");
			return true;
		}
		return false;
	}

	/**
	 * @param $payment_method
	 *
	 * @return bool
	 */
	public function uninstall_payment_method($payment_method) {
		if(isset($this->payment_methods[$payment_method]) AND $payment = $this->payment_methods[$payment_method] ) {
			$payment->uninstall();
			$payment->update_option("enabled", "no");
			return true;
		}
		return false;
	}
}