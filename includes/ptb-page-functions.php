<?php

/**
 * Page Type Builder Page Functions.
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

  $key = _ptb_get_page_type_meta_key();
  $page_type = h(get_post_meta($post_id, $key, true), '');

  if (empty($page_type) && isset($_GET['page_type']) && !empty($_GET['page_type'])) {
    $page_type = $_GET['page_type'];
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
 * @param int $post_id The post id.
 * @since 1.0.0
 *
 * @return PTB_Page|null
 */

function current_page () {
  return ptb_get_page();
}

/**
 * Get all page types that exists.
 *
 * @since 1.0.0
 * @todo rewrite and use logic in class-ptb-page-types.php
 *
 * @return array
 */

function _ptb_get_all_page_types () {
  $files = _ptb_get_files_in_directory('page-types');
  $post_type = _ptb_get_wp_post_type();
  $res = array();

  foreach ($files as $file) {
    $p = new PTB_Page_Type($file);

    if (!isset($p->post_types) || !is_array($p->post_types)) {
      $p->post_types = array('page');
    }

    if (in_array($post_type, $p->post_types)) {
      $res[] = $p;
    }
  }

  return $res;
}

/**
 * Get page type file from page type.
 *
 * @param string $page_type
 * @since 1.0.0
 *
 * @return null|string
 */

function _ptb_get_page_type_file ($page_type) {
  return _ptb_get_files_in_directory('page-types', _ptb_dashify(_ptbify($page_type)), true);
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

 if (!$page_type->has_name()) {
   return null;
 }

 return $page_type;
}

/**
 * Get template file from page type.
 *
 * @param int|string $post_id Post id or page type
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
  if (is_null($post_id) || is_numeric($post_id)) {
    $post_id = _ptb_get_post_id($post_id);
    $page_type = _ptb_get_page_type_meta_value($post_id);
  } else {
    $page_type = $post_id;
  }
  if (!is_null($page_type) && !empty($page_type)) {
    $file = _ptb_get_page_type_file($page_type);
    return _ptb_get_page_type($file);
  } else {
    return null;
  }
}

/**
 * Get property value for property on a page.
 *
 * @param int $post_id
 * @param string $name
 * @param mixed $default Default is null.
 * @since 1.0.0
 *
 * @return mixed
 */

function ptb_value ($post_id = null, $name = null, $default = null) {
  if (!is_numeric($post_id) && is_string($post_id)) {
    $name = $post_id;
    $post_id = null;
  }

  if (is_numeric($post_id)) {
    $post_id = intval($post_id);
  } else {
    $post_id = _ptb_get_post_id();
  }

  if (is_null($name)) {
    return $default;
  }

  $name = _ptb_remove_ptb($name);

  $page = ptb_get_page($post_id);

  if (is_null($page) || !$page->has_post()) {
    return $default;
  }

  $value = $page->get_value($name);

  if (is_null($value)) {
    return $default;
  }

  if (is_array($value) && isset($value[$name])) {
    return $value[$name];
  } else {
    return $value;
  }
}
