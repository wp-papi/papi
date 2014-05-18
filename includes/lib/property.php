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
 * Get property class by the type.
 *
 * @param string $type
 * @since 1.0.0
 *
 * @return object|null
 */

function _ptb_get_property ($type) {
  if (is_object($type) && isset($type->type) && is_string($type->type)) {
    $type = $type->type;
  }
  if (is_null($type) || empty($type)) {
    return null;
  }

  return PTB_Property::factory($type);
}

/**
 * Get property options.
 *
 * @param array $options
 * @since 1.0.0
 *
 * @return object|null
 */

function _ptb_get_property_options ($options) {
  $defaults = array(
    'title'      => _ptb_random_title(),
    'no_title'   => false,
    'disable'    => false,
    'name'       => '',
    'custom'     => new stdClass,
    'table'      => true,
    'sort_order' => 0,
    'value'      => '',
    'type'       => '',
    'colspan'    => ''
  );

  $options = array_merge($defaults, $options);
  $options = (object)$options;

  if ($options->no_title) {
    $options->title = '';
    $options->colspan = 2;
  }

  if (empty($options->name)) {
    // Generate a random title if no name is set and title is empty.
    // This make wp_editor and other stuff that go by name/id attributes to work.
    if (empty($options->title)) {
      $title = _ptb_random_title();
    } else {
      $title = $options->title;
    }

    $options->name = _ptb_slugify($title);
  }

  // Generate a vaild Page Type Builder meta name.
  $options->name = _ptb_name($options->name);

  if (!empty($options->colspan)) {
    $options->colspan = _ptb_attribute('colspan', $options->colspan);
  }

  return $options;
}