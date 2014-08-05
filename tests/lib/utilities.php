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
  list($lang) = _ptb_get_property_lang();
  $slug = _ptb_get_lang_field_slug($slug, $lang);
  return _ptb_f(_ptbify($slug));
}