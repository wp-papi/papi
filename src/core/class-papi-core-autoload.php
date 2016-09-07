<?php

/**
 * Autoload class that autoload classes.
 */
final class Papi_Core_Autoload {

	/**
	 * The Constructor.
	 *
	 * @codeCoverageIgnore
	 */
	public function __construct() {
		spl_autoload_register( [$this, 'autoload'] );
	}

	/**
	 * Autoload Papi classes.
	 *
	 * @codeCoverageIgnore
	 *
	 * @param string $class
	 */
	public function autoload( $class ) {
		$class = strtolower( $class );
		$file  = 'class-' . str_replace( '_', '-', strtolower( $class ) ) . '.php';
		$path  = PAPI_PLUGIN_DIR;

		if ( strpos( $class, 'papi_admin' ) === 0 ) {
			$path .= 'admin/';
		} else if ( strpos( $class, 'papi_core_' ) === 0 ) {
			$path .= 'core/';
		} else if ( strpos( $class, 'papi_cli_' ) === 0 ) {
			$path .= 'cli/';
		} else if ( preg_match( '/^papi\_\w+\_store$/', $class ) ) {
			$path .= 'stores/';
		} else if ( strpos( $class, 'papi_porter' ) === 0 ) {
			$path .= 'porter/';
		} else if ( strpos( $class, 'papi_property' ) === 0 ) {
			$path .= 'properties/';
		} else if ( strpos( $class, 'papi_query' ) === 0 ) {
			$path .= 'query/';
		} else if ( preg_match( '/^papi\_\w+\_type/', $class ) ) {
			$path .= 'types/';
		}

		if ( is_readable( $path . $file ) ) {
			require_once $path . $file;
		}
	}
}

new Papi_Core_Autoload;
