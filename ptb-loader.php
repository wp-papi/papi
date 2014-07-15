<?php

/**
 * Plugin Name: Page Type Builder
 * Description: Page Type Builder for WordPress
 * Author: Fredrik Forsmo
 * Author URI: http://forsmo.me/
 * Version: 1.0.0
 * Plugin URI: http://wp-ptb.com/
 * Textdomain: ptb
 */

// Exit if accessed directly.
if (!defined('ABSPATH')) exit;

/**
 * Page Type Builder Loader class.
 */

final class PTB_Loader {

  /**
   * The instance of Page Type Builder.
   *
   * @var object
   * @since 1.0.0
   */

  private static $instance;

  /**
   * Page Type Bulider instance.
   *
   * @since 1.0.0
   *
   * @return object
   */

  public static function instance () {
    if (!isset(self::$instance)) {
      self::$instance = new PTB_Loader;
      self::$instance->constants();
      self::$instance->setup_globals();
      self::$instance->require_files();
      self::$instance->setup_requried();
      self::$instance->setup_actions();
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
    // Path to Page Type Builder plugin directory
    if (!defined('PTB_PLUGIN_DIR')) {
      define('PTB_PLUGIN_DIR', trailingslashit(WP_PLUGIN_DIR . '/wp-ptb'));
    }

    // URL to Page Type Builder plugin directory
    if (!defined('PTB_PLUGIN_URL')) {
      $plugin_url = plugin_dir_url(__FILE__);

      if (is_ssl()) {
        $plugin_url = str_replace('http://', 'https://', $plugin_url);
      }

      define('PTB_PLUGIN_URL', $plugin_url);
    }

    // Property type key.
    if (!defined('PTB_PROPERTY_TYPE_KEY')) {
      define('PTB_PROPERTY_TYPE_KEY', '_property');
    }

    // Check for support for Polylang
    if (defined('POLYLANG_VERSION')) {
      define('PTB_POLYLANG', true);
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
    load_plugin_textdomain('ptb', false, $this->lang_dir);

    // Load Page Type Builder functions.
    require_once($this->plugin_dir . 'includes/lib/utilities.php');
    require_once($this->plugin_dir . 'includes/lib/core.php');
    require_once($this->plugin_dir . 'includes/lib/language.php');
    require_once($this->plugin_dir . 'includes/lib/page.php');
    require_once($this->plugin_dir . 'includes/lib/property.php');
    require_once($this->plugin_dir . 'includes/lib/io.php');
    require_once($this->plugin_dir . 'includes/lib/field.php');
    require_once($this->plugin_dir . 'includes/lib/template.php');
    require_once($this->plugin_dir . 'includes/lib/admin.php');

    // Load Page Type Builder classes that should not be autoloaded.
    require_once($this->plugin_dir . 'includes/class-ptb-core.php');
    require_once($this->plugin_dir . 'includes/class-ptb-page-type.php');
    require_once($this->plugin_dir . 'includes/class-ptb-page.php');
    require_once($this->plugin_dir . 'includes/class-ptb-property.php');
    require_once($this->plugin_dir . 'includes/class-ptb-page-data.php');

    // Load Page Type Builder property classes.
    require_once($this->plugin_dir . 'includes/properties/class-property-string.php');
    require_once($this->plugin_dir . 'includes/properties/class-property-boolean.php');
    require_once($this->plugin_dir . 'includes/properties/class-property-email.php');
    require_once($this->plugin_dir . 'includes/properties/class-property-date.php');
    require_once($this->plugin_dir . 'includes/properties/class-property-number.php');
    require_once($this->plugin_dir . 'includes/properties/class-property-url.php');
    require_once($this->plugin_dir . 'includes/properties/class-property-divider.php');
    require_once($this->plugin_dir . 'includes/properties/class-property-map.php');
    require_once($this->plugin_dir . 'includes/properties/class-property-text.php');
    require_once($this->plugin_dir . 'includes/properties/class-property-image.php');
    require_once($this->plugin_dir . 'includes/properties/class-property-dropdownlist.php');
    require_once($this->plugin_dir . 'includes/properties/class-property-checkboxlist.php');
    require_once($this->plugin_dir . 'includes/properties/class-property-list.php');
    require_once($this->plugin_dir . 'includes/properties/class-property-pagereferencelist.php');
    require_once($this->plugin_dir . 'includes/properties/class-property-radiobuttons.php');

    // Load custom properties.
    $this->require_custom_properties();
  }

  /**
   * Load custom properties.
   *
   * @since 1.0.0
   * @access private
   */

  private function require_custom_properties () {
    $files = _ptb_get_files_in_directory('properties');
    $this->properties = array();
    foreach ($files as $file) {
      $class_name = _ptb_get_class_name($file);
      if (preg_match('/Property\w+/', $class_name) && !class_exists($class_name)) {
        require_once($file);
        $this->properties[] = $class_name;
      }
    }
    add_filter('ptb_properties', array($this, 'add_custom_properties'));
  }

  /**
   * Add custom properties.
   *
   * @param array $properties
   * @since 1.0.0
   *
   * @return array
   */

  public function add_custom_properties ($properties) {
    return array_merge($properties, $this->properties);
  }

  /**
   * Setup required files.
   *
   * @since 1.0.0
   * @access private
   */

  private function setup_requried () {
    $this->core = PTB_Core::instance();
  }

  /**
   * Setup globals.
   *
   * @since 1.0.0
   * @access private
   */

  private function setup_globals () {
    // Information globals.
    $this->name       = 'Page Type Builder';
    $this->version    = '1.0.0';

    // Page Type Builder root directory.
    $this->file       = __FILE__;
    $this->basename   = plugin_basename($this->file);
    $this->plugin_dir = PTB_PLUGIN_DIR;
    $this->plugin_url = PTB_PLUGIN_URL;

    // Languages.
    $this->lang_dir = $this->plugin_dir . 'languages';
  }

  /**
   * Setup the default hooks and actions.
   *
   * @since 1.0.0
   * @access private
   */

  private function setup_actions () {}

  /**
   * Auto load Page Type Builder classes on demand.
   *
   * @param mixed $class
   * @since 1.0.0
   */

  public function autoload ($class) {
    $path = null;
    $class = strtolower($class);
    $file = 'class-' . str_replace( '_', '-', $class ) . '.php';

    if (strpos($class, 'ptb_admin') === 0) {
      $path = PTB_PLUGIN_DIR . 'includes/admin/';
    } else if (strpos($class, 'property') === 0 && strpos($class, 'ptb') !== false) {
      $path = PTB_PLUGIN_DIR . 'includes/properties/';
    } else if (strpos($class, 'ptb') === 0) {
      $path = PTB_PLUGIN_DIR . 'includes/';
    }

    if (!is_null($path) && is_readable($path . $file)) {
      include_once($path . $file);
      return;
    }
  }
}

/**
 * Return the instance of Page Type Builder to everyone.
 *
 * @since 1.0.0
 *
 * @return object
 */

function page_type_builder () {
  return PTB_Loader::instance();
}

// Since we would have custom data in our theme directory we need to hook us up to 'after_setup_theme' action.
add_action('after_setup_theme', 'page_type_builder');

/**
 * Register a directory that contains Page Type Builder files.
 *
 * @param string $directory Either the full filesystem path
 * @since 1.0.0
 *
 * @return bool
 */

function register_ptb_directory ($directory) {
  global $ptb_directories;

  if (!is_array($ptb_directories)) {
    $ptb_directories = array();
  }

  if (!file_exists($directory) || !is_dir($directory)) {
    return false;
  }

  $ptb_directories[] = $directory;

  return true;
}