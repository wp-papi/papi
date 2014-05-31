<?php

// Exit if accessed directly
if (!defined('ABSPATH')) exit;

/**
 * Page Type Builder Html class.
 *
 *
 * @package PageTypeBuilder
 * @version 1.0.0
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
      if (!is_object($value) && !is_array($value)) {
        $html .= ' ' . $key . '="' . $value . '" ';
      }
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
   * @param array $attributes
   * @since 1.0
   *
   * @return string
   */

  public static function tr ($inner, $attributes = array()) {
    $html = '<tr';
    $html .= self::attributes($attributes);
    return $html . '>' . $inner . '</tr>';
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

  /**
   * Generate HTML textarea tag.
   *
   * @param string $inner
   * @param array $attributes
   * @since 1.0
   *
   * @return string
   */

  public static function textarea ($inner, $attributes = array()) {
    $html = '<textarea';
    $html .= self::attributes($attributes);
    return $html . '>' . $inner . '</textarea>';
  }

  /**
   * Generate dynamic html tag.
   *
   * @param string $tag
   * @param string $inner
   * @param array $attributes
   * @param bool $end
   * @since 1.0
   *
   * @return string
   */

  public static function tag ($tag, $inner = '', $attributes = array(), $end = true) {
    if (is_bool($inner)) {
      $end = $inner;
      $inner = '';
      $attributes = array();
    }

    if (is_bool($attributes)) {
      $end = $attributes;
      $attributes = array();
    }

    if (is_array($inner)) {
      $attributes = $inner;
      $inner = '';
    }

    $html = '<' . $tag;
    $html .= self::attributes($attributes);
    return $html . '>' . $inner . ($end ? '</' . $tag . '>' : '');
  }

  /**
   * Generate start tag.
   *
   * @param string $tag
   * @param array $attributes
   * @since 1.0
   *
   * @return string
   */

  public static function start ($tag, $attributes = array()) {
    return self::tag($tag, $attributes, false);
  }

  /**
   * Genrate stop tag.
   *
   * @param string $tag
   * @since 1.0
   *
   * @return string
   */

  public static function stop ($tag) {
    return '</' . $tag . '>';
  }

}