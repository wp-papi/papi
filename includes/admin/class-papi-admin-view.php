<?php

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Papi Admin View.
 *
 * @package Papi
 * @version 1.0.0
 */

class Papi_Admin_View {

	/**
	 * Path to view dir.
	 */

	private $path = '';

	/**
	 * Constructor.
	 *
	 * @param string $path
	 *
	 * @since 1.0
	 */

	public function __construct( $path = '' ) {
		$this->path = empty( $path ) ? PAPI_PLUGIN_DIR . 'includes/admin/views/' : $path;
	}

	/**
	 * Check if file exists.
	 *
	 * @param string $file
	 *
	 * @since 1.0
	 *
	 * @return bool
	 */

	public function exists( $file ) {
		return file_exists( $this->file( $file ) );
	}

	/**
	 * Render file.
	 *
	 * @param string $file
	 *
	 * @since 1.0
	 *
	 * @return string|null
	 */

	public function render( $file ) {
		if ( ! empty( $file ) && $this->exists( $file ) ) {
			require_once( $this->file( $file ) );
		}

		return null;
	}

	/**
	 * Get full path to file with php exstention.
	 *
	 * @param string $file
	 *
	 * @since 1.0
	 * @access private
	 *
	 * @return string
	 */

	private function file( $file ) {
		return $this->path . $file . '.php';
	}

}
