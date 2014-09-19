<?php

/**
 * Plugin Name: Act
 * Description: Act is a page type builder for WordPress
 * Author: Fredrik Forsmo
 * Author URI: http://forsmo.me/
 * Version: 1.0.0
 * Plugin URI: https://github.com/wp-act/act
 * Textdomain: act
 */

// Exit if accessed directly.
if (!defined('ABSPATH')) exit;

/**
 * Act loader class.
 */

final class Act_Loader {

  /**
   * The instance of ACT_Loader class.
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
   * Act instance.
   *
   * @since 1.0.0
   *
   * @return object
   */

  public static function instance () {
    if (!isset(self::$instance)) {
      self::$instance = new Act_Loader;
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

  private function __construct () {
    if (function_exists('__autoload')) {
      spl_autoload_register('__autoload');
    }

    spl_autoload_register(array($this, 'autoload'));
  }

  /**
   * Bootstrap constants
   *
   * @since 1.0.0
   * @access private
   */

  private function constants () {
    // Path to Act plugin directory
    if (!defined('ACT_PLUGIN_DIR')) {
      define('ACT_PLUGIN_DIR', trailingslashit(WP_PLUGIN_DIR . '/' . basename(__DIR__)));
    }

    // URL to Act plugin directory
    if (!defined('ACT_PLUGIN_URL')) {
      $plugin_url = plugin_dir_url(__FILE__);

      if (is_ssl()) {
        $plugin_url = str_replace('http://', 'https://', $plugin_url);
      }

      define('ACT_PLUGIN_URL', $plugin_url);
    }

    // Property type key.
    if (!defined('ACT_PROPERTY_TYPE_KEY')) {
      define('ACT_PROPERTY_TYPE_KEY', '_property');
    }

    // Check for support for Polylang
    if (defined('POLYLANG_VERSION')) {
      define('ACT_POLYLANG', true);
    }
  }

  /**
   * Require files.
   *
   * @since 1.0.0
   * @access private
   */

  private function require_files () {
    // Load languages.
    $domain = 'act';
    $path = $this->plugin_dir . 'languages/' . $domain . '-' . get_locale() . '.mo';
    load_textdomain($domain, $path);

    // Load Act functions.
    require_once($this->plugin_dir . 'includes/lib/utilities.php');
    require_once($this->plugin_dir . 'includes/lib/core.php');
    require_once($this->plugin_dir . 'includes/lib/page.php');
    require_once($this->plugin_dir . 'includes/lib/property.php');
    require_once($this->plugin_dir . 'includes/lib/io.php');
    require_once($this->plugin_dir . 'includes/lib/field.php');
    require_once($this->plugin_dir . 'includes/lib/template.php');
    require_once($this->plugin_dir . 'includes/lib/admin.php');

    // Load Act classes that should not be autoloaded.
    require_once($this->plugin_dir . 'includes/admin/class-act-admin.php');
    require_once($this->plugin_dir . 'includes/class-act-page-type.php');
    require_once($this->plugin_dir . 'includes/class-act-page.php');
    require_once($this->plugin_dir . 'includes/class-act-property.php');
    require_once($this->plugin_dir . 'includes/class-act-page-data.php');

    // Load Act property classes.
    require_once($this->plugin_dir . 'includes/properties/class-property-string.php');
    require_once($this->plugin_dir . 'includes/properties/class-property-hidden.php');
    require_once($this->plugin_dir . 'includes/properties/class-property-boolean.php');
    require_once($this->plugin_dir . 'includes/properties/class-property-email.php');
    require_once($this->plugin_dir . 'includes/properties/class-property-date.php');
    require_once($this->plugin_dir . 'includes/properties/class-property-number.php');
    require_once($this->plugin_dir . 'includes/properties/class-property-url.php');
    require_once($this->plugin_dir . 'includes/properties/class-property-divider.php');
    require_once($this->plugin_dir . 'includes/properties/class-property-map.php');
    require_once($this->plugin_dir . 'includes/properties/class-property-text.php');
    require_once($this->plugin_dir . 'includes/properties/class-property-image.php');
    require_once($this->plugin_dir . 'includes/properties/class-property-dropdown.php');
    require_once($this->plugin_dir . 'includes/properties/class-property-checkbox.php');
    require_once($this->plugin_dir . 'includes/properties/class-property-list.php');
    require_once($this->plugin_dir . 'includes/properties/class-property-relationship.php');
    require_once($this->plugin_dir . 'includes/properties/class-property-radiobuttons.php');

    // Include third party properties.
    $this->include_third_party();
  }

  /**
   * Include third party properties.
   *
   * @since 1.0.0
   * @access private
   */

  private function include_third_party () {
    do_action('act/include_property_types');
  }

  /**
   * Setup required files.
   *
   * @since 1.0.0
   * @access private
   */

  private function setup_requried () {
    Act_Admin::instance();
  }

  /**
   * Setup globals.
   *
   * @since 1.0.0
   * @access private
   */

  private function setup_globals () {
    // Information globals.
    $this->name       = 'Act';
    $this->version    = '1.0.0';

    // Act plugin directory and url.
    $this->plugin_dir = ACT_PLUGIN_DIR;
    $this->plugin_url = ACT_PLUGIN_URL;

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
   * Auto load Act classes on demand.
   *
   * @param mixed $class
   * @since 1.0.0
   */

  public function autoload ($class) {
    $path = null;
    $class = strtolower($class);
    $file = 'class-' . str_replace( '_', '-', $class ) . '.php';

    if (strpos($class, 'act_admin') === 0) {
      $path = ACT_PLUGIN_DIR . 'includes/admin/';
    } else if (strpos($class, 'property') === 0 && strpos($class, 'act') !== false) {
      $path = ACT_PLUGIN_DIR . 'includes/properties/';
    } else if (strpos($class, 'act') === 0) {
      $path = ACT_PLUGIN_DIR . 'includes/';
    }

    if (!is_null($path) && is_readable($path . $file)) {
      include_once($path . $file);
      return;
    }
  }
}

/**
 * Return the instance of Act to everyone.
 *
 * @since 1.0.0
 *
 * @return object
 */

function act () {
  return ACT_Loader::instance();
}

// Since we would have custom data in our theme directory we need to hook us up to 'after_setup_theme' action.
add_action('after_setup_theme', 'act');

/**
 * Register a directory that contains Act files.
 *
 * @param string $directory Either the full filesystem path
 * @since 1.0.0
 *
 * @return bool
 */

function register_act_directory ($directory) {
  global $act_directories;

  if (!is_array($act_directories)) {
    $act_directories = array();
  }

  if (!file_exists($directory) || !is_dir($directory)) {
    return false;
  }

  $act_directories[] = $directory;

  return true;
}