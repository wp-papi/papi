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
    'PropertyDropdownList',
    'PropertyCheckboxList',
    'PropertyList',
    'PropertyPageReferenceList'
  );

  /**
   * Check if assets has been outputted or not.
   *
   * @var array
   * @since 1.0
   */

  private static $assets_up = array();

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
      if (!isset(self::$assets_up[$property])) {
        add_action('admin_head', array($klass, 'css'));
        add_action('admin_head', array($klass, 'autocss'));
        add_action('admin_footer', array($klass, 'js'));
        add_action('admin_footer', array($klass, 'autojs'));
        self::$assets_up[$property] = true;
      }
      return $klass;
    } else {
      return null;
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
    $name = _ptb_dashify($name);
    $file = 'properties/' . $name . '.css';
    $path = $this->css_dir . $file;
    $url = $this->css_url . $file;

    // Load css file.
    if (file_exists($path)) {
      wp_enqueue_style($file, $url);
    }

    // Load custom css files.
    $custom = _ptb_get_files_in_directory('gui', $file);
    $start = basename(WP_CONTENT_URL);
    $home_url = trailingslashit(home_url());

    foreach ($custom as $path) {
      $url = strstr($path, $start);
      $url = $home_url . $url;
      wp_enqueue_style($file, $url);
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
    $name = _ptb_dashify($name);
    $file = 'properties/' . $name . '.js';
    $path = $this->js_dir . $file;
    $url = $this->js_url . $file;

    // Load js file.
    if (file_exists($path)) {
      wp_enqueue_script($file, $url, array(), '1.0.0', true);
    }

    // Load custom js files.
    $custom = _ptb_get_files_in_directory('gui', $file);
    $start = basename(WP_CONTENT_URL);
    $home_url = trailingslashit(home_url());

    foreach ($custom as $path) {
      $url = strstr($path, $start);
      $url = $home_url . $url;
      wp_enqueue_script($file, $url, array(), '1.0.0', true);
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
      $title = $this->options->label_text;
    } else {
      $title = $this->options->title;
    }
    $name = $this->options->name;
    return PTB_Html::label($title, $name);
  }

  /**
   * Get help text for property.
   *
   * @param bool $empty_left
   *
   * @since 1.0
   *
   * @return string
   */

  public function helptext ($empty_left = true) {
    if (isset($this->options->help_text)) {
      $help_text = $this->options->help_text;
      $help_text = strip_tags($help_text);
      $html = PTB_Html::tag('span', $help_text, array(
        'class' => 'description'
      ));
      $html = PTB_Html::td($html);

      if ($empty_left) {
        $html = PTB_Html::td('&nbsp;') . $html;
      }

      $html = PTB_Html::tr($html, array(
        'class' => 'help-text'
      ));
      return $html;
    }
    return '';
  }

  /**
   * Render the final html that is displayed in the table.
   *
   * @since 1.0
   *
   * @return string
   */

  public function render () {
    if ($this->get_options()->table) {
      $html = PTB_Html::td($this->label());
      $html .= PTB_HTMl::td($this->html());
      $html = PTB_Html::tr($html);
      $html .= $this->helptext();
      return $html;
    }
    return $this->label() . $this->html() . $this->helptext();
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

  public function css_classes ($css_class = '') {
    if (isset($this->get_options()->custom->css_class)) {
      $css_class .= ' ' . $this->get_options()->custom->css_class;
    }

    return $css_class;
  }
}