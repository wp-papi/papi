<?php

/**
 * Page Type Builder Admin functions.
 *
 * @package PageTypeBuilder
 * @version 1.0.0
 */

// Exit if accessed directly
if (!defined('ABSPATH')) exit;

/**
 * Check if current is allowed the given capabilities.
 *
 * @since 1.0.0
 *
 * @return bool
 */

function _ptb_current_user_is_allowed ($capabilities = array()) {
  foreach (_ptb_string_array($capabilities) as $capability) {
    if (!current_user_can($capability)) return false;
  }

  return true;
}