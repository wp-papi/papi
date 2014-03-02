<?php

// Exit if accessed directly
if (!defined('ABSPATH')) exit;

/**
 * Page Type Builder Property class.
 */

abstract class PTB_Property {

  /**
   * Page Type Builder properties array.
   *
   * @var array
   * @since 1.0
   * @access private
   */

  private static $properties = array(
    'PropertyString',
    'PropertyBoolean',
    'PropertyEmail',
    'PropertyUrl',
    'PropertyNumber',
    'PropertyDate',
    'PropertyDivider',
    'PropertyMap',
    'PropertyText',
    'PropertyImage',
    'PropertyDropDownList',
    'PropertyCheckboxList'
  );

  /**
   * Current property options object that is used to generate a property.
   *
   * @var object
   * @since 1.0
   * @access private
   */

  private $options;

  /**
   * Create a new instance of the given property.
   *
   * @param string $property
   * @since 1.0
   *
   * @throws Exception
   * @return PTB_Property
   */

  public static function factory ($property) {
    self::filter_custom_properties();
    if (in_array($property, self::$properties) && class_exists($property)) {
      $klass = new $property();
      $klass->setup_globals();
      add_action('admin_head', array($klass, 'css'));
      add_action('admin_head', array($klass, 'autocss'));
      add_action('admin_footer', array($klass, 'js'));
      add_action('admin_footer', array($klass, 'autojs'));
      return $klass;
    } else {
      throw new Exception('PTB Error: Unsupported property - ' . $property);
    }
  }

  /**
   * Setup globals.
   *
   * @since 1.0
   * @access private
   */

  private function setup_globals () {
    $this->js_dir = PTB_PLUGIN_DIR . 'gui/js/';
    $this->js_url = PTB_PLUGIN_URL . 'gui/js/';
    $this->css_dir = PTB_PLUGIN_DIR . 'gui/css/';
    $this->css_url = PTB_PLUGIN_URL . 'gui/css/';
  }

  /**
   * Find custom properties that isn't register in this plugin.
   *
   * @since 1.0
   * @access private
   */

  private static function filter_custom_properties () {
    $result = apply_filters('ptb_properties', self::$properties);
    if (is_array($result)) {
      self::$properties = array_filter(array_unique(array_merge(self::$properties, $result)), function ($property) {
        return preg_match('/Property\w+/', $property);
      });
    }
  }

  /**
   * Check if the property exists in the properties array.
   *
   * @param string $property
   * @since 1.0
   *
   * @return bool
   */

  public static function exists ($property) {
    return isset(self::$properties[$property]);
  }

  /**
   * Get the current property options object.
   *
   * @since 1.0
   *
   * @return object|null
   */

  public function get_options () {
    return $this->options;
  }

  /**
   * Set the current property options object.
   *
   * @param object $options
   * @since 1.0
   */

  public function set_options ($options) {
    $this->options = $options;
  }

  /**
   * Get the html to display from the property.
   *
   * @since 1.0
   *
   * @return string
   */

  abstract public function html ();

  /**
   * Output custom css for property
   *
   * @since 1.0
   */

  public function css () {}

  /**
   * Output automatic js for property
   *
   * @since 1.0
   */

  public function autocss () {
    $name = get_class($this);
    $name = strtolower($name);
    $name = str_replace('property', 'property-', $name);
    $name = ptb_dashify($name);
    $file = 'properties/' . $name . '.css';
    $path = $this->css_dir . $file;
    $url = $this->css_url . $file;

    // Load css file.
    if (file_exists($path)) {
      wp_enqueue_style($file, $url);
    }

    // Load custom css file.
    if (PTB_CUSTOM_PATH !== false && PTB_CUSTOM_URL !== false) {
      $path = trailingslashit(PTB_CUSTOM_PATH) . $file;
      $url = trailingslashit(PTB_CUSTOM_URL) . $file;

      if (file_exists($path)) {
        wp_enqueue_style($file, $url);
      }
    }
  }

  /**
   * Output custom js for property
   *
   * @since 1.0
   */

  public function js () {}

  /**
   * Output automatic js for property
   *
   * @since 1.0
   */

  public function autojs () {
    $name = get_class($this);
    $name = strtolower($name);
    $name = str_replace('property', 'property-', $name);
    $name = ptb_dashify($name);
    $file = 'properties/' . $name . '.js';
    $path = $this->js_dir . $file;
    $url = $this->js_url . $file;

    // Load css file.
    if (file_exists($path)) {
      wp_enqueue_script($file, $url, array(), '1.0.0', true);
    }

    // Load custom css file.
    if (PTB_CUSTOM_PATH !== false && PTB_CUSTOM_URL !== false) {
      $path = trailingslashit(PTB_CUSTOM_PATH) . $file;
      $url = trailingslashit(PTB_CUSTOM_URL) . $file;

      if (file_exists($path)) {
        wp_enqueue_script($file, $url, array(), '1.0.0', true);
      }
    }
  }

  /**
   * Output hidden input field that cointains which property is used.
   *
   * @since 1.0
   *
   * @return string
   */

  public function hidden () {
    return PTB_Html::input('hidden', array(
      'name' => $this->get_options()->name . '_property',
      'value' => $this->get_options()->type
    ));
  }

  /**
   * Get label for the property.
   *
   * @since 1.0
   *
   * @return string
   */

  public function label () {
    if (isset($this->options->label_text)) {
      $title = $this->get_options()->label_text;
    } else {
      $title = $this->get_options()->title;
    }
    $name = $this->get_options()->name;
    return PTB_Html::label($title, $name);
  }

  /**
   * Render the final html that is displayed in the table.
   *
   * @since 1.0
   *
   * @return string
   */

  public function render () {
    $html = PTB_Html::td($this->label());
    $html .= PTB_HTMl::td($this->html());
    return PTB_HTml::tr($html);
  }

  /**
   * Convert the value of the property before we output it to the application.
   *
   * @param mixed $value
   * @since 1.0
   *
   * @return string
   */

  public function convert ($value) {
    return strval($value);
  }
  
  /**
   * Get css classes for the property.
   *
   * @param string $css_classes
   * @since 1.0
   *
   * @return string
   */
  
  public function css_classes ($css_classes = '') {
    if (isset($this->get_options()->custom->css_class)) {
      $css_class .= ' ' . $this->get_options()->custom->css_class;
    }
    
    return $css_classes;
  }
}