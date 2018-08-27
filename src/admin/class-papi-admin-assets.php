<?php

/**
 * Admin class that handle assets.
 */
final class Papi_Admin_Assets {

	/**
	 * The constructor.
	 */
	public function __construct() {
		add_action( 'admin_enqueue_scripts', [$this, 'enqueue_css'] );
		add_action( 'admin_enqueue_scripts', [$this, 'enqueue_js'] );
		add_action( 'admin_enqueue_scripts', [$this, 'enqueue_locale'] );
	}

	/**
	 * Enqueue CSS.
	 */
	public function enqueue_css() {
		wp_enqueue_media();
		wp_enqueue_style( 'wp-color-picker' );
		wp_enqueue_style( 'papi-main', dirname( PAPI_PLUGIN_URL ) . '/dist/css/style.min.css', false, null );
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

		wp_enqueue_script( 'papi-main', dirname( PAPI_PLUGIN_URL ) . '/dist/js/main.min.js', [
			'json2',
			'jquery',
			'jquery-ui-core',
			'jquery-ui-sortable',
			'jquery-masonry',
			'wp-color-picker'
		], '', true );
	}

	/**
	 * Enqueue locale.
	 */
	public function enqueue_locale() {
		wp_localize_script( 'papi-main', 'papiL10n', [
			'close'         => __( 'Close' ),
			'edit'          => __( 'Edit' ),
			'remove'        => __( 'Remove' ),
			'requiredError' => __( 'This fields are required:', 'papi' ),
		] );
	}
}

if ( papi_is_admin() ) {
	new Papi_Admin_Assets;
}
