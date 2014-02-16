<?php

/**
 * Get the url to 'post-new.php' with query string of the page type to load.
 *
 * @param string $page_type
 * @since 1.0
 *
 * @return string
 */

function get_ptb_page_new_url ($page_type) {
  return get_admin_url() . 'post-new.php?post_type=page&page_type=' . $page_type;
}

/**
 * Get page view from query string.
 *
 * @since 1.0
 *
 * @return string|null
 */

function get_ptb_page_view () {
  if (isset($_GET['page']) && strpos($_GET['page'], 'ptb') !== false) {
    return str_replace('ptb-', '', $_GET['page']);
  }

  return null;
}

/**
 * Get page type from query string.
 *
 * @since 1.0
 *
 * @return string|null
 */

function get_ptb_page_type () {
  if (isset($_GET['page_type']) && !empty($_GET['page_type'])) {
    return $_GET['page_type'];
  }

  return null;
}

/**
 * Get class name from php file.
 *
 * @param string $file
 * @since 1.0
 *
 * @return string|null
 */

function get_ptb_class_name ($file) {
  header('Content-Type: text/plain');
  $content = file_get_contents($file);
  $tokens = token_get_all($content);
  $class_token = false;
  $class_name = null;

  foreach ($tokens as $token) {
    if (is_array($token)) {
      if ($token[0] === T_CLASS) {
        $class_token = true;
      } else if ($class_token && $token[0] === T_STRING) {
        $class_name = $token[1];
        $class_token = false;
      }
    }
  }

  return $class_name;
}

/**
 * Slugify the given string.
 *
 * @param string $str
 *
 * @return string
 */

function ptb_slugify ($str) {
  $str = iconv('UTF-8', 'ASCII//TRANSLIT', $str);
  $str = strtolower($str);
  $str = preg_replace("/\W/", '-', $str);
  $str = preg_replace("/-+/", '-', $str);
  return trim($str, '-');
}

/**
 * Underscorify the given string.
 * Replacing whitespace and dash with a underscore.
 *
 * @param string $str
 *
 * @return string
 */

function ptb_underscorify ($str) {
  return str_replace(' ', '_', str_replace('-', '_', $str));
}