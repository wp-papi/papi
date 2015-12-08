<?php

// Load Papi Container.
require_once __DIR__ . '/container/class-papi-container.php';

/**
 * Papi loader class that handle the loading
 * of the plugin.
 */
final class Papi_Loader extends Papi_Container {

	/**
	 * The instance of Papi loader class.
	 *
	 * @var Papi_Loader
	 */
	private static $instance;

	/**
	 * The plugin name.
	 *
	 * @var string
	 */
	public $name = 'Papi';

	/**
	 * Papi loader instance.
	 *
	 * @return Papi_Loader
	 */
	public static function instance() {
		if ( ! isset( self::$instance ) ) {
			self::$instance = new self;
		}

		return self::$instance;
	}

	/**
	 * Call function in the container.
	 *
	 * @param  string $name
	 * @param  array  $arguments
	 *
	 * @return mixed
	 */
	public function __call( $name, $arguments ) {
		return $this->make( $name, $arguments );
	}

	/**
	 * The constructor.
	 */
	private function __construct() {
		$this->constants();
		$this->load_textdomain();
		$this->require_files();
		$this->setup_container();
		papi_action_include();
	}

	/**
	 * Cloning is forbidden.
	 *
	 * @codeCoverageIgnore
	 */
	public function __clone() {
		_doing_it_wrong( __FUNCTION__, __( 'Cheatin&#8217; huh?', 'papi' ), '2.4.10' );
	}

	/**
	 * Unserializing instances of this class is forbidden.
	 *
	 * @codeCoverageIgnore
	 */
	public function __wakeup() {
		_doing_it_wrong( __FUNCTION__, __( 'Cheatin&#8217; huh?', 'papi' ), '2.4.10' );
	}

	/**
	 * Bootstrap constants
	 */
	private function constants() {
		// Path to Papi plugin directory
		if ( ! defined( 'PAPI_PLUGIN_DIR' ) ) {
			$mu_dir = trailingslashit( sprintf( '%s/%s/src',
				WPMU_PLUGIN_DIR,
				basename( dirname( __DIR__ ) )
			) );

			if ( is_dir( $mu_dir ) ) {
				define( 'PAPI_PLUGIN_DIR', $mu_dir );
			} else {
				define( 'PAPI_PLUGIN_DIR', trailingslashit( __DIR__ ) );
			}
		}

		// URL to Papi plugin directory
		if ( ! defined( 'PAPI_PLUGIN_URL' ) ) {
			$plugin_url = plugin_dir_url( __FILE__ );

			if ( is_ssl() ) {
				$plugin_url = str_replace( 'http://', 'https://', $plugin_url );
			}

			define( 'PAPI_PLUGIN_URL', $plugin_url );
		}

		// The meta key that page type value is using
		if ( ! defined( 'PAPI_PAGE_TYPE_KEY' ) ) {
			define( 'PAPI_PAGE_TYPE_KEY', '_papi_page_type' );
		}
	}

	/**
	 * Load Localisation files.
	 *
	 * Locales found in:
	 * - WP_LANG_DIR/papi/papi-LOCALE.mo
	 * - WP_CONTENT_DIR/[mu-]plugins/papi/languages/papi-LOCALE.mo
	 */
	private function load_textdomain() {
		$locale = apply_filters( 'plugin_locale', get_locale(), 'papi' );
		load_textdomain( 'papi', WP_LANG_DIR . '/papi/papi-' . $locale . '.mo' );
		load_textdomain( 'papi', PAPI_PLUGIN_DIR . '../languages/papi-' . $locale . '.mo' );
	}

	/**
	 * Require files.
	 */
	private function require_files() {
		// Require the autoload class.
		require_once __DIR__ . '/core/class-papi-core-autoload.php';

		$lib_path     = __DIR__ . '/lib/';
		$lib_includes = [
			'utilities.php',
			'actions.php',
			'cache.php',
			'filters.php',
			'url.php',
			'post.php',
			'page.php',
			'property.php',
			'tabs.php',
			'io.php',
			'field.php',
			'template.php',
			'option.php',
			'deprecated.php',
			'conditional.php'
		];

		// Require function files.
		foreach ( $lib_includes as $file ) {
			if ( file_exists( $lib_path . $file ) ) {
				require_once $lib_path . $file;
			}
		}

		unset( $file );

		// Require admin classes that should not be loaded by the autoload.
		require_once __DIR__ . '/admin/class-papi-admin.php';
		require_once __DIR__ . '/admin/class-papi-admin-assets.php';
		require_once __DIR__ . '/admin/class-papi-admin-menu.php';

		// Require conditional rules that should not be loaded by the autoload.
		require_once __DIR__ . '/conditional/class-papi-conditional-rules.php';
	}

	/**
	 * Deactivate Papi if the WordPress version is lower then 4.0.
	 */
	public static function deactivate() {
		// Remove Papi from plugins_loaded action.
		remove_action( 'plugins_loaded', 'papi' );

		// Load is_plugin_active and deactivate_plugins.
		if ( ! function_exists( 'is_plugin_active' ) ) {
			include_once ABSPATH . 'wp-admin/includes/plugin.php';
		}

		$plugin_path = plugin_basename( __DIR__ . '/../' . basename( __FILE__ ) );

		// If the plugin is active then deactivate it.
		if ( is_plugin_active( $plugin_path ) ) {
			deactivate_plugins( $plugin_path );
		}

		wp_die( __( 'WordPress 4.0 and higher required to run Papi! The plugin has now disabled itself.', 'papi' ) );
	}

	/**
	 * Setup container.
	 */
	private function setup_container() {
		$this->singleton( 'porter', new Papi_Porter );
	}
}

/**
 * Return the instance of Papi to everyone.
 *
 * @return Papi_Loader
 */
function papi() {
	if ( version_compare( get_bloginfo( 'version' ), '4.0', '<' ) ) {
		Papi_Loader::deactivate();
	}

	return Papi_Loader::instance();
}

add_action( 'plugins_loaded', 'papi' );
