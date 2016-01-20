<?php

/**
 * Admin class that handle assets.
 */
final class Papi_Admin_Assets {

	/**
	 * The constructor.
	 */
	public function __construct() {
		add_action( 'admin_head', [$this, 'enqueue_css'] );
		add_action( 'admin_enqueue_scripts', [$this, 'enqueue_js'] );
		add_action( 'admin_enqueue_scripts', [$this, 'enqueue_locale'] );
	}

	/**
	 * Enqueue CSS.
	 */
	public function enqueue_css() {
		wp_enqueue_media();
		wp_enqueue_style( 'wp-color-picker' );
		wp_enqueue_style(
			'papi-main',
			$this->get_file_path( 'style.css' ),
			false,
			null
		);
	}

	/**
	 * Enqueue JavaScript.
	 */
	public function enqueue_js() {
		// WordPress will override window.papi on plugins page,
		// so don't include Papi JavaScript on plugins page.
		if ( strpos( $_SERVER['REQUEST_URI'], 'plugins.php' ) !== false ) {
			return;
		}

		wp_enqueue_script(
			'papi-main',
			$this->get_file_path( 'main.js' ),
			[
				'json2',
				'jquery',
				'jquery-ui-core',
				'jquery-ui-sortable',
				'wp-color-picker'
			],
			'',
			true
		);
	}

	/**
	 * Enqueue locale.
	 */
	public function enqueue_locale() {
		wp_localize_script( 'papi-main', 'papiL10n', [
			'remove'        => __( 'Remove', 'papi' ),
			'requiredError' => __( 'This fields are required:', 'papi' ),
		] );
	}

	/**
	 * Get file path for CSS or JavaScript file. If the
	 * non minified version of CSS or JavaScript exists
	 * it return that or default to the minified version.
	 *
	 * @param  string $file
	 *
	 * @return string
	 */
	protected function get_file_path( $file ) {
		$path = dirname( PAPI_PLUGIN_DIR ) . '/dist/';
		$url  = dirname( PAPI_PLUGIN_URL ) . '/dist/';
		$type = preg_match( '/\.css/', $file ) ? 'css' : 'js';
		$path = $path . $type . '/';
		$url  = $url . $type . '/';
		$min  = str_replace( $type, 'min.' . $type, $file );
		$file = PAPI_DEBUG && file_exists( $path . $file ) ? $file : $min;

		return $url . $file;
	}
}

if ( is_admin() ) {
	new Papi_Admin_Assets;
}
