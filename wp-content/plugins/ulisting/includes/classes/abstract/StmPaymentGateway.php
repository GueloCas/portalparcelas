<?php

namespace uListing\Classes;

use uListing\Classes\AbstractClasses\StmSettingsApi;

abstract class StmPaymentGateway extends StmSettingsApi{

	/**
	 * @var string
	 */
	public $title;

	/**
	 * @var string
	 */
	public $icon;

    /**
     * @var string
     */
    public $image;

	/**
	 * @var string
	 */
	public $enabled = 'yes';

	/**
	 * @var string
	 */
	public $description;

	/**
	 * @var string
	 */
	public $method_title = '';

	/**
	 * @var string
	 */
	public $method_description = '';

	/**
	 * @var array
	 */
	public $supports = array( 'pricing_plan' );

	public function init_settings() {
		parent::init_settings_data();
		$this->enabled  = ! empty( $this->settings_data['enabled'] ) && 'yes' === $this->settings_data['enabled'] ? 'yes' : 'no';
	}

	/**
	 * @return mixed|void
	 */
	public function get_title() {
		return apply_filters( 'ulisting_gateway_title', $this->title, $this );
	}

	/**
	 * @return mixed|void
	 */
	public function get_icon() {
		return apply_filters( 'ulisting_gateway_icon', $this->icon, $this );
	}

	/**
	 * @return mixed|void
	 */
	public function get_enabled() {
		return apply_filters( 'ulisting_gateway_enabled', $this->enabled, $this );
	}

	/**
	 * @return mixed|void
	 */
	public function get_method_title() {
		return apply_filters( 'ulisting_gateway_method_title', $this->method_title, $this );
	}

	/**
	 * @return mixed|void
	 */
	public function get_method_description() {
		return apply_filters( 'ulisting_gateway_method_description', $this->method_description, $this );
	}

	/**
	 * @return mixed|void
	 */
	public function get_supports() {
		return apply_filters( 'ulisting_gateway_supports', $this->supports, $this );
	}

	/**
	 * @param $feature
	 *
	 * @return mixed|void
	 */
	public function supports( $feature ) {
		return apply_filters( 'ulisting_payment_supports', in_array( $feature, $this->supports ), $feature, $this );
	}
}