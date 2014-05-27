<?php

// Exit if accessed directly
if (!defined('ABSPATH')) exit;

/**
 * Page Type Builder Property.
 *
 * @package PageTypeBuilder
 * @version 1.0.0
 */

abstract class PTB_Property {

  /**
   * Page Type Builder properties array.
   *
   * @var array
   * @since 1.0.0
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
    'PropertyPageReferenceList',
    'PropertyRadioButtons'
  );

  /**
   * Check if assets has been outputted or not.
   *
   * @var array
   * @since 1.0.0
   */

  private static $assets_up = array();

  /**
   * Current property options object that is used to generate a property.
   *
   * @var object
   * @since 1.0.0
   * @access private
   */

  private $options;

  /**
   * Create a new instance of the given property.
   *
   * @param string $property
   * @since 1.0.0
   *
   * @throws Exception
   * @return PTB_Property
   */

  public static function factory ($property) {
    self::filter_custom_properties();
    if (in_array($property, self::$properties) && class_exists($property)) {
      $klass = new $property();
      $klass->setup_globals();
      return $klass;
    } else {
      return null;
    }
  }

  /**
   * Setup globals.
   *
   * @since 1.0.0
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
   * @since 1.0.0
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
   * @since 1.0.0
   *
   * @return bool
   */

  public static function exists ($property) {
    return isset(self::$properties[$property]);
  }

  /**
   * Get the html to display from the property.
   *
   * @since 1.0.0
   */

  abstract public function html ();

  /**
   * Output custom css for property
   *
   * @since 1.0.0
   */

  public function css () {}

  /**
   * Output automatic js for property
   *
   * @since 1.0.0
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
  }

  /**
   * Output custom js for property
   *
   * @since 1.0.0
   */

  public function js () {}

  /**
   * Output automatic js for property
   *
   * @since 1.0.0
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
  }

  /**
   * Register assets actions.
   *
   * @since 1.0.0
   */

  public function assets () {
    add_action('admin_head', array($this, 'css'));
    add_action('admin_head', array($this, 'autocss'));
    add_action('admin_footer', array($this, 'js'));
    add_action('admin_footer', array($this, 'autojs'));
  }

  /**
   * Output hidden input field that cointains which property is used.
   *
   * @since 1.0.0
   */

  public function hidden () {
    $slug = $this->options->slug;

    if (substr($slug, -1) === ']') {
      $slug = substr($slug, 0, -1);
      $slug = _ptb_property_type_key($slug);
      $slug .= ']';
    } else {
      $slug = _ptb_property_type_key($slug);
    }

    echo PTB_Html::input('hidden', array(
      'name' => $slug,
      'value' => $this->options->type
    ));
  }

  /**
   * Get label for the property.
   *
   * @since 1.0.0
   */

  public function label () {
    if (isset($this->options->label_text)) {
      $title = $this->options->label_text;
    } else {
      $title = $this->options->title;
    }

    echo PTB_Html::label($title, $this->options->slug);
  }

  /**
   * Get help text for property.
   *
   * @param bool $empty_left
   *
   * @since 1.0.0
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
      echo $html;
    }
  }

  /**
   * Render the final html that is displayed in the table.
   *
   * @since 1.0.0
   *
   * @return string
   */

  public function render () {
    $options = $this->get_options();
    if ($options->table): ?>
      <tr>
        <?php if (!$options->no_title): ?>
        <td <?php echo $options->colspan; ?>><?php $this->label(); ?></td>
        <?php endif; ?>
        <td <?php echo $options->colspan; ?>><?php $this->html(); ?></td>
      </tr>
    <?php
      $this->helptext(empty($options->colspan));
    else:
      $this->label();
      $this->html();
      $this->helptext(false);
    endif;
  }

  /**
   * Convert the value of the property before we output it to the application.
   *
   * @param mixed $value
   * @since 1.0.0
   *
   * @return string
   */

  public function convert ($value) {
    return strval($value);
  }

  /**
   * Get the current property options object.
   *
   * @since 1.0.0
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
   * @since 1.0.0
   */

  public function set_options ($options) {
    $this->options = $options;
  }

  /**
   * Get database value.
   *
   * @param null $default
   * @since 1.0.0
   *
   * @return mixed
   */

  public function get_value ($default = null) {
    $value = $this->options->value;

    if (is_null($value)) {
      return $default;
    }

    if (is_string($value) && strlen($value) === 0) {
      return $default;
    }

    return $value;
  }

  /**
   * Get custom property settings.
   *
   * @param array $defaults
   * @since 1.0.0
   *
   * @return object
   */

  public function get_settings ($defaults = array()) {
    if (isset($this->options->settings)) {
      $custom = $this->options->settings;
    } else {
      $custom = array();
    }

    return (object)wp_parse_args((array)$custom, $defaults);
  }

  /**
   * Get css classes for the property.
   *
   * @param string $css_classes
   * @since 1.0.0
   *
   * @return string
   */

  public function css_classes ($css_class = '') {
    return $css_class . ' ' . $this->get_settings(array('css_class' => ''))->css_class;
  }
}