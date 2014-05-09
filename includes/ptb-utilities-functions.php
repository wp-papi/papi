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
 */

function eh (&$obj, $default = null) {
  echo h($obj, $default);
}

/**
 * Remove trailing dobule quote.
 * PHP's $_POST object adds this automatic.
 *
 * @param string $str The string to check.
 *
 * @return string
 */

function remove_trailing_quotes ($str) {
  return str_replace("\'", "'", str_replace('\"', '"', $str));
}