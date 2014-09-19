<?php

// Exit if accessed directly
if (!defined('ABSPATH')) exit;

/**
 * Act Page.
 *
 * @package Act
 * @version 1.0.0
 */

class Act_Page {

  /**
   * The WordPress post id.
   *
   * @var int
   * @since 1.0.0
   */

  public $id;

  /**
   * The WordPress post.
   *
   * @var object
   * @since 1.0.0
   */

  private $post;

  /**
   * The Page type.
   *
   * @var object.
   * @since 1.0.0
   */

  private $page_type;

  /**
   * The Page type
   */

  /**
   * Create a new instance of the class.
   *
   * @param int $post_id
   * @since 1.0.0
   */

  public function __construct ($post_id = 0) {
    $this->id = $post_id;
    $this->post = get_post($this->id);

    // Load page type object.
    $path = _act_get_page_type_file(_act_get_page_type_meta_value($this->id));
    $this->page_type = _act_get_page_type($path);
  }

  /**
   * Get Act Property value.
   *
   * @param string $slug
   * @since 1.0.0
   *
   * @return mixed
   */

  private function get_value ($slug) {
    $property_key = _act_property_key($slug);
    $property_value = get_post_meta($this->id, $property_key, true);

    if (is_null($property_value)) {
      return;
    }

    $property_type_key = _act_property_type_key($property_key);
    $property_type_value = get_post_meta($this->id, $property_type_key, true);

    if (is_null($property_type_value)) {
      return;
    }

    // The convert takes a array as argument so let's make one.
    if (!is_array($property_value)) {
      return $this->convert(array(
        'type'  => $property_type_value,
        'value' => $property_value
      ));
    }

    $convert = false;

    // Property List has array with properties.
    // Remove `act_` key and property key.
    foreach ($property_value as $ki => $vi) {
      if (is_array($property_value[$ki])) {
        foreach ($property_value[$ki] as $k => $v) {
          if (_act_is_property_type_key($k)) {
            unset($property_value[$ki][$k]);
          } else {
            $ptk = _act_property_type_key($k);
            $kn = _act_remove_act($k);
            $property_value[$ki][$kn] = $this->convert(array(
              'type'  => $property_value[$ki][$ptk],
              'value' => $v
            ));
            unset($property_value[$ki][$k]);
          }
        }
      } else {
        $convert = true;
        break;
      }
    }

    // Convert non property list arrays.
    if ($convert) {
      $property_value = $this->convert(array(
        'type'  => $property_type_value,
        'value' => $property_value
      ));
    }

    return array_filter($property_value);
  }

  /**
   * Convert property value with the property type converter.
   *
   * @param array $property
   * @since 1.0.0
   *
   * @return mixed|null
   */

  private function convert ($property) {
    if (!is_array($property)) {
      return $property;
    }

    // Try to convert the property value with a property type.
    if (isset($property['value']) && isset($property['type'])) {
      // Get the property type.
      $type = strval($property['type']);
      $property_type = _act_get_property_type($type);

      // If no property is found, just return the value.
      if (is_null($property_type)) {
        return $property['value'];
      }

      // Run a `load_value` right after the value has been loaded from the database.
      $property['value'] = $property_type->load_value($property['value'], $this->id);

      // Format the value before we return it.
      return $property_type->format_value($property['value'], $this->id);
    }

    // If we only have the value, let's return that.
    if (isset($property['value'])) {
      return $property['value'];
    }

    return $property;
  }

  /**
   * Check if the page has the post object and that it's not null
   *
   * @since 1.0.0
   *
   * @return bool
   */

  public function has_post () {
    return $this->post != null;
  }

  /**
   * Get the WordPress post object.
   *
   * @since 1.0.0
   *
   * @return object
   */

  public function get_post () {
    return $this->post;
  }

  /**
   * Get the permalink for the page.
   *
   * @since 1.0.0
   *
   * @return string
   */

  public function get_permalink () {
    return get_permalink($this->id);
  }

  /**
   * Get the post status of a page.
   *
   * @since 1.0.0
   *
   * @return string
   */

  public function get_status () {
    return get_post_status($this->id);
  }

  /**
   * Get Act property value.
   *
   * @param string $slug
   * @since 1.0.0
   *
   * @return mixed
   */

  public function __get ($slug) {
    return $this->get_value($slug);
  }

}
