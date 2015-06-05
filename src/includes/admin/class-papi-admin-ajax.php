<?php

// Exit if accessed directly
defined( 'ABSPATH' ) || exit;

/**
 * Papi Admin Ajax.
 *
 * @package Papi
 */

class Papi_Admin_Ajax {

	/**
	 * The action prefix for Papi ajax actions.
	 *
	 * @var string
	 */

	private $action_prefix = 'papi/ajax/';

	/**
	 * The constructor.
	 */

	public function __construct() {
		$this->setup_actions();
	}

	/**
	 * Setup actions.
	 */

	private function setup_actions() {
		add_action( 'init', [$this, 'add_endpoint'] );
		add_action( 'parse_query', [$this, 'handle_papi_ajax'] );
		add_action( 'admin_enqueue_scripts', [$this, 'ajax_url'], 10 );

		add_action( $this->action_prefix . 'get_property', [$this, 'get_property'] );
		add_action( $this->action_prefix . 'get_properties', [$this, 'get_properties'] );
	}

	/**
	 * Add ajax endpoint.
	 */

	public function add_endpoint() {
		add_rewrite_tag( '%action%', '([^/]*)' );
		add_rewrite_rule( 'papi-ajax/([^/]*)/?', 'index.php?action=$matches[1]', 'top' );
	}

	/**
	 * Add ajax url to Papi JavaScript object.
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
	 */

	public function handle_papi_ajax() {
		global $wp_query;

		if ( ! is_object( $wp_query ) ) {
			return;
		}

		if ( defined( 'DOING_AJAX' ) && DOING_AJAX ) {
			return;
		}

		if ( ! empty( $_GET['action'] ) ) {
			$wp_query->set( 'papi_action', sanitize_text_field( $_GET['action'] ) );
		}

		$action = $wp_query->get( 'papi_action' );

		if ( is_user_logged_in() && has_action( $this->action_prefix . $action ) != false ) {
			if ( ! defined( 'DOING_AJAX' ) ) {
				define( 'DOING_AJAX', true );
			}

			header( 'Cache-Control: no-cache, must-revalidate' );
			header( 'Content-type: application/json' );

			do_action( $this->action_prefix . sanitize_text_field( $action ) );
			die;
		}
	}

	/**
	 * Get property html via GET.
	 */

	public function get_property() {
		$default_options = Papi_Property::default_options();
		$keys = array_keys( $default_options );
		$options = papi_get_qs( $keys, true );

		if ( $property = papi_property( $options ) ) {
			ob_start();

			papi_render_property( $property );

			$html = ob_get_clean();

			if ( empty( $html ) ) {
				$this->render_error( 'No property found' );
			} else {
				$this->render( [
					'html' => utf8_encode( $html )
				] );
			}
		} else {
			$this->render_error( 'No property found' );
		}
	}

	/**
	 * Get properties via POST.
	 */

	public function get_properties() {
		if ( ! isset( $_POST['properties'] ) ) {
			$this->render_error( 'No properties found' );
			exit;
		}

		$items = json_decode( stripslashes( $_POST['properties'] ), true );

		if ( empty( $items ) || ! is_array( $items ) ) {
			$this->render_error( 'No properties found' );
			exit;
		}

		foreach ( $items as $key => $item ) {
			$property = papi_property( (array) $item );

			if ( ! papi_is_property( $property ) ) {
				continue;
			}

			ob_start();

			papi_render_property( $property );

			$items[$key] = ob_get_clean();
		}

		$items = array_filter( $items );

		if ( empty( $items ) ) {
			$this->render_error( 'No properties found' );
		} else {
			$this->render( [
				'html' => $items
			] );
		}
	}

	/**
	 * Render json.
	 *
	 * @param mixed $obj
	 */

	public function render( $obj ) {
		echo json_encode( $obj );
	}

	/**
	 * Render error message.
	 *
	 * @param string $message
	 */

	public function render_error( $message ) {
		echo json_encode( [
			'error' => $message
		] );
	}
}

new Papi_Admin_Ajax;
