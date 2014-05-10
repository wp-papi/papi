<?php

/**
 * Page Type Builder Core Functions.
 *
 * @package PageTypeBuilder
 * @version 1.0.0
 */

// Exit if accessed directly
if (!defined('ABSPATH')) exit;

/**
 * Get post or page id from a object.
 *
 * @param object $post_id
 * @since 1.0
 *
 * @return int
 */

function _ptb_get_post_id ($post_id = null) {
  if (is_object($post_id)) {
    return $post_id->ID;
  }

  if (is_null($post_id) && get_post()) {
    return get_the_ID();
  }

  if (is_null($post_id) && isset($_GET['post'])) {
    return intval($_GET['post']);
  }

  if (is_null($post_id) && isset($_GET['page_id'])) {
    return intval($_GET['page_id']);
  }

  return $post_id;
}

/**
 * Generate random title for property.
 *
 * @since 1.0
 *
 * @return string
 */

function _ptb_random_title () {
  return PTB_RANDOM_KEY . uniqid();
}

/**
 * Check if it's a random ptb title string.
 *
 * @param string $str
 * @since 1.0
 *
 * @return bool
 */

function _ptb_is_random_title ($str = '') {
  return preg_match('/^' . PTB_RANDOM_KEY . '/', $str);
}

/**
 * Get Page Type Builder Core class instance.
 *
 * @since 1.0.0
 *
 * @return object
 */

function _ptb_core () {
  return PTB_Core::instance();
}

/**
 * Get Page Type Builder settings.
 *
 * @since 1.0.0
 *
 * @return array
 */

function _ptb_get_settings () {
  $defaults = array(
    'post_types' => array('page')
  );

  return array_merge($defaults, _ptb_core()->get_settings());
}

/**
 * Get all post types Page Type Builder should work with.
 *
 * @since 1.0.0
 *
 * @return array
 */

function _ptb_get_post_types () {
  $settings = _ptb_get_settings();
  return $settings['post_types'];
}

/**
 * Get WordPress post type in various ways
 *
 * @since 1.0
 *
 * @return string
 */

function _ptb_get_wp_post_type () {
  if (isset($_GET['post_type'])) {
    return strtolower($_GET['post_type']);
  }

  if (isset($_POST['post_type'])) {
    return strtolower($_POST['post_type']);
  }

  $post_id = _ptb_get_post_id();

  if ($post_id != 0) {
    return strtolower(get_post_type($post_id));
  }

  return null;
}