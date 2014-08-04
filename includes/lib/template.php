<?php

/**
 * Page Type Builder Template functions.
 *
 * @package PageTypeBuilder
 * @version 1.0.0
 */

// Exit if accessed directly
if (!defined('ABSPATH')) exit;

/**
 * Include template files from Page Type Builders custom page template meta field.
 * WordPress build in isn't so good when it comes to changing it without having a
 * real page template.
 *
 * @param string $original_template
 * @since 1.0.0
 *
 * @return string
 */

function _ptb_template_include ($original_template) {
  global $post;

  if (!isset($post) || !isset($post->ID)) {
    return $original_template;
  }

  $page_template = get_post_meta($post->ID, '__ptb_page_template', true);

  if (!is_null($page_template) && !empty($page_template)) {
    $path = get_template_directory();
    $path = trailingslashit($path);
    $file = $path . $page_template;

    if (file_exists($file) && !is_dir($file)) {
      return $file;
    }
  }

  return $original_template;
}

add_filter('template_include', '_ptb_template_include');