<?php

// Exit if accessed directly
defined( 'ABSPATH' ) || exit;

use Tank\Container;

/**
 * Papi loader class.
 *
 * @package Papi
 */
final class Papi_Loader extends Container {

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
			self::$instance->constants();
			self::$instance->setup_actions();
			self::$instance->require_files();
		}

		return self::$instance;
	}

	/**
	 * The constructor.
	 *
	 * @codeCoverageIgnore
	 */
	private function __construct() {
	}

	/**
	 * Cloning is forbidden.
	 *
	 * @codeCoverageIgnore
	 */
	public function __clone() {
		_doing_it_wrong( __FUNCTION__, __( 'Cheatin&#8217; huh?', 'papi' ), '1.0.0' );
	}

	/**
	 * Unserializing instances of this class is forbidden.
	 *
	 * @codeCoverageIgnore
	 */
	public function __wakeup() {
		_doing_it_wrong( __FUNCTION__, __( 'Cheatin&#8217; huh?', 'papi' ), '1.0.0' );
	}

	/**
	 * Bootstrap constants
	 */
	private function constants() {
		// Path to Papi plugin directory
		if ( ! defined( 'PAPI_PLUGIN_DIR' ) ) {
			$mu_dir = trailingslashit( WPMU_PLUGIN_DIR . '/' . basename( dirname( __DIR__ ) ) . '/src' );

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
	 * Require files.
	 */
	private function require_files() {
		// Load languages.
		$domain = 'papi';
		$path   = __DIR__ . '/../languages/' . $domain . '-' . get_locale() . '.mo';
		load_textdomain( $domain, $path );

		// Require the autoload class.
		require_once __DIR__ . '/includes/core/class-papi-core-autoload.php';

		$lib_path     = __DIR__ . '/includes/lib/';
		$lib_includes = [
			'utilities.php',
			'actions.php',
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

		// Require admin classes.
		require_once __DIR__ . '/includes/admin/class-papi-admin.php';
		require_once __DIR__ . '/includes/admin/class-papi-admin-menu.php';

		// Require conditional rules.
		require_once __DIR__ . '/includes/conditional/class-papi-conditional-rules.php';

		// Include plugins or properties.
		papi_action_include();
	}

	/**
	 * Deactivate Papi if the WordPress version is lower then 3.8.
	 */
	public static function deactivate() {
		// Remove Papi from plugins_loaded action.
		remove_action( 'plugins_loaded', 'papi' );

		// Load is_plugin_active and deactivate_plugins.
		if ( ! function_exists( 'is_plugin_active' ) ) {
			include_once ABSPATH . 'wp-admin/includes/plugin.php';
		}

		$plugin_path = plugin_basename( dirname( __DIR__ ) . '/' . basename( __FILE__ ) );

		// If the plugin is active then deactivate it.
		if ( is_plugin_active( $plugin_path ) ) {
			deactivate_plugins( $plugin_path );
		}

		wp_die( __( 'WordPress 4.0 and higher required to run Papi! The plugin has now disabled itself.', 'papi' ) );
	}

	/**
	 * Setup actions.
	 */
	private function setup_actions() {
		add_action( 'after_setup_theme', 'papi_action_include' );
	}
}

/**
 * Return the instance of Papi to everyone.
 *
 * @package Papi
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
