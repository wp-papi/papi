<?php

// Exit if accessed directly
defined( 'ABSPATH' ) || exit;

/**
 * Load Papi Container.
 */

require_once __DIR__ . '/includes/container/class-papi-container.php';

/**
 * Papi loader class.
 */

final class Papi_Loader extends Papi_Container {

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
	 * The plugin directory path.
	 *
	 * @var string
	 * @since 1.0.0
	 */

	private $plugin_dir;

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
			self::$instance = new static;
			self::$instance->constants();
			self::$instance->setup_globals();
			self::$instance->require_files();
			self::$instance->setup_requried();
		}

		return self::$instance;
	}

	/**
	 * Empty construct.
	 *
	 * @since 1.0.0
	 */

	private function __construct() {}

	/**
	 * Cloning is forbidden.
	 *
	 * @since 1.0.0
	 */

	public function __clone() {
		_doing_it_wrong( __FUNCTION__, __( 'Cheatin&#8217; huh?', 'papi' ), '1.0.0' );
	}

	/**
	 * Unserializing instances of this class is forbidden.
	 *
	 * @since 1.0.0
	 */

	public function __wakeup() {
		_doing_it_wrong( __FUNCTION__, __( 'Cheatin&#8217; huh?', 'papi' ), '1.0.0' );
	}

	/**
	 * Bootstrap constants
	 *
	 * @since 1.0.0
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
	 *
	 * @since 1.0.0
	 */

	private function require_files() {
		// Load languages.
		$domain = 'papi';
		$path   = dirname( $this->plugin_dir ) . '/languages/' . $domain . '-' . get_locale() . '.mo';

		load_textdomain( $domain, $path );

		// Load function files.
		require_once $this->plugin_dir . 'includes/lib/utilities.php';
		require_once $this->plugin_dir . 'includes/lib/actions.php';
		require_once $this->plugin_dir . 'includes/lib/filters.php';
		require_once $this->plugin_dir . 'includes/lib/url.php';
		require_once $this->plugin_dir . 'includes/lib/post.php';
		require_once $this->plugin_dir . 'includes/lib/page.php';
		require_once $this->plugin_dir . 'includes/lib/property.php';
		require_once $this->plugin_dir . 'includes/lib/tabs.php';
		require_once $this->plugin_dir . 'includes/lib/io.php';
		require_once $this->plugin_dir . 'includes/lib/field.php';
		require_once $this->plugin_dir . 'includes/lib/template.php';

		// Load core classes.
		require_once $this->plugin_dir . 'includes/page/class-papi-page.php';
		require_once $this->plugin_dir . 'includes/property/class-papi-property.php';
		require_once $this->plugin_dir . 'includes/page-type/class-papi-page-type-base.php';
		require_once $this->plugin_dir . 'includes/page-type/class-papi-page-type-meta.php';
		require_once $this->plugin_dir . 'includes/page-type/class-papi-page-type.php';

		// Load admin class
		require_once $this->plugin_dir . 'includes/admin/class-papi-admin-management-pages.php';
		require_once $this->plugin_dir . 'includes/admin/class-papi-admin-meta-box.php';
		require_once $this->plugin_dir . 'includes/admin/class-papi-admin-meta-boxes.php';
		require_once $this->plugin_dir . 'includes/admin/class-papi-admin-meta-box-tabs.php';
		require_once $this->plugin_dir . 'includes/admin/class-papi-admin-view.php';
		require_once $this->plugin_dir . 'includes/admin/class-papi-admin.php';
		require_once $this->plugin_dir . 'includes/admin/class-papi-admin-ajax.php';

		// Load properties classes.
		require_once $this->plugin_dir . 'includes/properties/class-papi-property-string.php';
		require_once $this->plugin_dir . 'includes/properties/class-papi-property-hidden.php';
		require_once $this->plugin_dir . 'includes/properties/class-papi-property-bool.php';
		require_once $this->plugin_dir . 'includes/properties/class-papi-property-email.php';
		require_once $this->plugin_dir . 'includes/properties/class-papi-property-datetime.php';
		require_once $this->plugin_dir . 'includes/properties/class-papi-property-number.php';
		require_once $this->plugin_dir . 'includes/properties/class-papi-property-url.php';
		require_once $this->plugin_dir . 'includes/properties/class-papi-property-divider.php';
		require_once $this->plugin_dir . 'includes/properties/class-papi-property-text.php';
		require_once $this->plugin_dir . 'includes/properties/class-papi-property-image.php';
		require_once $this->plugin_dir . 'includes/properties/class-papi-property-dropdown.php';
		require_once $this->plugin_dir . 'includes/properties/class-papi-property-checkbox.php';
		require_once $this->plugin_dir . 'includes/properties/class-papi-property-repeater.php';
		require_once $this->plugin_dir . 'includes/properties/class-papi-property-relationship.php';
		require_once $this->plugin_dir . 'includes/properties/class-papi-property-radio.php';
		require_once $this->plugin_dir . 'includes/properties/class-papi-property-post.php';
		require_once $this->plugin_dir . 'includes/properties/class-papi-property-color.php';
		require_once $this->plugin_dir . 'includes/properties/class-papi-property-reference.php';
		require_once $this->plugin_dir . 'includes/properties/class-papi-property-html.php';
		require_once $this->plugin_dir . 'includes/properties/class-papi-property-gallery.php';
		require_once $this->plugin_dir . 'includes/properties/class-papi-property-editor.php';
		require_once $this->plugin_dir . 'includes/properties/class-papi-property-flexible.php';

		// Include plugins or properties.
		papi_action_include();
	}

	/**
	 * Setup required files.
	 *
	 * @since 1.0.0
	 */

	private function setup_requried() {
		Papi_Admin::instance();
	}

	/**
	 * Setup globals.
	 *
	 * @since 1.0.0
	 */

	private function setup_globals() {
		// Information globals.
		$this->name = 'Papi';

		// Papi plugin directory and url.
		$this->plugin_dir = PAPI_PLUGIN_DIR;

		// Languages.
		$this->lang_dir = $this->plugin_dir . 'languages';
	}

	/**
	 * Deactivate Papi if the WordPress version is lower then 3.8.
	 *
	 * @since 1.2.0
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

		wp_die( __( 'WordPress 3.8 and higher required to run Papi! The plugin has now disabled itself.', 'papi' ) );
	}
}

/**
 * Return the instance of Papi to everyone.
 *
 * @since 1.0.0
 *
 * @return object|null
 */

function papi() {
	if ( version_compare( get_bloginfo( 'version' ), '3.8', '<' ) ) {
		return Papi_Loader::deactivate();
	}

	return Papi_Loader::instance();
}

add_action( 'plugins_loaded', 'papi' );
