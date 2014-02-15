<?php

/**
 * Page Type Builder Properties class.
 */

class PTB_Properties {

  /**
   * Data holder.
   *
   * @var array
   * @since 1.0
   */

  private $data = array();

  /**
   * Get property from the data array.
   *
   * @param string $property
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

}