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
		$this->init();

		// Fires the loaded action.
		// Deprecated action. `papi/loaded` should be used instead.
		did_action( 'papi/include' ) || do_action( 'papi/include' );

		// Fires the loaded action.
		did_action( 'papi/loaded' ) || do_action( 'papi/loaded' );
	}

	/**
	 * Cloning is forbidden.
	 *
	 * @codeCoverageIgnore
	 */
	public function __clone() {
		_doing_it_wrong( __FUNCTION__, __( 'Cheatin&#8217; huh?', 'papi' ), '3.0.0-dev' );
	}

	/**
	 * Unserializing instances of this class is forbidden.
	 *
	 * @codeCoverageIgnore
	 */
	public function __wakeup() {
		_doing_it_wrong( __FUNCTION__, __( 'Cheatin&#8217; huh?', 'papi' ), '3.0.0-dev' );
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
		$this->define( 'PAPI_PAGE_TYPE_KEY', '_papi_page_type' );

		// The plugin basename that is used in actions to match so right plugin is modified.
		$this->define( 'PAPI_PLUGIN_BASENAME', basename( dirname( __DIR__ ) ) . '/papi-loader.php' );
	}

	/**
	 * Define constant if not already set.
	 *
	 * @param  string $name
	 * @param  string|bool $value
	 */
	private function define( $name, $value ) {
		if ( ! defined( $name ) ) {
			define( $name, $value );
		}
	}

	/**
	 * Initialise Papi.
	 */
	private function init() {
		// Fires the before init action.
		did_action( 'papi/before_init' ) || do_action( 'papi/before_init' );

		// Set up localisation.
		$this->load_textdomain();

		// Load all required files.
		$this->require_files();

		// Setup the container.
		$this->setup_container();

		// Fires the init action.
		did_action( 'papi/init' ) || do_action( 'papi/init' );
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
			'core/cache.php',
			'core/conditional.php',
			'core/deprecated.php',
			'core/page.php',
			'core/post.php',
			'core/io.php',
			'core/property.php',
			'core/tabs.php',
			'core/template.php',
			'core/url.php',
			'core/utilities.php',
			'hooks/actions.php',
			'hooks/filters.php',
			'fields/page.php',
			'fields/option.php',
			'types/content.php',
			'types/page.php',
			'types/option.php'
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

		// Load Papi CLI class if WP CLI is used.
		if ( defined( 'WP_CLI' ) && WP_CLI ) {
			require_once __DIR__ . '/cli/class-papi-cli.php';
		}
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
