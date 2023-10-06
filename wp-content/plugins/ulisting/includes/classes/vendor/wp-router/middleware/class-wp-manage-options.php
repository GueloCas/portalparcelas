<?php

class UlistingManageOptions extends UlistingMiddleware {

	/**
	 * Can Current User Manage Options
	 *
	 * @param UlistingRequest $request
	 * @return bool|mixed
	 */
	public function handle(UlistingRequest $request)
	{
		if ( ! current_user_can( 'manage_options' ) ) {
			return false;
		}

		$this->next($request);
	}
}