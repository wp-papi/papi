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
	 * @return object
	 */

	public static function instance() {
		if ( ! isset( self::$instance ) ) {
			self::$instance = new static;
			self::$instance->constants();
			self::$instance->require_files();
		}

		return self::$instance;
	}

	/**
	 * Empty construct.
	 */

	private function __construct() {}

	/**
	 * Cloning is forbidden.
	 */

	public function __clone() {
		_doing_it_wrong( __FUNCTION__, __( 'Cheatin&#8217; huh?', 'papi' ), '1.0.0' );
	}

	/**
	 * Unserializing instances of this class is forbidden.
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

		// Load function files.
		require_once __DIR__ . '/includes/lib/utilities.php';
		require_once __DIR__ . '/includes/lib/actions.php';
		require_once __DIR__ . '/includes/lib/filters.php';
		require_once __DIR__ . '/includes/lib/url.php';
		require_once __DIR__ . '/includes/lib/post.php';
		require_once __DIR__ . '/includes/lib/page.php';
		require_once __DIR__ . '/includes/lib/property.php';
		require_once __DIR__ . '/includes/lib/tabs.php';
		require_once __DIR__ . '/includes/lib/io.php';
		require_once __DIR__ . '/includes/lib/field.php';
		require_once __DIR__ . '/includes/lib/template.php';
		require_once __DIR__ . '/includes/lib/option.php';

		// Load core classes.
		require_once __DIR__ . '/includes/page/class-papi-page-manager.php';
		require_once __DIR__ . '/includes/page/class-papi-page.php';
		require_once __DIR__ . '/includes/page/class-papi-option-page.php';

		require_once __DIR__ . '/includes/property/class-papi-property.php';
		require_once __DIR__ . '/includes/page-type/class-papi-page-type-base.php';
		require_once __DIR__ . '/includes/page-type/class-papi-page-type-meta.php';
		require_once __DIR__ . '/includes/page-type/class-papi-page-type.php';

		require_once __DIR__ . '/includes/option-type/class-papi-option-type.php';

		// Load admin classes.
		require_once __DIR__ . '/includes/admin/class-papi-admin-view.php';
		require_once __DIR__ . '/includes/admin/class-papi-admin-management-pages.php';
		require_once __DIR__ . '/includes/admin/class-papi-admin-meta-box.php';
		# require_once __DIR__ . '/includes/admin/class-papi-admin-meta-boxes.php';
		require_once __DIR__ . '/includes/admin/class-papi-admin-meta-box-tabs.php';
		require_once __DIR__ . '/includes/admin/class-papi-admin-data-handler.php';
		require_once __DIR__ . '/includes/admin/class-papi-admin-post-handler.php';
		require_once __DIR__ . '/includes/admin/class-papi-admin.php';
		require_once __DIR__ . '/includes/admin/class-papi-admin-ajax.php';

		// Load properties classes.
		require_once __DIR__ . '/includes/properties/class-papi-property-string.php';
		require_once __DIR__ . '/includes/properties/class-papi-property-hidden.php';
		require_once __DIR__ . '/includes/properties/class-papi-property-bool.php';
		require_once __DIR__ . '/includes/properties/class-papi-property-email.php';
		require_once __DIR__ . '/includes/properties/class-papi-property-datetime.php';
		require_once __DIR__ . '/includes/properties/class-papi-property-number.php';
		require_once __DIR__ . '/includes/properties/class-papi-property-url.php';
		require_once __DIR__ . '/includes/properties/class-papi-property-divider.php';
		require_once __DIR__ . '/includes/properties/class-papi-property-text.php';
		require_once __DIR__ . '/includes/properties/class-papi-property-image.php';
		require_once __DIR__ . '/includes/properties/class-papi-property-dropdown.php';
		require_once __DIR__ . '/includes/properties/class-papi-property-checkbox.php';
		require_once __DIR__ . '/includes/properties/class-papi-property-repeater.php';
		require_once __DIR__ . '/includes/properties/class-papi-property-relationship.php';
		require_once __DIR__ . '/includes/properties/class-papi-property-radio.php';
		require_once __DIR__ . '/includes/properties/class-papi-property-post.php';
		require_once __DIR__ . '/includes/properties/class-papi-property-color.php';
		require_once __DIR__ . '/includes/properties/class-papi-property-reference.php';
		require_once __DIR__ . '/includes/properties/class-papi-property-html.php';
		require_once __DIR__ . '/includes/properties/class-papi-property-gallery.php';
		require_once __DIR__ . '/includes/properties/class-papi-property-editor.php';
		require_once __DIR__ . '/includes/properties/class-papi-property-flexible.php';

		require_once __DIR__ . '/includes/admin/class-papi-admin-option-handler.php';

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

		wp_die( __( 'WordPress 3.9 and higher required to run Papi! The plugin has now disabled itself.', 'papi' ) );
	}
}

/**
 * Return the instance of Papi to everyone.
 *
 * @return object|null
 */

function papi() {
	if ( version_compare( get_bloginfo( 'version' ), '3.9', '<' ) ) {
		return Papi_Loader::deactivate();
	}

	return Papi_Loader::instance();
}

add_action( 'plugins_loaded', 'papi' );
