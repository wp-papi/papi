<?php

/**
 * Page Type Builder Utilities Functions.
 *
 * @package PageTypeBuilder
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

function h (&$obj, $default = null) {
  return isset($obj) ? $obj : $default;
}

/**
 * Check if $obj is set and if not echo null or default.
 *
 * @param mixed $obj The var to check if it is set.
 * @param mixed $default The value to return if var is not set.
 * @since 1.0.0
 */

function eh (&$obj, $default = null) {
  echo h($obj, $default);
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

function remove_trailing_quotes ($str) {
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

function _f ($str) {
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

function _ptb_slugify ($str, $replace = array(), $delimiter = '-') {
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

function _ptb_underscorify ($str) {
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

function _ptb_dashify ($str) {
  return str_replace(' ', '-', str_replace('_', '-', $str));
}

/**
 * Add `ptb_` to the given string ad the start of the string.
 *
 * @param string $str
 * @since 1.0.0
 *
 * @return string
 */

function _ptbify ($str) {
  if (!preg_match('/^\_\_ptb|^\_ptb|^ptb\_/', $str) && !_ptb_is_random_title($str)) {
    return  'ptb_' . $str;
  }

  return $str;
}

/**
 * Remove `ptb-` or `ptb_` from the given string.
 *
 * @param string $str
 * @since 1.0.0
 *
 * @return string
 */

function _ptb_remove_ptb ($str) {
  return str_replace('ptb-', '', str_replace('ptb_', '', $str));
}

/**
 * Get a php friendly name.
 *
 * @param string $name
 * @since 1.0.0
 *
 * @return string
 */

function _ptb_name ($name) {
  if (!preg_match('/^\_\_ptb|^\_ptb/', $name)) {
    return _ptb_underscorify(_ptb_slugify(_ptbify($name)));
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

function _ptb_is_method ($method = '') {
  return strtoupper($_SERVER ['REQUEST_METHOD']) == strtoupper($method);
}

/**
 * Get class name from page type file.
 *
 * @param string $file
 * @since 1.0.0
 *
 * @return string|null
 */

function _ptb_get_class_name ($file) {
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