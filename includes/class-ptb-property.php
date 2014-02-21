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
    'PropertyDateTime',
    'PropertyTime',
    'PropertyColor',
    'PropertyDivider',
    'PropertyMap',
    'PropertyText',
    'PropertyTab',
    'PropertyLinkCollection',
    'PropertyList'
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
      add_action('admin_head', array($klass, 'css'));
      add_action('admin_head', array($klass, 'js'));
      return $klass;
    } else {
      throw new Exception('Unsupported property');
    }
  }

  /**
   * Add property to properties array.
   *
   * @param $property
   * @since 1.0
   */

  public static function add_property ($property) {
    self::$properties[] = $property;
  }

  /**
   * Find custom properties that isn't register in this plugin.
   *
   * @since 1.0
   */

  private static function filter_custom_properties () {
    $result = apply_filters('ptb_custom_properties', self::$properties);
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
   * Output custom js for property
   *
   * @since 1.0
   */
  
  public function js () {}
   
  /**
   * Output hidden input field that cointains which property is used.
   *
   * @since 1.0
   *
   * @return string
   */
   
  public function hidden () {
    return PTB_Html::input('hidden', array(
      'name' => $this->get_options()->name . '_property'
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

}