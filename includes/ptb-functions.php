<?php

/**
 * Get post or page id from a object.
 *
 * @param object $post_id
 * @since 1.0
 *
 * @return int
 */

function get_ptb_post_id ($post_id = null) {
  if (is_object($post_id)) {
    return $post->ID;
  }

  if (is_null($post_id) && get_post()) {
    return get_the_ID();
  }

  if (is_null($post_id) && isset($_GET['post'])) {
    return $_GET['post'];
  }

  if (is_null($post_id) && isset($_GET['page_id'])) {
    return $_GET['page_id'];
  }
  
  return null;
}

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
 * Ptbify the given string.
 *
 * @param string $str
 * @since 1.0
 *
 * @return string
 */

function ptbify ($str) {
  if (!preg_match('/^ptb\_/', $str)) {
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
 * Get page type for post id or post object.
 *
 * @param object|int $post_id
 * @since 1.0
 *
 * @return string|null
 */

function ptb_get_page_type ($post_id = null) {
  $post_id = get_ptb_post_id($post_id);

  $meta = get_post_meta($post_id, PTB_META_KEY, true);

  if (is_array($meta) && isset($meta['ptb_page_type'])) {
    return $meta['ptb_page_type'];
  }

  return null;
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
    $post_id = get_ptb_post_id();
  }
  $post_id = get_ptb_post_id($post_id);
  return get_post_meta($post_id, PTB_META_KEY, true);
}

/**
 * Get property value for property on a post.
 *
 * @param object|int $post_id
 * @param string $property
 * @param mixed $default Default is null.
 * @since 1.0
 *
 * @return mixed
 */

function ptb_get_property_value ($post_id, $property = null, $default = null) {
  if (!isset($property)) {
    $property = $post_id;
    $post_id = get_ptb_post_id();
  }

  $properties = ptb_get_properties($post_id);
  $property = ptb_underscorify(ptbify($property));

  if (is_array($properties) && isset($properties[$property])) {
    $property = $properties[$property];
    if (is_array($property)) {
      return ptb_convert_property_value($property);
    }
    return $property;
  }

  return $default;
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
    $type = ptb_property_type_format($property['type']);
    $property_type = PTB_Property::factory($type);
    return $property_type->convert($property['value']);
  }
}

/**
 * Get the current page. Like in EPiServer.
 *
 * @param bool $array Return as array instead of object
 * @since 1.0
 *
 * @return object|array
 */

function current_page ($array = false) {
  $post_id = get_ptb_post_id();
  $post = get_post($post_id, ARRAY_A);
  $post_meta = get_post_meta($post_id, PTB_META_KEY, true);

  if (is_array($post_meta)) {
    foreach ($post_meta as $key => $value) {
      if (is_array($value)) {
        $value = ptb_convert_property_value($value);
      }
      $post[ptb_remove_ptb($key)] = $value;
    }
    return $array ? $post : (object)$post;
  }

  return null;
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
  $page_type = 'page_type';

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
 * @return object
 */

function ptb_get_page_type_from_file ($file) {
  $class_name = get_ptb_class_name($file);
  require_once($file);
  return (object)array(
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
 * Get template file from page type.
 *
 * @param int|string $post_id Post id or page type
 * @since 1.0
 *
 * @return string
 */

function ptb_get_template ($post_id) {
  if (is_null($post_id) || is_numeric($post_id)) {
    $post_id = get_ptb_post_id($post_id);
    $page_type = ptb_get_page_type($post_id);
  } else {
    $page_type = $post_id;
  }
  $file = ptb_get_page_type_file($page_type);
  $data = ptb_get_page_type_from_file($file);
  return $data->page_type->template;
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
 * Make sure we have the right format of the property type.
 *
 * Example:
 *  'propertystring' => 'PropertyString'
 *
 * @param string $type
 * @since 1.0
 * @throws Exception
 *
 * @return string
 */

function ptb_property_type_format ($type) {
  $type = strtolower($type);
  var_dump($type);
  $type = str_replace('property', '', $type);
  $type = ucfirst($type);
  $type = 'Property' . $type;
  if (!preg_match('/Property\w+/', $type)) {
      throw new Exception('Wrong format of the Page Type Builder property: ' . $type);
  }
  return $type;
}