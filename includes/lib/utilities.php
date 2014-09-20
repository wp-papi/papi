<?php

/**
 * Papi Utilities functions.
 *
 * @package Papi
 * @version 1.0.0
 */

// Exit if accessed directly
if (!defined('ABSPATH')) exit;

/**
 * Check if $obj is set and if not return null or default.
 *
 * @param mixed $obj The var to check if it is set.
 * @param mixed $default The value to return if var is not set.
 * @since 1.0.0
 *
 * @return mixed
 */

function _papi_h (&$obj, $default = null) {
  return isset($obj) ? $obj : $default;
}

/**
 * Remove trailing dobule quote.
 * PHP's $_POST object adds this automatic.
 *
 * @param string $str The string to check.
 * @since 1.0.0
 *
 * @return string
 */

function _papi_remove_trailing_quotes ($str) {
  return str_replace("\'", "'", str_replace('\"', '"', $str));
}

/**
 * Add a underscore at the start of the string.
 *
 * @param string $str
 * @since 1.0.0
 *
 * @return string
 */

function _papi_f ($str = '') {
  if (strpos($str, '_') === 0) {
    return $str;
  }

  return '_' . $str;
}

/**
 * Slugify the given string.
 *
 * @param string $str
 * @param array $replace
 * @param string $delimiter
 * @since 1.0.0
 *
 * @return string
 */

function _papi_slugify ($str, $replace = array(), $delimiter = '-') {
  setlocale(LC_ALL, 'en_US.UTF8');
  if(!empty($replace)) {
    $str = str_replace((array)$replace, ' ', $str);
  }
  $clean = iconv('UTF-8', 'ASCII//TRANSLIT', $str);
  $clean = preg_replace("/[^a-zA-Z0-9\/_|+ -]/", '', $clean);
  $clean = strtolower(trim($clean, '-'));
  $clean = preg_replace("/[\/_|+ -]+/", $delimiter, $clean);
  return trim($clean);
}

/**
 * Underscorify the given string.
 * Replacing whitespace and dash with a underscore.
 *
 * @param string $str
 * @since 1.0.0
 *
 * @return string
 */

function _papi_underscorify ($str) {
  return str_replace(' ', '_', str_replace('-', '_', $str));
}

/**
 * Dashify the given string.
 * Replacing whitespace and underscore with a dash.
 *
 * @param string $str
 * @since 1.0.0
 *
 * @return string
 */

function _papi_dashify ($str) {
  return str_replace(' ', '-', str_replace('_', '-', $str));
}

/**
 * Add `papi_` to the given string ad the start of the string.
 *
 * @param string $str
 * @since 1.0.0
 *
 * @return string
 */

function _papify ($str = '') {
  if (!preg_match('/^\_\_papi|^\_papi|^papi\_/', $str)) {
    return  'papi_' . $str;
  }

  return $str;
}

/**
 * Remove `papi-` or `papi_` from the given string.
 *
 * @param string $str
 * @since 1.0.0
 *
 * @return string
 */

function _papi_remove_papi ($str) {
  return str_replace('papi-', '', str_replace('papi_', '', $str));
}

/**
 * Get a php friendly name.
 *
 * @param string $name
 * @since 1.0.0
 *
 * @return string
 */

function _papi_name ($name) {
  if (!preg_match('/^\_\_papi|^\_papi/', $name)) {
    return _papi_underscorify(_papi_slugify(_papify($name)));
  }

  return $name;
}

/**
 * Check what the request method is.
 *
 * @param string $method
 * @since 1.0.0
 *
 * @return bool
 */

function _papi_is_method ($method = '') {
  return isset($_SERVER['REQUEST_METHOD']) && strtoupper($_SERVER['REQUEST_METHOD']) == strtoupper($method);
}

/**
 * Get class name from page type file.
 *
 * @param string $file
 * @since 1.0.0
 *
 * @return string|null
 */

function _papi_get_class_name ($file) {
  $content = file_get_contents($file);
  $tokens = token_get_all($content);
  $class_token = false;
  $class_name = null;

  foreach ($tokens as $token) {
    if (is_array($token)) {
      if ($token[0] === T_CLASS) {
        $class_token = true;
      } else if ($class_token && $token[0] === T_STRING) {
        $class_name = $token[1];
        $class_token = false;
      }
    }
  }

  return $class_name;
}

/**
 * Get html attribute string.
 *
 * @param string $name
 * @param string|array $value
 * @since 1.0.0
 *
 * @return string
 */

function _papi_attribute ($name, $value) {
  if (!is_array($value)) {
    $value = array($value);
  }

  return $name . '="' . implode(',', $value) . '"';
}

/**
 * Get query string if it exists and is not empty.
 *
 * @param string $qs
 * @since 1.0.0
 *
 * @return string
 */

function _papi_get_qs ($qs) {
  if (isset($_GET[$qs]) && !empty($_GET[$qs])) {
    return $_GET[$qs];
  }

  return null;
}

/**
 * Include partial view.
 *
 * @param string $tpl_file
 * @param array $vars
 *
 * @since 1.0.0
 */

function _papi_include_template ($tpl_file, $vars = array()) {
  $path = PAPI_PLUGIN_DIR;
  $path = rtrim($path, '/') . '/';

  include($path . $tpl_file);
}

/**
 * Get string value into a array.
 *
 * @param object|array|string $obj
 * @since 1.0.0
 *
 * @return array
 */

function _papi_string_array ($obj) {
  if (is_string($obj)) {
    $obj = array($obj);
  }

  if (!is_array($obj)) {
    $obj = array();
  }

  return $obj;
}

/**
 * Sort array based on given key and numeric value.
 *
 * @param array $array
 * @param string $key
 * @since 1.0.0
 *
 * @return array
 */

function _papi_sort_order ($array, $key = 'sort_order') {
  if (empty($array)) {
    return array();
  }

  $sorter = array();

  foreach ($array as $k => $value) {
    if (is_object($value)) {
      $sorter[$k] = $value->$key;
    } else {
      $sorter[$k] = $value[$key];
    }
  }

  asort($sorter, SORT_NUMERIC);

  $result = array();
  $rest = array();

  foreach ($sorter as $k => $v) {
    $value = $array[$k];

    if ((is_object($value) && !isset($value->$key)) || (is_array($value) && !isset($value[$key]))) {
      $rest[] = $value;
    } else {
      $result[$k] = $array[$k];
    }
  }

  $result = array_values($result);

  foreach ($rest as $key => $value) {
    $result[] = $value;
  }

  return $result;
}

/**
 * Check if polylang is used or not.
 *
 * @return bool
 */

function _papi_polylang () {
  return defined('PAPI_POLYLANG') && PAPI_POLYLANG;
}
