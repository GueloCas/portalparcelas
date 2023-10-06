<?php
namespace uListing\Classes;

class StmQuery {

	public $query_vars = array();

	public function __construct() {
		add_action( 'init', array( $this, 'add_endpoints' ) );
		if ( ! is_admin() ) {
			add_filter( 'query_vars', array( $this, 'stm_add_query_vars' ), 0 );
			add_action( 'parse_request', array( $this, 'stm_parse_request' ), 0 );
		}
		$this->stm_init_query_vars();
	}

	public function get_query_vars() {
		return apply_filters( 'stm_get_query_vars', $this->query_vars );
	}

	public function stm_init_query_vars() {
		$this->query_vars = apply_filters("ulisting_query_vars", []);
	}

	/**
	 * @param $endpoint
	 *
	 * @return mixed|void
	 */
	public function get_endpoint_title( $endpoint ) {
		global $wp;
		$title = "";
		$endpoint_title = apply_filters("ulisting_endpoint_title", []);
		if(isset($endpoint_title[$endpoint]))
			$title = $endpoint_title[$endpoint];
		return apply_filters( 'stm_endpoint_' . $endpoint . '_title', $title, $endpoint );
	}

	/**
	 * @return int
	 */
	public function get_endpoints_mask() {
		return EP_PAGES;
	}

	public function add_endpoints() {
		$mask = $this->get_endpoints_mask();
		foreach ($this->stm_get_query_vars() as $key => $var ) {
			if ( !empty($var) ) {
				add_rewrite_endpoint( $var, $mask );
			}
		}
	}

	/**
	 * @param array $vars Query vars.
	 * @return array
	 */
	public function stm_add_query_vars( $vars ) {
		foreach ( $this->stm_get_query_vars() as $key => $var ) {
			$vars[] = $key;
		}
		return $vars;
	}

	/**
	 * @return array
	 */
	public function stm_get_query_vars() {
		return apply_filters( 'stm_get_query_vars', $this->query_vars );
	}

	/**
	 * @return string
	 */
	public function get_current_endpoint() {
		global $wp;
		foreach ( $this->stm_get_query_vars() as $key => $value ) {
			if (isset( $wp->query_vars[$key]))
				return $key;
		}
		return '';
	}

	public function stm_parse_request() {
		global $wp;
		foreach ( $this->stm_get_query_vars() as $key => $var ) {
			if ( isset( $_GET[ $var ] ) ) {
				$wp->query_vars[ $key ] = sanitize_text_field( wp_unslash( $_GET[ $var ] ) );
			} elseif ( isset( $wp->query_vars[ $var ] ) ) {
				$wp->query_vars[ $key ] = $wp->query_vars[ $var ];
			}
		}
	}
}