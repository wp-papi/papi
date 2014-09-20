<?php

// Exit if accessed directly
if (!defined('ABSPATH')) exit;

/**
 * Papi Property.
 *
 * @package Papi
 * @version 1.0.0
 */

abstract class Papi_Property {

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
   * @return Papi_Property
   */

  public static function factory ($property) {
    if (!class_Exists($property)) {
      return null;
	  }

	  $prop = new $property();

    // Property class need to be a subclass of Papi Property class.
    if (!is_subclass_of($prop, 'Papi_property')) {
      return null;
    }

    return null;
  }

  /**
   * Get the html to display from the property.
   * This function is required by the property class to have.
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
   * Output custom js for property
   *
   * @since 1.0.0
   */

  public function js () {}

  /**
   * Register assets actions.
   *
   * @since 1.0.0
   */

  public function assets () {
    add_action('admin_head', array($this, 'css'));
    add_action('admin_footer', array($this, 'js'));
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
      $slug = _papi_property_type_key($slug);
      $slug .= ']';
    } else {
      $slug = _papi_property_type_key($slug);
    }

    ?>
    <input type="hidden" value="<?php echo $this->options->type; ?>" name="<?php echo $slug; ?>" />
    <?php
  }

  /**
   * Get label for the property.
   *
   * @since 1.0.0
   */

  public function label () {
    ?>
    <label for="<?php echo $this->options->slug; ?>"><?php echo $this->options->title; ?></label>
    <?php
  }

  /**
   * Get help text for property.
   *
   * @since 1.0.0
   */

  public function helptext () {
    if (empty($this->options->help_text)) {
      return;
    }

    $help_text = $this->options->help_text;
    $help_text = strip_tags($help_text);

    ?>
      <p><?php echo $help_text; ?></p>
    <?php
  }

  /**
   * Render the final html that is displayed in the table.
   *
   * @since 1.0.0
   */

  public function render () {
    if (!empty($this->options->title)):
    ?>
      <tr>
        <td>
          <?php
            $this->label();
            $this->helptext();
          ?>
        </td>
        <td>
          <?php $this->html(); ?>
        </td>
      </tr>
    <?php
    else:
      $this->html();
    endif;
  }

  /**
   * This filter is applied after the $value is loaded in the database.
   *
   * @param mixed $value
   * @param int $post_id
   * @since 1.0.0
   *
   * @return mixed
   */

  public function load_value ($value, $post_id) {
    return $value;
  }

  /**
   * Format the value of the property before we output it to the application.
   *
   * @param mixed $value
   * @param int $post_id
   * @since 1.0.0
   *
   * @return mixed
   */

  public function format_value ($value, $post_id) {
    return $value;
  }

  /**
   * This filter is applied before the $value is saved in the database.
   *
   * @param mixed $value
   * @param int $post_id
   * @since 1.0.0
   *
   * @return mixed
   */

  public function update_value ($value, $post_id) {
    return $value;
  }

  /**
   * Get the current property options object.
   *
   * @since 1.0.0
   *
   * @return string
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
   * @param mixed $default
   * @since 1.0.0
   *
   * @return mixed
   */

  public function get_value ($default = null) {
    $value = $this->options->value;

    if (empty($value)) {
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
   * @param string $css_class
   * @since 1.0.0
   *
   * @return string
   */

  public function css_classes ($css_class = '') {
    return $css_class . ' ' . $this->get_settings(array('css_class' => ''))->css_class;
  }
}
