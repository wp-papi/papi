<?php

/**
 * Page Type Builder Admin Functions.
 *
 * @package PageTypeBuilder
 * @version 1.0.0
 */

// Exit if accessed directly
if (!defined('ABSPATH')) exit;

/**
 * Get option view path.
 *
 * @param string $view
 * @since 1.0.0
 *
 * @return string
 */

function _ptb_get_options_view_path ($view, $page_type = '') {
  $path = '?page=page-type-builder&view=' . $view;

  if (!empty($page_type)) {
    $path .= '&page_type=' . $page_type;
  }

  return $path;
}