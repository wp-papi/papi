<?php

/**
 * Page Type Builder Properties Base class.
 */

class PTB_Properties_Base {

  /**
   * Data holder.
   *
   * @var array
   * @since 1.0
   * @access private
   */

  private $data = array();

  /**
   * Get property from the data array.
   *
   * @param string $property
   * @since 1.0
   *
   * @return mixed
   */

  public function __get ($property) {
    if (array_key_exists($property, $this->data)) {
      return $this->data[$property];
    }

    return null;
  }

  /**
   * Set property in the data array with the value.
   *
   * @param string $property
   * @since 1.0
   */

  public function __set ($property, $value) {
    $this->data[$property] = $value;
  }

  /**
   * Check if property is set or not.
   *
   * @param string $property
   * @since 1.0
   *
   * @return bool
   */

  public function __isset ($property) {
    return isset($this->data[$property]);
  }

  /**
   * Unset property from the data array.
   *
   * @param string $property
   * @since 1.0
   *
   * @return bool
   */

  public function __unset ($property) {
    unset($this->data[$property]);
  }

  /**
   * Generate html from a property type.
   *
   * @param string $type The property string
   * @param array $args Array of attributes
   * @param string $inner Inner html
   * @since 1.0
   *
   * @return string
   */

  public function toHTML ($type, $args = array(), $inner = '') {
    $attributes = '';
    foreach ($args as $key => $value) {
      $attributes .= ' ' . $key . '="' . $value . '" ';
    }
    $type = str_replace('{{attributes}}', $attributes, $type);
    return str_replace('{{inner}}', $inner, $type);
  }

}