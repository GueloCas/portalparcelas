<?php

class UlistingJSONResponse extends UlistingResponse {

	/**
	 * Builds a json response.
	 *
	 * @param mixed
	 * @param integer
	 * @param array
	 */
    public function __construct( $data = null, $status = 200, $headers = array() )
    {
    	$headers[] = 'Content-Type: application/json';
    	$data = json_encode( $data );

    	return parent::__construct( $data, $status, $headers );
    }
}
