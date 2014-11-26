<?php

/**
 * Plugin Name: Papi
 * Description: Page Type API
 * Author: Fredrik Forsmo
 * Author URI: http://forsmo.me/
 * Version: 1.0.0
 * Plugin URI: https://github.com/wp-papi/papi
 * Textdomain: papi
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Papi loader class.
 */

final class Papi_Loader {

	/**
	 * The instance of Papi loader class.
	 *
	 * @var object
	 * @since 1.0.0
	 */

	private static $instance;

	/**
	 * The plugin name.
	 *
	 * @var string
	 * @since 1.0.0
	 */

	public $name;

	/**
	 * The plugin version.
	 *
	 * @var string
	 * @since 1.0.0
	 */

	public $version;

	/**
	 * The plugin directory path.
	 *
	 * @var string
	 * @since 1.0.0
	 */

	private $plugin_dir;

	/**
	 * The plugin url path.
	 *
	 * @var string
	 * @since 1.0.0
	 */

	private $plugin_url;

	/**
	 * The plugin language directory path.
	 *
	 * @var string
	 * @since 1.0.0
	 */

	private $lang_dir;

	/**
	 * Papi loader instance.
	 *
	 * @since 1.0.0
	 *
	 * @return object
	 */

	public static function instance() {
		if ( ! isset( self::$instance ) ) {
			self::$instance = new Papi_Loader;
			self::$instance->constants();
			self::$instance->setup_globals();
			self::$instance->require_files();
			self::$instance->setup_requried();
			// Not used yet.
			//self::$instance->setup_actions();
		}

		return self::$instance;
	}

	/**
	 * Construct. Register autoloader.
	 *
	 * @since 1.0.0
	 * @access private
	 */

	private function __construct() {
		if ( function_exists( '__autoload' ) ) {
			spl_autoload_register( '__autoload' );
		}

		spl_autoload_register( array( $this, 'autoload' ) );
	}

	/**
	 * Cloning is forbidden.
	 *
	 * @since 2.1
	 */

	public function __clone() {
		_doing_it_wrong( __FUNCTION__, __( 'Cheatin&#8217; huh?', 'papi' ), '1.0.0' );
	}

	/**
	 * Unserializing instances of this class is forbidden.
	 *
	 * @since 2.1
	 */

	public function __wakeup() {
		_doing_it_wrong( __FUNCTION__, __( 'Cheatin&#8217; huh?', 'papi' ), '1.0.0' );
	}

	/**
	 * Bootstrap constants
	 *
	 * @since 1.0.0
	 * @access private
	 */

	private function constants() {
		// Path to Papi plugin directory
		if ( ! defined( 'PAPI_PLUGIN_DIR' ) ) {
			$mu_dir = trailingslashit( WPMU_PLUGIN_DIR . '/' . basename( __DIR__ ) );

			if ( is_dir( $mu_dir ) ) {
				define( 'PAPI_PLUGIN_DIR', $mu_dir );
			} else {
				define( 'PAPI_PLUGIN_DIR', trailingslashit( WP_PLUGIN_DIR . '/' . basename( __DIR__ ) ) );
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
	}

	/**
	 * Require files.
	 *
	 * @since 1.0.0
	 * @access private
	 */

	private function require_files() {
		// Load languages.
		$domain = 'papi';
		$path   = $this->plugin_dir . 'languages/' . $domain . '-' . get_locale() . '.mo';
		load_textdomain( $domain, $path );

		// Load Papi functions.
		require_once( $this->plugin_dir . 'includes/lib/utilities.php' );
		require_once( $this->plugin_dir . 'includes/lib/filters.php' );
		require_once( $this->plugin_dir . 'includes/lib/url.php' );
		require_once( $this->plugin_dir . 'includes/lib/post.php' );
		require_once( $this->plugin_dir . 'includes/lib/options.php' );
		require_once( $this->plugin_dir . 'includes/lib/page.php' );
		require_once( $this->plugin_dir . 'includes/lib/property.php' );
		require_once( $this->plugin_dir . 'includes/lib/io.php' );
		require_once( $this->plugin_dir . 'includes/lib/field.php' );
		require_once( $this->plugin_dir . 'includes/lib/template.php' );
		require_once( $this->plugin_dir . 'includes/lib/api.php' );

		// Load Papi classes that should not be autoloaded.
		require_once( $this->plugin_dir . 'includes/admin/class-papi-admin.php' );
		require_once( $this->plugin_dir . 'includes/class-papi-page.php' );
		require_once( $this->plugin_dir . 'includes/class-papi-property.php' );
		require_once( $this->plugin_dir . 'includes/page-type/class-papi-page-type-base.php' );
		require_once( $this->plugin_dir . 'includes/page-type/class-papi-page-type-meta.php' );
		require_once( $this->plugin_dir . 'includes/page-type/class-papi-page-type.php' );

		// Load Papi property classes.
		require_once( $this->plugin_dir . 'includes/properties/class-papi-property-string.php' );
		require_once( $this->plugin_dir . 'includes/properties/class-papi-property-hidden.php' );
		require_once( $this->plugin_dir . 'includes/properties/class-papi-property-bool.php' );
		require_once( $this->plugin_dir . 'includes/properties/class-papi-property-email.php' );
		require_once( $this->plugin_dir . 'includes/properties/class-papi-property-datetime.php' );
		require_once( $this->plugin_dir . 'includes/properties/class-papi-property-number.php' );
		require_once( $this->plugin_dir . 'includes/properties/class-papi-property-url.php' );
		require_once( $this->plugin_dir . 'includes/properties/class-papi-property-divider.php' );
		require_once( $this->plugin_dir . 'includes/properties/class-papi-property-text.php' );
		require_once( $this->plugin_dir . 'includes/properties/class-papi-property-image.php' );
		require_once( $this->plugin_dir . 'includes/properties/class-papi-property-dropdown.php' );
		require_once( $this->plugin_dir . 'includes/properties/class-papi-property-checkbox.php' );
		require_once( $this->plugin_dir . 'includes/properties/class-papi-property-repeater.php' );
		require_once( $this->plugin_dir . 'includes/properties/class-papi-property-relationship.php' );
		require_once( $this->plugin_dir . 'includes/properties/class-papi-property-radio.php' );
		require_once( $this->plugin_dir . 'includes/properties/class-papi-property-post.php' );

		// Include third party properties.
		$this->include_third_party();
	}

	/**
	 * Include third party properties.
	 *
	 * @since 1.0.0
	 * @access private
	 */

	private function include_third_party() {
		do_action( 'papi_include_properties' );
	}

	/**
	 * Setup required files.
	 *
	 * @since 1.0.0
	 * @access private
	 */

	private function setup_requried() {
		Papi_Admin::instance();
	}

	/**
	 * Setup globals.
	 *
	 * @since 1.0.0
	 * @access private
	 */

	private function setup_globals() {
		// Information globals.
		$this->name    = 'Papi';
		$this->version = '1.0.0';

		// Papi plugin directory and url.
		$this->plugin_dir = PAPI_PLUGIN_DIR;
		$this->plugin_url = PAPI_PLUGIN_URL;

		// Languages.
		$this->lang_dir = $this->plugin_dir . 'languages';
	}

	/**
	 * Setup the default hooks and actions.
	 *
	 * @since 1.0.0
	 * @access private
	 */

	// private function setup_actions () {}

	/**
	 * Auto load Papi classes on demand.
	 *
	 * @param mixed $class
	 *
	 * @since 1.0.0
	 */

	public function autoload( $class ) {
		$path  = null;
		$class = strtolower( $class );
		$file  = 'class-' . str_replace( '_', '-', $class ) . '.php';

		if ( strpos( $class, 'papi_admin' ) === 0 ) {
			$path = PAPI_PLUGIN_DIR . 'includes/admin/';
		} else if ( strpos( $class, 'property' ) === 0 && strpos( $class, 'papi' ) !== false ) {
			$path = PAPI_PLUGIN_DIR . 'includes/properties/';
		} else if ( strpos( $class, 'papi' ) === 0 ) {
			$path = PAPI_PLUGIN_DIR . 'includes/';
		}

		if ( ! is_null( $path ) && is_readable( $path . $file ) ) {
			include_once( $path . $file );

			return;
		}
	}
}

/**
 * Return the instance of Papi to everyone.
 *
 * @since 1.0.0
 *
 * @return object
 */

function papi() {
	return Papi_Loader::instance();
}

// Since we would have custom data in our theme directory we need to hook us up to 'after_setup_theme' action.
add_action( 'after_setup_theme', 'papi' );

/**
 * Register a directory that contains papi files.
 *
 * @param string $directory
 *
 * @since 1.0.0
 *
 * @return bool
 */

function register_page_types_directory( $directory ) {
	global $papi_directories;

	if ( ! is_array( $papi_directories ) ) {
		$papi_directories = array();
	}

	if ( ! is_dir( $directory ) ) {
		return false;
	}

	$papi_directories[] = $directory;

	return true;
}
