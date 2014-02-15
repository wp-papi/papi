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

function get_class_name ($file) {
  header('Content-Type: text/plain');
  $php_file = file_get_contents($file);
  $tokens = token_get_all($php_file);
  $class_token = false;
  $class_name = null;
  foreach ($tokens as $token) {
    if (is_array($token)) {
      if ($token[0] == T_CLASS) {
         $class_token = true;
      } else if ($class_token && $token[0] == T_STRING) {
        $class_name = $token[1];
         $class_token = false;
      }
    }
  }
  return $class_name;
}