<?php

/**
 * Papi Utilities functions for testing.
 *
 * @package Papi
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

function _test_papi_generate_slug ($slug) {
  return _papi_f(_papify($slug));
}
