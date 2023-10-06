<?php

abstract class UlistingMiddleware {

	/**
	 * @var UlistingRouter
	 */
	protected $router;

	/**
	 * @var array
	 */
	protected $store;

	/**
	 * Called by UlistingRouter to run Middleware.
	 *
	 * @param  UlistingRequest
	 * @param  UlistingRouter
	 * @param  array
	 * @return mixed
	 */
	public function run( UlistingRequest $request, UlistingRouter $router, $store )
	{
		$this->router = $router;
		$this->store  = $store;

		return $this->handle( $request );
	}

	/**
	 * Calls the next Middleware.
	 *
	 * @param  UlistingRequest
	 * @return void
	 */
	public function next( UlistingRequest $request )
	{
		$this->router->next( $request, $this->router, $this->store );
	}

	/**
	 * Method to be implemented by each Middleware.
	 *
	 * @param  UlistingRequest
	 * @return mixed
	 */
	abstract function handle( UlistingRequest $request );
}
