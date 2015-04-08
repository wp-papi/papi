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
		add_action( 'admin_enqueue_scripts', array( $this, 'ajax_url' ), 10 );

		add_action( 'papi_ajax_get_property', array( $this, 'get_property' ) );
		add_action( 'papi_ajax_get_properties', array( $this, 'get_properties' ) );
	}

	/**
	 * Add ajax endpoint.
	 *
	 * @since 1.3.0
	 */

	public function add_endpoint() {
		add_rewrite_tag( '%papi-ajax%', '([^/]*)' );
		add_rewrite_rule( 'papi-ajax/([^/]*)/?', 'index.php?action=$matches[1]', 'top' );
	}

	/**
	 * Add ajax url to Papi JavaScript object.
	 *
	 * @since 1.3.0
	 */

	public function ajax_url() {
		?>
		<script type="text/javascript">
			var papi = papi || {};
			papi.ajaxUrl = '/papi-ajax/';
		</script>
		<?php
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

		if ( ! empty( $_GET['papi-ajax'] ) ) {
			$wp_query->set( 'papi-ajax', sanitize_text_field( $_GET['papi-ajax'] ) );
		}

		if ( $action = $wp_query->get( 'papi-ajax' ) ) {
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
	 * Get property html via GET.
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
	 * Get properties via POST.
	 *
	 * @since 1.3.0
	 */

	public function get_properties() {
		$json   = file_get_contents('php://input');
		$items  = json_decode($json);

		foreach ($items as $key => $item) {
			$property = papi_property( $item );

			ob_start();

			papi_render_property( $property );

			$items[$key] = ob_get_clean();
		}

		if ( empty( $items ) ) {
			$this->render_error( 'No properties found' );
		} else {
			$this->render( $items );
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
