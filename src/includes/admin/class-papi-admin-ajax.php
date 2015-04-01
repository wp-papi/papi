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

			header( 'Cache-Control: no-cache, must-revalidate' );
			header( 'Content-type: application/json' );

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
        $keys = array_keys( papi_get_property_default_options() );
		$options = papi_get_qs( $keys, true );

		$property = papi_property( $options );

		ob_start();

		papi_render_property( $property );

		$html = ob_get_clean();

        if ( empty( $html ) ) {
            $this->render_error( 'No property found' );
        } else {
            $this->render( array(
                'html' => utf8_encode( $html )
            ) );
        }
	}

    /**
     * Render json.
     *
     * @param mixed $obj
     * @since 1.3.0
     */

    public function render( $obj ) {
        echo json_encode( $obj );
    }

    /**
     * Render error message.
     *
     * @param string $message
     * @since 1.3.0
     */

    public function render_error( $message ) {
        echo json_encode( array(
            'error' => $message
        ) );
    }
}

new Papi_Admin_Ajax();
