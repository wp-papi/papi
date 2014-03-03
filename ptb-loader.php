<?php

/**
 * Plugin Name: Page Type Builder
 * Description: Page Type Builder for WordPress
 * Author: Fredrik Forsmo
 * Author URI: http://forsmo.me/
 * Version: 1.0.0
 * Plugin URI: http://wp-ptb.com/
 */

// Exit if accessed directly.
if (!defined('ABSPATH')) exit;

/**
 * Page Type Builder Loader class.
 */

class PTB_Loader {

  /**
   * The instance of Page Type Builder.
   *
   * @var object
   * @since 1.0
   */

  private static $instance;

  /**
   * Page Type Bulider instance.
   *
   * @since 1.0
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
   * Construct. Nothing to see.
   *
   * @since 1.0
   * @access private
   */

  private function __construct () {}

  /**
   * Bootstrap constants
   *
   * @since 1.0
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

    // Our meta key that is used to save the data array on pages.
    if (!defined('PTB_META_KEY')) {
      define('PTB_META_KEY', '_ptb_meta');
    }

    // Used for random titles etc.
    if (!defined('PTB_RANDOM_KEY')) {
      define('PTB_RANDOM_KEY', '_PTB_');
    }

    // Collection key.
    if (!defined('PTB_COLLECTION_KEY')) {
      define('PTB_COLLECTION_KEY', 'ptb_collection');
    }

    // Property type key.
    if (!defined('PTB_PROPERTY_TYPE_KEY')) {
      define('PTB_PROPERTY_TYPE_KEY', '_property');
    }

    // Path to custom wp-ptb directory.
    if (!defined('PTB_CUSTOM_PATH')) {
      define('PTB_CUSTOM_PATH', false);
    } else {
      define('PTB_PAGES_DIR', trailingslashit(PTB_CUSTOM_PATH . '/page-types'));
    }

    // Url to custom wp-ptb directory.
    if (!defined('PTB_CUSTOM_URL')) {
      define('PTB_CUSTOM_URL', false);
    }
    
    /*

    Custom wp-ptb directory structure:

      - custom wp-ptb dir
        - gui
          - js
          - css
        - properties
        - page-types

    */

  }

  /**
   * Require files.
   *
   * @since 1.0
   * @access private
   */

  private function require_files () {
    // Load Page Type Builder.
    require_once($this->plugin_dir . 'includes/ptb-functions.php');
    require_once($this->plugin_dir . 'includes/ptb-actions.php');
    require_once($this->plugin_dir . 'includes/class-ptb-html.php');
    require_once($this->plugin_dir . 'includes/class-ptb-core.php');
    require_once($this->plugin_dir . 'includes/class-ptb-view.php');
    require_once($this->plugin_dir . 'includes/class-ptb-property.php');
    require_once($this->plugin_dir . 'includes/class-ptb-tab.php');
    require_once($this->plugin_dir . 'includes/class-ptb-collection.php');

    // Load properties
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

    // Load custom properties
    $this->require_custom_files();

    // Load Page Type Builder base file.
    require_once($this->plugin_dir . 'includes/class-ptb-base.php');
  }

  /**
   * Require custom files.
   *
   * @since 1.0
   * @access private
   */

  private function require_custom_files () {
    if (defined('PTB_CUSTOM_PATH') && is_string(PTB_CUSTOM_PATH)) {
      $files = glob(trailingslashit(PTB_CUSTOM_PATH . '/properties') . '*');
      foreach ($files as $file) {
        require_once($file);
      }
    }
  }

  /**
   * Setup required files.
   *
   * @since 1.0
   * @access private
   */

  private function setup_requried () {
    $this->core = new PTB_Core;
  }

  /**
   * Setup globals.
   *
   * @since 1.0
   * @access private
   */

  private function setup_globals () {
    $this->file       = __FILE__;
    $this->basename   = plugin_basename($this->file);
    $this->plugin_dir = PTB_PLUGIN_DIR;
    $this->plugin_url = PTB_PLUGIN_URL;

    $this->name       = __('Page Type Builder', 'ptb');
  }

  /**
   * Setup the default hooks and actions.
   *
   * @since 1.0
   * @access private
   */

  private function setup_actions () {
    // add_action('activate_' . $this->basename, 'ptb_activation');
    // add_action('deactivate_' . $this->basename, 'ptb_deactivation');
  }
}

/**
 * Return the instance of Page Type Builder to everyone.
 *
 * @since 1.0
 *
 * @return object
 */

function page_type_builder () {
  return PTB_Loader::instance();
}

/** 
 * Since we would have custom data in our theme directory we need to hook us up to 'after_setup_theme' action.
 *
 * @since 1.0
 */

function ptb_after_theme_setup () {
  // Let's make it global too!
  $_GLOBALS['ptb'] = &page_type_builder();
}

add_action('after_setup_theme', 'ptb_after_theme_setup');