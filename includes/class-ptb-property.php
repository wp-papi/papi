<?php

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
    'PropertyDivider'
  );

  /**
   * Create a new instance of the given property.
   *
   * @param string $property
   * @since 1.0
   *
   * @throws Exception
   * @return PTB_Property
   */

  public static function factory ($property) {
    $propertyClass = isset(self::$properties[$property]) ? self::$properties[$property] : null;
    if (!is_null($propertyClass)) {
      return new $propertyClass();
    } else {
      throw new Exception('Unsupported property');
    }
  }

  /**
   * Get the html to display from the property.
   *
   * @since 1.0
   *
   * @return string
   */

  abstract public function html ();

}