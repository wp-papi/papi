<?php

/**
 * Act Utilities functions for testing.
 *
 * @package Act
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

function _test_act_generate_slug ($slug) {
  return _act_f(_actify($slug));
}
