<?php
namespace uListing\Classes;

class StmVerifyNonce {

	public static function verifyNonce($wpnonce, $action, $message = null) {
		if ( ! wp_verify_nonce( $wpnonce, $action) ) {
			if ($message)
				die($message);
			die( 'invalid nonce' );
		}
	}

	public static function createAjaxNonce() {
		return wp_create_nonce( 'ulisting-ajax-nonce' );
	}

	public static function verifyAjaxNonce() {
		if ( isset( $_REQUEST['nonce'] ) ) {
			$nonce          = sanitize_text_field($_REQUEST['nonce']);
		} else {
			$request_body   = file_get_contents('php://input');
			$request_data   = json_decode($request_body, true);
			$nonce          = ( isset( $request_data['nonce'] ) ) ? sanitize_text_field($request_data['nonce']) : '';
		}

		return wp_verify_nonce( $nonce, 'ulisting-ajax-nonce' );
	}
}