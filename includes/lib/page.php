<?php

/**
 * Page Type Builder Page functions.
 *
 * @package PageTypeBuilder
 * @version 1.0.0
 */

// Exit if accessed directly
if (!defined('ABSPATH')) exit;

/**
 * Get page type meta key.
 *
 * @since 1.0.0
 *
 * @return string
 */

function _ptb_get_page_type_meta_key () {
  return '__ptb_page_type';
}

/**
 * Get page type meta value.
 *
 * @param int $post_id
 * @since 1.0.0
 *
 * @return string
 */

function _ptb_get_page_type_meta_value ($post_id = null) {
  if (is_null($post_id)) {
    $post_id = _ptb_get_post_id();
  }

  $page_type = '';

  // Get page type value from database.
  $key = _ptb_get_page_type_meta_key();

  if (!is_null($post_id)) {
    $meta_value = get_post_meta($post_id, $key, true);
    $page_type = _ptb_h($meta_value, '');
  }

  // Get page type value from get object.
  if (empty($page_type) && isset($_GET['page_type'])) {
    $page_type = $_GET['page_type'];
  }

  // Get page type value from post object.
  if (empty($page_type) && isset($_POST['ptb_page_type'])) {
    $page_type = $_POST['ptb_page_type'];
  }

  // Load right page type when Polylang is in use.
  if (empty($page_type) && _ptb_polylang()) {
    $from_post = _ptb_get_qs('from_post');
    if (!is_null($from_post) && is_numeric($from_post)) {
      $meta_value = get_post_meta(intval($from_post), $key, true);
      $page_type = _ptb_h($meta_value, '');
    }
  }

  return $page_type;
}

/**
 * Get the url to 'post-new.php' with query string of the page type to load.
 *
 * @param string $page_type
 * @param string $post_type
 * @param bool $append_admin_url Default true
 * @since 1.0.0
 *
 * @return string
 */

function _ptb_get_page_new_url ($page_type, $post_type, $append_admin_url = true) {
  $admin_url = $append_admin_url ? get_admin_url () : '';
  return $admin_url . 'post-new.php?post_type=' . $post_type . '&page_type=' . $page_type;
}

/**
 * Check if page type is allowed to use.
 *
 * @param string $post_type
 * @since 1.0.0
 *
 * @return bool
 */

function _ptb_is_page_type_allowed ($post_type) {
  $post_types = array_map(function ($p) {
    return strtolower($p);
  }, _ptb_get_post_types());
  return in_array(strtolower($post_type), $post_types);
}

/**
 * Get a page type by file path.
 *
 * @param string $file_path
 * @since 1.0.0
 *
 * @return string
 */

function _ptb_get_page_type ($file_path) {
 $page_type = new PTB_Page_Type($file_path);

 // If the page type don't have a name we can't use it.
 if (!$page_type->has_name()) {
   return null;
 }

 return $page_type;
}

/**
 * Get all page types that exists.
 *
 * @since 1.0.0
 * @todo rewrite and use logic in class-ptb-page-types.php
 * @param bool $all Default false
 *
 * @return array
 */

function _ptb_get_all_page_types ($all = false) {
  // Get all page types files.
  $files = _ptb_get_all_page_type_files();

  // Get the right WordPress post type.
  $post_type = _ptb_get_wp_post_type();

  $page_types = array();

  foreach ($files as $file) {
    $p = _ptb_get_page_type($file);

    // Add the page type if the post types is allowed.
    if (!is_null($p) && ($all || in_array($post_type, $p->post_types))) {
      $page_types[] = $p;
    }
  }

  // Sort by name.
  usort($page_types, function ($a, $b) {
    return strcmp($a->name, $b->name);
  });

  return $page_types;
}

/**
 * Get template file from post id.
 *
 * @param int|string $post_id
 * @since 1.0.0
 *
 * @return null|string
 */

function _ptb_get_template ($post_id) {
  $data = _ptb_get_file_data($post_id);

  if (isset($data) && isset($data->template) && isset($data->template)) {
    return $data->template;
  } else {
    return null;
  }
}

/**
 * Get data from page type file.
 *
 * @param int|string $post_id Post id or page type
 * @since 1.0.0
 *
 * @return null|object
 */

function _ptb_get_file_data ($post_id) {
  $post_id = _ptb_get_post_id($post_id);
  $page_type = _ptb_get_page_type_meta_value($post_id);

  // Check so the page type isn't null or empty before we
  // trying to get the page type data.
  if (!is_null($page_type) && !empty($page_type)) {
    $file = _ptb_get_page_type_file($page_type);
    return _ptb_get_page_type($file);
  }

  return null;
}

/**
 * Get the page.
 *
 * @param int $post_id The post id.
 * @since 1.0.0
 *
 * @return PTB_Page|null
 */

function ptb_get_page ($post_id = null) {
  $post_id = _ptb_get_post_id($post_id);
  $page = new PTB_Page($post_id);

  if (!$page->has_post()) {
    return null;
  }

  return $page;
}

/**
 * Get the current page. Like in EPiServer.
 *
 * @since 1.0.0
 *
 * @return PTB_Page|null
 */

function current_page () {
  return ptb_get_page();
}

/**
 * Get number of how many pages uses the given page type.
 *
 * @since 1.0.0
 *
 * @return int
 */

function _ptb_get_number_of_pages ($page_type) {
  global $wpdb;

  if (is_null($page_type) || empty($page_type)) {
    return 0;
  }

  $query = "SELECT COUNT(*) FROM wp_postmeta WHERE `meta_key` = '__ptb_page_type' AND `meta_value` = '$page_type'";

  return intval($wpdb->get_var($query));
}

/**
 * Get all post types Page Type Builder should work with.
 *
 * @since 1.0.0
 *
 * @return array
 */

function _ptb_get_post_types () {
  $page_types = _ptb_get_all_page_types(true);
  $post_types = array();

  foreach ($page_types as $page_type) {
    $post_types = array_merge($post_types, $page_type->post_types);
  }

  return array_unique($post_types);
}

/**
 * The default page type thumbnail.
 *
 * @since 1.0.0
 *
 * @return string
 */

function _ptb_page_type_default_thumbnail () {
  return get_template_directory_uri() . '/screenshot.png';
}