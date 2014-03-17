<?php

/**
 * Get post or page id from a object.
 *
 * @param object $post_id
 * @since 1.0
 *
 * @return int
 */

function ptb_get_post_id ($post_id = null) {
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
 * Get the url to 'post-new.php' with query string of the page type to load.
 *
 * @param string $page_type
 * @since 1.0
 *
 * @return string
 */

function ptb_get_page_new_url ($page_type) {
  return get_admin_url() . 'post-new.php?post_type=page&page_type=' . $page_type;
}

/**
 * Get page view from query string.
 *
 * @since 1.0
 *
 * @return string|null
 */

function ptb_get_page_view () {
  if (isset($_GET['page']) && strpos($_GET['page'], 'ptb') !== false) {
    return str_replace('ptb-', '', $_GET['page']);
  }

  return null;
}

/**
 * Get page type from query string.
 *
 * @param int $post_id
 * @since 1.0
 *
 * @return string|null
 */

function ptb_get_page_type ($post_id = null) {
  if (isset($_GET['page_type']) && !empty($_GET['page_type'])) {
    return $_GET['page_type'];
  }
  
  if (is_null($post_id)) {
    $post_id = ptb_get_post_id();
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
 * Get class name from php file.
 *
 * @param string $file
 * @since 1.0
 *
 * @return string|null
 */

function ptb_get_class_name ($file) {
  // header('Content-Type: text/plain');
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
 * @param array $replace
 * @param string $delimiter
 *
 * @return string
 */

function ptb_slugify ($str, $replace = array(), $delimiter = '-') {
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
 * Ptbify the given string.
 *
 * @param string $str
 * @since 1.0
 *
 * @return string
 */

function ptbify ($str) {
  if (!preg_match('/^ptb\_/', $str) && !ptb_is_random_title($str)) {
    return  'ptb_' . $str;
  }

  return $str;
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

/**
 * Dashify the given string.
 * Replacing whitespace and underscore with a dash.
 *
 * @param string $str
 *
 * @return string
 */

function ptb_dashify ($str) {
  return str_replace(' ', '-', str_replace('_', '-', $str));
}

/**
 * Remove `ptb-` or `ptb_` from the given string.
 *
 * @param string $str
 * @since 1.0
 *
 * @return string
 */

function ptb_remove_ptb ($str) {
  return str_replace('ptb-', '', str_replace('ptb_', '', $str));
}

/**
 * Get properties array for page.
 *
 * @param object|int $post_id
 * @since 1.0
 *
 * @return array|null
 */

function ptb_get_properties ($post_id = null) {
  if (!isset($post_id)) {
    $post_id = ptb_get_post_id();
  }
  $post_id = ptb_get_post_id($post_id);
  return get_post_meta($post_id, PTB_META_KEY, true);
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
  if (!is_numeric($post_id)) && is_string($post_id)) {
    $name = $post_id;
    $post_id = null;
  }
  
  if (is_numeric($post_id)) {
    $post_id = intval($post_id);
  }
  
  if (is_null($name)) {
    return $default;
  }
  
  $page = ptb_get_page($post_id);
  
  if (!$page->has_post()) {
    return null;
  }
  
  $meta = $page->get_meta();
  
  if (isset($meta[$name])) {
    $value = $meta[$name];
    if (is_array($value) && isset($value[$name])) {
      return $value[$name];
    }
    return $value;
  }
  
  /*
  
  // OLD CODE
  
  if (!isset($property)) {
    $name = $post_id;
    $post_id = ptb_get_post_id();
  }
  
  $properties = ptb_get_properties($post_id);
  $name = ptb_name($name);
  
  if (is_array($properties) && isset($properties[$name])) {
    $value = $properties[$name];
    if (is_array($value) && PTB_COLLECTION_KEY !== $name) {
      return ptb_convert_property_value($value);
    }
    return $value;
  }
  
  $name = ptb_remove_ptb($name);
  
  $collection = ptb_get_collection_values();
  if (!is_null($collection) && !empty($collection) && isset($collection[$name])) {
    return $collection[$name];
  }
  
  return $default;
  
  */
}

/**
 * Convert the property value to the right value that the property should have.
 *
 * @param array $property
 * @since 1.0
 *
 * @return mixed
 */

function ptb_convert_property_value (array $property = array()) {
  if (isset($property['value']) && isset($property['type'])) {
    $type = $property['type'];
    $property_type = PTB_Property::factory($type);
    return $property_type->convert($property['value']);
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
  $post_id = ptb_get_post_id($post_id);
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
 *
 * @return array
 */

function ptb_get_all_page_types () {
  $files = glob(PTB_PAGES_DIR . '*');
  $res = array();

  foreach ($files as $file) {
    $res[] = ptb_get_page_type_from_file($file);
  }

  return $res;
}

/**
 * Get page type object form file.
 *
 * @param string $file
 * @since 1.0
 *
 * @todo rewrite this. see Trello.
 *
 * @return object
 */

function ptb_get_page_type_from_file ($file) {
  $class_name = get_ptb_class_name($file);
  $page_type = 'page_type';
  require_once($file);
  return (object)array(
    'class_name' => $class_name,
    'file_name' => ptb_remove_ptb(basename($file, '.php')),
    'page_type' => (object)$class_name::$page_type
  );
}

/**
 * Get page type file from page type.
 *
 * @param string $page_type
 * @since 1.0
 *
 * @return string
 */

function ptb_get_page_type_file ($page_type) {
  return PTB_PAGES_DIR . ptb_dashify(ptbify($page_type)) . '.php';
}

/**
 * Get data from page type file.
 *
 * @param int|string $post_id Post id or page type
 * @since 1.0
 *
 * @return null|object
 */

function ptb_get_file_data ($post_id) {
  if (is_null($post_id) || is_numeric($post_id)) {
    $post_id = ptb_get_post_id($post_id);
    $page_type = ptb_get_page_type($post_id);
  } else {
    $page_type = $post_id;
  }
  if (!is_null($page_type) && !empty($page_type)) {
    $file = ptb_get_page_type_file($page_type);
    $data = ptb_get_page_type_from_file($file);
    return $data;
  } else {
    return null;
  }
}

/**
 * Get template file from page type.
 *
 * @param int|string $post_id Post id or page type
 * @since 1.0
 *
 * @return null|string
 */

function ptb_get_template ($post_id) {
  $data = ptb_get_file_data($post_id);
  if (isset($data) && isset($data->page_type) && isset($data->page_type->template)) {
    return $data->page_type->template;
  } else {
    return null;
  }
}

/**
 * Get html name value from the given string.
 *
 * @param string $name
 * @since 1.0
 *
 * @return string
 */

function ptb_get_html_name ($name) {
  return ptb_underscorify(ptbify($name));
}

/**
 * Generate random title for property.
 *
 * @since 1.0
 *
 * @return string
 */

function ptb_random_title () {
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

function ptb_is_random_title ($str = '') {
  return preg_match('/^' . PTB_RANDOM_KEY . '/', $str);
}

/**
 * Check if it's ends with '_property'.
 *
 * @param string $str
 * @since 1.0
 *
 * @return bool
 */

function ptb_is_property_key ($str = '') {
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
 *
 * @return string
 */

function ptb_property_type_key ($str = '') {
  return $str . PTB_PROPERTY_TYPE_KEY;
}

/**
 * Returns only values in the array and removes `{x}_property` key and value.
 *
 * @param array $a
 * @since 1.0
 *
 * @return array
 */

function ptb_get_only_values ($a = array()) {
  foreach ($a as $key => $value) {
    if (ptb_is_property_key($key)) {
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
 *
 * @return string
 */

function ptb_name ($name) {
  return ptb_underscorify(ptb_slugify(ptbify($name)));
}

/**
 * Check if we have a page type or not.
 *
 * @since 1.0
 *
 * @return bool
 */

function ptb_has_page_type () {
  // todo write this function.
  return false;
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