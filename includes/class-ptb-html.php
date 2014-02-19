<?php

// Exit if accessed directly
if (!defined('ABSPATH')) exit;

/**
 * Page Type Builder Html class.
 */

class PTB_Html {

  /**
   * Append attributes to html string.
   *
   * @param array $attributes
   * @since 1.0
   *
   * @return string
   */
  
  private static function attributes ($attributes = array()) {
    $html = '';
    foreach ($attributes as $key => $value) {
      $html .= ' ' . $key . '="' . $value . '" ';
    }
    return $html;
  }
  
  /**
   * Generate HTML label tag.
   *
   * @param string $title
   * @param string $for
   * @since 1.0
   *
   * @return string
   */

  public static function label ($title, $for) {
    return '<label for="' . $for . '">' . $title . '</label>';
  }

  /**
   * Generate HTML tr tag.
   *
   * @param string $inner
   * @since 1.0
   *
   * @return string
   */

  public static function tr ($inner) {
    return '<tr>' . $inner . '</tr>';
  }

  /**
   * Generate HTML td tag.
   *
   * @param string $inner
   * @param array $attributes
   * @since 1.0
   *
   * @return string
   */

  public static function td ($inner, $attributes = array()) {
    $html = '<td';
    $html .= self::attributes($attributes);
    return $html . '>' . $inner . '</td>';
  }

  /**
   * Generate HTML input tag.
   *
   * @param string $type
   * @param array $attributes
   * @since 1.0
   *
   * @return string
   */

  public static function input ($type, $attributes = array()) {
    $html = '<input type="' . $type . '"';
    $html .= self::attributes($attributes);
    return $html . '/>';
  }

}