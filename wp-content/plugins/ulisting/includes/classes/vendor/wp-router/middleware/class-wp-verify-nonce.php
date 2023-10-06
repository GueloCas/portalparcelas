<?php

class UlistingVerifyNonce extends UlistingMiddleware {

	/**
	 * Can Current User Manage Options
	 *
	 * @param UlistingRequest $request
	 * @return bool|mixed
	 */
	public function handle(UlistingRequest $request)
	{
		if ( isset( $_REQUEST['nonce'] ) ) {
			$nonce          = sanitize_text_field($_REQUEST['nonce']);
		} else {
			$request_body   = file_get_contents('php://input');
			$request_data   = json_decode($request_body, true);
			$nonce          = ( isset( $request_data['nonce'] ) ) ? sanitize_text_field($request_data['nonce']) : '';
		}

		if ( ! wp_verify_nonce( $nonce, 'ulisting-ajax-nonce' ) ) {
			return false;
		}

		$this->next($request);
	}
}