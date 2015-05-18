<?php

// Exit if accessed directly
defined( 'ABSPATH' ) || exit;

/**
 * Papi Admin Management Pages.
 *
 * @package Papi
 * @since 1.0.0
 */

class Papi_Admin_Management_Pages {

	/**
	 * Thew view instance.
	 *
	 * @var Papi_Admin_View
	 * @since 1.0.0
	 */

	private $view;

	/**
	 * Constructor.
	 *
	 * @since 1.0.0
	 */

	public function __construct() {
		$this->setup_globals();
		$this->setup_actions();
	}

	/**
	 * Setup actions.
	 *
	 * @since 1.0.0
	 */

	private function setup_actions() {
		add_action( 'admin_menu', [$this, 'admin_menu'] );
	}

	/**
	 * Setup globals.
	 *
	 * @since 1.0.0
	 */

	private function setup_globals() {
		$this->view = new Papi_Admin_View;
	}

	/**
	 * Register management page.
	 *
	 * @since 1.0.0
	 */

	public function admin_menu() {
		$papi = papi();
		add_management_page( $papi->name, $papi->name, 'manage_options', 'papi', [$this, 'render_view'] );
	}

	/**
	 * Render tools page view.
	 *
	 * @since 1.0.0
	 */

	public function render_view() {
		$page_view = (string) papi_get_qs( 'view' );

		if ( empty( $page_view ) ) {
			$page_view = 'management-start';
		}

		if ( ! is_null( $page_view ) ) {
			$this->view->render( $page_view );
		} else {
			echo '<h2>Papi - 404</h2>';
		}
	}

}

new Papi_Admin_Management_Pages;
