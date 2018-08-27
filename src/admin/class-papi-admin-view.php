<?php

/**
 * Admin class that handle admin view rendering.
 */
final class Papi_Admin_View {

	/**
	 * Path to view dir.
	 *
	 * @var string
	 */
	protected $path = '';

	/**
	 * The constructor.
	 *
	 * @param string $path
	 */
	public function __construct( $path = '' ) {
		$this->path = empty( $path ) ? PAPI_PLUGIN_DIR . '/admin/views/' : $path;
	}

	/**
	 * Check if file exists.
	 *
	 * @param  string $file
	 *
	 * @return bool
	 */
	public function exists( $file ) {
		return file_exists( $this->file( $file ) );
	}

	/**
	 * Render file.
	 *
	 * @param  string $file
	 *
	 * @return string
	 */
	public function render( $file ) {
		if ( ! empty( $file ) && $this->exists( $file ) ) {
			require $this->file( $file );
		}
	}

	/**
	 * Get full path to file with php exstention.
	 *
	 * @param  string $file
	 *
	 * @return string
	 */
	protected function file( $file ) {
		return $this->path . $file . '.php';
	}
}
