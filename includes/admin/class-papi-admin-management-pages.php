<?php

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Papi Admin Management Pages.
 *
 * @package Papi
 * @version 1.0.0
 */

class Papi_Admin_Management_Pages {

	/**
	 * Thew view instance.
	 *
	 * @var Papi_Admin_View
	 */

	private $view;

	/**
	 * Constructor.
	 */

	public function __construct() {
		// Setup globals.
		$this->setup_globals();

		// Setup actions.
		$this->setup_actions();
	}

	/**
	 * Setup actions.
	 *
	 * @since 1.0.0
	 * @access private
	 */

	private function setup_actions() {
		add_action( 'admin_menu', array( $this, 'admin_menu' ) );
	}

	/**
	 * Setup globals.
	 *
	 * @since 1.0.0
	 * @access private
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
		add_management_page( $papi->name, $papi->name, 'manage_options', 'papi', array( $this, 'render_view' ) );
	}

	/**
	 * Render tools page view.
	 *
	 * @since 1.0.0
	 */

	public function render_view() {
		if ( isset( $_GET['view'] ) ) {
			$page_view = $_GET['view'];
		} else {
			$page_view = 'management-start';
		}

		if ( ! is_null( $page_view ) ) {
			$this->view->render( $page_view );
		} else {
			echo '<h2>Papi - 404</h2>';
		}
	}

}
