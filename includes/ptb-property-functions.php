<?php

/**
 * Page Type Builder Property Functions.
 *
 * @package PageTypeBuilder
 * @version 1.0.0
 */

// Exit if accessed directly
if (!defined('ABSPATH')) exit;

/**
 * Check if it's ends with '_property'.
 *
 * @param string $str
 * @since 1.0
 *
 * @return bool
 */

function _ptb_is_property_type_key ($str = '') {
  $pattern = PTB_PROPERTY_TYPE_KEY;
  $pattern = str_replace('_', '\_', $pattern);
  $pattern = str_replace('-', '\-', $pattern);
  $pattern = '/' . $pattern . '$/';
  return preg_match($pattern, $str);
}

/**
 * Get the right key for a property type.
 *
 * @param string $str
 * @since 1.0
 *
 * @return string
 */

function _ptb_property_type_key ($str = '') {
  return $str . PTB_PROPERTY_TYPE_KEY;
}

/**
 * Get property key.
 *
 * @param string $str
 * @since 1.0.0
 *
 * @return string
 */

function _ptb_property_key ($str) {
  return _f(_ptbify($str));
}

/**
 * Returns only values in the array and removes `{x}_property` key and value.
 *
 * @param array $a
 * @since 1.0
 *
 * @return array
 */

function _ptb_get_only_property_values ($a = array()) {
  foreach ($a as $key => $value) {
    if (_ptb_is_property_type_key($key)) {
      unset($a[$key]);
    }
  }
  return $a;
}

/**
 * Render property html.
 *
 * @param array $args
 * @since 1.0.0
 */

function _ptb_render_property_html ($args) {
  if (!is_array($args)) {
    return '';
  }

  switch ($args['action']) {
    case 'html':
      echo $args['html'];
      break;
    case 'wp_editor':
      wp_editor($args['value'], $args['name'], array(
        'textarea_name' => $args['name']
      ));
      break;
  }
}

/**
 * Get property class by the type.
 *
 * @param string $type
 * @since 1.0.0
 *
 * @return object|null
 */

function _ptb_get_property ($type) {
  if (is_null($type) || empty($type)) {
    return null;
  }

  return PTB_Property::factory($type);
}