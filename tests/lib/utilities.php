<?php

/**
 * Page Type Builder Utilities functions for testing.
 *
 * @package PageTypeBuilder
 * @version 1.0.0
 */

// Exit if accessed directly
if (!defined('ABSPATH')) exit;

/**
 * Generate slug for testing.
 *
 * @param string $slug
 * @since 1.0.0
 *
 * @return string
 */

function _test_ptb_generate_slug ($slug) {
  return _ptb_f(_ptbify($slug));
}