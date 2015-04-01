<?php

// Exit if accessed directly
defined( 'ABSPATH' ) || exit;

/**
 * Papi Admin Ajax.
 *
 * @package Papi
 * @since 1.0.0
 */

class Papi_Admin_Ajax {

	/**
	 * Constructor.
	 *
	 * @since 1.3.0
	 */

	public function __construct() {
		$this->setup_actions();
	}

	/**
	 * Setup actions.
	 *
	 * @since 1.3.0
	 */

	private function setup_actions() {
		add_action( 'init', array( $this, 'add_endpoint' ) );
		add_action( 'parse_query', array( $this, 'handle_papi_ajax' ) );
		add_action( 'papi_ajax_get_property', array( $this, 'get_property' ) );
	}

	/**
	 * Add ajax endpoint.
	 *
	 * @since 1.3.0
	 */

	public function add_endpoint() {
		add_rewrite_rule( 'papi-ajax/([^/]*)/?', 'index.php?action=$matches[1]', 'top' );
	}

	/**
	 * Handle Papi ajax.
	 *
	 * @since 1.3.0
	 */

	public function handle_papi_ajax() {
		global $wp_query;

		if ( ! is_object( $wp_query ) ) {
			return;
		}

		if ( ! empty( $_GET['action'] ) ) {
			$wp_query->set( 'action', sanitize_text_field( $_GET['action'] ) );
		}

		if ( $action = $wp_query->get( 'action' ) ) {
			if ( ! defined( 'DOING_AJAX' ) ) {
				define( 'DOING_AJAX', true );
			}

			header('Cache-Control: no-cache, must-revalidate');
			header('Content-type: application/json');

			do_action( 'papi_ajax_' . sanitize_text_field( $action ) );
			die;
		}
	}

	/**
	 * Get property html via ajax.
	 *
	 * @since 1.3.0
	 */

	public function get_property() {
		$options = papi_get_qs( array( 'type', 'slug' ), true );

		$property = papi_property( $options );

		ob_start();

		papi_render_property( $property );

		$html = ob_get_clean();

		echo json_encode( array(
			'html' => utf8_encode( $html )
		) );
	}
}

new Papi_Admin_Ajax();
