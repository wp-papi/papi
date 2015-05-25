<?php

/**
 * Papi core autoload class.
 *
 * @package Papi
 */

class Papi_Core_Autoload {

	/**
	 * The Constructor.
	 */

	public function __construct() {
		spl_autoload_register( array( $this, 'autoload' ) );
	}

	/**
	 * Autoload Papi classes.
	 *
	 * @param string $class
	 */

	public function autoload( $class ) {
		$class = strtolower( $class );
		$file  = 'class-' . str_replace( '_', '-', strtolower( $class ) ) . '.php';
		$path  = PAPI_PLUGIN_DIR . 'includes/';

		if ( strpos( $class, 'papi_admin' ) === 0 ) {
			$path .= 'admin/';
		} else if ( strpos( $class, 'papi_core_' ) === 0 ) {
			$path .= 'core/';
		} else if ( strpos( $class, 'papi_property' ) === 0 ) {
			$path .= 'properties/';
		} else if ( strpos( $class, 'papi_page_type' ) === 0 ) {
			$path .= 'page-type/';
		} else if ( strpos( $class, 'papi_option_type' ) === 0 ) {
			$path .= 'option-type/';
		} else if ( strpos( $class, 'papi_' ) === 0 && preg_match( '/\_page$/', $class ) ) {
			$path .= 'pages/';
		} else {
		}

		if ( is_readable( $path . $file ) ) {
			require_once $path . $file;
		}
	}

}

new Papi_Core_Autoload();
