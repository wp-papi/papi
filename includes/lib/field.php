<?php

/**
 * Page Type Builder Field Functions.
 *
 * @package PageTypeBuilder
 * @version 1.0.0
 */

// Exit if accessed directly
if (!defined('ABSPATH')) exit;

/**
 * Get property value for property on a page.
 *
 * @param int $post_id
 * @param string $name
 * @param mixed $default
 * @param string $lang
 * @param string $old_name
 * @since 1.0.0
 *
 * @return mixed
 */

function ptb_field ($post_id = null, $name = null, $default = null, $lang = null, $old_name = null) {
  // Check if we have a post id or not.
  if (!is_numeric($post_id) && is_string($post_id)) {
    $default = $name;
    $name = $post_id;
    $post_id = null;
  }

  // If it's a numeric value, let's convert it to int.
  if (is_numeric($post_id)) {
    $post_id = intval($post_id);
  } else {
    $post_id = _ptb_get_post_id();
  }

  // Return the default value if we don't have a name.
  if (is_null($name)) {
    return $default;
  }

  // Get the page.
  $page = ptb_get_page($post_id);

  // Return the default value if we don't have a WordPress post on the page object.
  if (is_null($page) || !$page->has_post()) {
    return $default;
  }

  // Check for "dot" notation.
  $names = explode('.', $name);

  // Remove any `ptb_` stuff if it exists.
  $name = _ptb_remove_ptb($names[0]);

  // Add language code.
  $name = _ptb_get_lang_field_slug($name, $lang);

  // Remove the first value of the array.
  $names = array_slice($names, 1);

  // Get value.
  $value = $page->$name;

  // Try to get the language code value.
  if (!empty($old_name) && empty($value)) {
    $old_name = _ptb_remove_ptb($old_name);
    $value = $page->$old_name;
  }

  // Return default value we don't have a value.
  if (!isset($value) || is_null($value)) {
    return $default;
  }

  // Check if it's a array value or object.
  if (!empty($names) && (is_object($value) || is_array($value))) {

    // Convert object to array.
    if (is_object($value)) {
      $value = (array)$value;
    }

    foreach ($names as $key) {
      if (isset($value[$key])) {
        $value = $value[$key];
      }
    }

    return $value;
  }

  return $value;
}

/**
 * Shortcode for `ptb_field` function.
 *
 * [ptb_field id=1 name="field_name" default="Default value"][/ptb_field]
 *
 * @param array $atts
 * @param string $content
 *
 * @return mixed
 */

function ptb_field_shortcode ($atts, $content = null) {
  // Extract arguments.
  extract(shortcode_atts(array(
    'id'      => null,
    'name'    => null,
    'default' => null
  ), $atts));

  // Try to fetch to post id.
  if (is_null($id)) {
    global $post;
    if (isset($post) && isset($post->ID)) {
      $id = $post->ID;
    }
  }

  // Fetch value.
  if (!is_null($id)) {
    $value = ptb_field($id, $name, $default);
  }

  // Set default value if is null.
  if (is_null($default)) {
    $default = '';
  }

  // Return empty string if null or the value.
  return !isset($value) || $value == null ? $default : $value;
}

add_shortcode('ptb_field', 'ptb_field_shortcode');

/**
 * Echo the property value for property on a page.
 *
 * @param int $post_id
 * @param string $name
 * @param mixed $default
 * @param string $lang
 * @param string $old_name
 * @since 1.0.0
 */

function the_ptb_field ($post_id = null, $name = null, $default = null, $lang = null, $old_name = null) {
  $value = ptb_field($post_id, $name, $default, $lang, $old_name);

  if (is_array($value)) {
    $value = @implode(',', $value);
  }

  echo $value;
}