<?php

/**
 * Get post or page id from a object.
 *
 * @param object $post_id
 * @since 1.0
 * @access private
 *
 * @return int
 */

function _ptb_get_post_id ($post_id = null) {
  if (is_object($post_id)) {
    return $post->ID;
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
 * Get page view from query string.
 *
 * @since 1.0
 * @access private
 *
 * @return string|null
 */

function _ptb_get_page_view () {
  if (isset($_GET['page']) && strpos($_GET['page'], 'ptb') !== false) {
    $page = str_replace('ptb-', '', $_GET['page']);
    return preg_replace('/\,.*/', '', $page);
  }

  return null;
}

/**
 * Get page type from query string or the database.
 *
 * @param int $post_id
 * @since 1.0
 * @access private
 *
 * @return string|null
 */

function _ptb_get_page_page_type ($post_id = null) {
  if (isset($_GET['page_type']) && !empty($_GET['page_type'])) {
    return $_GET['page_type'];
  }

  if (is_null($post_id)) {
    $post_id = _ptb_get_post_id();
  }

  if (!is_null($post_id)) {
    $meta = get_post_meta($post_id, PTB_META_KEY, true);
    if (isset($meta) && !empty($meta) && isset($meta['ptb_page_type'])) {
      return $meta['ptb_page_type'];
    }
  }

  return null;
}

/**
 * Slugify the given string.
 *
 * @param string $str
 * @param array $replace
 * @param string $delimiter
 * @access private
 *
 * @return string
 */

function _ptb_slugify ($str, $replace = array(), $delimiter = '-') {
  setlocale(LC_ALL, 'en_US.UTF8');
  if(!empty($replace)) {
    $str = str_replace((array)$replace, ' ', $str);
  }
  $clean = iconv('UTF-8', 'ASCII//TRANSLIT', $str);
  $clean = preg_replace("/[^a-zA-Z0-9\/_|+ -]/", '', $clean);
  $clean = strtolower(trim($clean, '-'));
  $clean = preg_replace("/[\/_|+ -]+/", $delimiter, $clean);
  return trim($clean);
}

/**
 * Add `ptb_` to the given string ad the start of the string.
 *
 * @param string $str
 * @since 1.0
 * @access private
 *
 * @return string
 */

function _ptbify ($str) {
  if (!preg_match('/^ptb\_/', $str) && !_ptb_is_random_title($str)) {
    return  'ptb_' . $str;
  }

  return $str;
}

/**
 * Underscorify the given string.
 * Replacing whitespace and dash with a underscore.
 *
 * @param string $str
 * @since 1.0
 * @access private
 *
 * @return string
 */

function _ptb_underscorify ($str) {
  return str_replace(' ', '_', str_replace('-', '_', $str));
}

/**
 * Dashify the given string.
 * Replacing whitespace and underscore with a dash.
 *
 * @param string $str
 * @since 1.0
 * @access private
 *
 * @return string
 */

function _ptb_dashify ($str) {
  return str_replace(' ', '-', str_replace('_', '-', $str));
}

/**
 * Remove `ptb-` or `ptb_` from the given string.
 *
 * @param string $str
 * @since 1.0
 * @access private
 *
 * @return string
 */

function _ptb_remove_ptb ($str) {
  return str_replace('ptb-', '', str_replace('ptb_', '', $str));
}

/**
 * Get meta value direct from the database without any converting or so.
 *
 * @param int $post_id
 * @param string $name
 * @param mixed $default Default is null.
 * @since 1.0
 *
 * @return mixed
 */

function _ptb_meta_value ($post_id = null, $name = null, $default = null) {
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

  $meta = get_post_meta($post_id, PTB_META_KEY, true);

  if (isset($meta[$name])) {
    return $meta[$name];
  }

  return $default;
}

/**
 * Get property value for property on a page.
 *
 * @param int $post_id
 * @param string $name
 * @param mixed $default Default is null.
 * @since 1.0
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
    return null;
  }

  if (isset($page->$name)) {
    $value = $page->$name;
    if (is_array($value) && isset($value[$name])) {
      return $value[$name];
    }
    return $value;
  }
}

/**
 * Get the page.
 *
 * @param int $post_id The post id.
 * @since 1.0
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
 * @since 1.0
 *
 * @return PTB_Page|null
 */

function current_page () {
  return ptb_get_page();
}

/**
 * Get all page types that exists.
 *
 * @since 1.0
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
 * @since 1.0
 * @access private
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
 * @since 1.0
 * @access private
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
 * Get data from page type file.
 *
 * @param int|string $post_id Post id or page type
 * @since 1.0
 * @access private
 *
 * @return null|object
 */

function _ptb_get_file_data ($post_id) {
  if (is_null($post_id) || is_numeric($post_id)) {
    $post_id = _ptb_get_post_id($post_id);
    $page_type = _ptb_get_page_page_type($post_id);
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
 * Get template file from page type.
 *
 * @param int|string $post_id Post id or page type
 * @since 1.0
 * @access private
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
 * Generate random title for property.
 *
 * @since 1.0
 * @access private
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
 * @access private
 *
 * @return bool
 */

function _ptb_is_random_title ($str = '') {
  return preg_match('/^' . PTB_RANDOM_KEY . '/', $str);
}

/**
 * Check if it's ends with '_property'.
 *
 * @param string $str
 * @since 1.0
 * @access private
 *
 * @return bool
 */

function _ptb_is_property_key ($str = '') {
  $pattern = PTB_PROPERTY_TYPE_KEY;
  $pattern = str_replace('_', '\_', $pattern);
  $pattern = str_replace('-', '\-', $pattern);
  $pattern = '/' . $pattern . '$/';
  return preg_match($pattern, $str);
}

/**
 * Get the right key for a property type.
 *
 * @param string $str
 * @since 1.0
 * @access private
 *
 * @return string
 */

function _ptb_property_type_key ($str = '') {
  return $str . PTB_PROPERTY_TYPE_KEY;
}

/**
 * Returns only values in the array and removes `{x}_property` key and value.
 *
 * @param array $a
 * @since 1.0
 * @access private
 *
 * @return array
 */

function _ptb_get_only_values ($a = array()) {
  foreach ($a as $key => $value) {
    if (_ptb_is_property_key($key)) {
      unset($a[$key]);
    }
  }
  return $a;
}

/**
 * Get a php friendly name.
 *
 * @param string $name
 * @since 1.0
 * @access private
 *
 * @return string
 */

function _ptb_name ($name) {
  return _ptb_underscorify(_ptb_slugify(_ptbify($name)));
}

/**
 * Check what the request method is.
 *
 * @param string $method
 * @since 1.0
 * @access private
 *
 * @return bool
 */

function _ptb_is_method ($method = '') {
  return strtoupper($_SERVER ['REQUEST_METHOD']) == strtoupper($method);
}

/**
 * Get the url to 'post-new.php' with query string of the page type to load.
 *
 * @param string $page_type
 * @param string $post_type
 * @param bool $append_admin_url Default true
 * @since 1.0
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
 * @since 1.0
 *
 * @return bool
 */

function _ptb_is_page_type_allowed ($post_type) {
  $post_types = array_map(function ($p) {
    return strtolower($p);
  }, page_type_builder()->core->post_types);
  return in_array(strtolower($post_type), $post_types);
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

/**
 * Get all registered directories.
 *
 * @param string $find
 *
 * @return array
 */

function _ptb_get_ptb_directories ($find = '') {
  global $ptb_directories;

  if (empty($ptb_directories) || !is_array($ptb_directories)) {
    return array();
  }

  $result = array();

  foreach ($ptb_directories as $directory) {
    $dirs = @scandir($directory);
    $dirs = array_diff($dirs, array('..', '.'));
    foreach ($dirs as $dir) {
      if ($dir == $find) {
        $result[] = $directory . '/' . $dir;
      }
    }
  }

  return $result;
}

/**
 * Get all files in the given directory.
 * Looking down 3 levels.
 *
 * @param string $directory
 * @param string $file
 * @param bool $first
 *
 * @return array
 */

function _ptb_get_files_in_directory ($directory = '', $find_file = '', $first = false) {
  // Can't proceed empty directory.
  if (empty($directory)) {
    return array();
  }

  // Get all directories that are registered.
  $dirs = _ptb_get_ptb_directories($directory);
  $result = array();

  // Loop through all directories.
  foreach ($dirs as $dir) {
    $files = @scandir($dir);
    $files = array_diff($files, array('..', '.'));
    foreach ($files as $file) {
      $path = $dir . '/' . $file;
      if (is_dir($path)) {
        $subfiles = @scandir($dir . '/' . $file);
        $subfiles = array_diff($subfiles, array('..', '.'));
        foreach ($subfiles as $subfile) {
          $path = $dir . '/' . $file . '/' . $subfile;
          if (is_dir($path)) {
            $subsubfiles = @scandir($dir . '/' . $file . '/' . $subfile);
            $subsubfiles = array_diff($subsubfiles, array('..', '.'));
            foreach ($subsubfiles as $subsubfile) {
              $result[] = $dir . '/' . $file . '/' . $subfile . '/' . $subsubfile;
            }
          } else {
            $result[] = $dir . '/' . $file . '/' . $subfile;
          }
        }
      } else {
        $result[] = $path;
      }
    }
  }

  if (!empty($find_file)) {
    if (pathinfo($find_file, PATHINFO_EXTENSION) == null) {
      $find_file = $find_file . '.php';
    }

    $result = array_filter($result, function ($f) use ($find_file) {
      return basename($f) == basename($find_file);
    });

    $result = array_values($result);

    if ($first) {
      return !empty($result) ? reset($result) : '';
    } else {
      return $result;
    }
  } else {
    return $result;
  }
}

/**
 * Get class name from page type file.
 *
 * @param string $file
 * @since 1.0
 *
 * @return string|null
 */

function _ptb_get_class_name ($file) {
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
 * Get Page Type Builder settings.
 *
 * @since 1.0
 *
 * @return array
 */

function _ptb_get_settings () {
  return page_type_builder()->core->get_settings();
}