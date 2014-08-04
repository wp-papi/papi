<?php

/**
 * Page Type Builder Property functions.
 *
 * @package PageTypeBuilder
 * @version 1.0.0
 */

// Exit if accessed directly
if (!defined('ABSPATH')) exit;

/**
 * Check if it's ends with '_property'.
 *
 * @param string $str
 * @since 1.0
 *
 * @return bool
 */

function _ptb_is_property_type_key ($str = '') {
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

function _ptb_property_type_key ($str = '') {
  return $str . PTB_PROPERTY_TYPE_KEY;
}

/**
 * Get property key.
 *
 * @param string $str
 * @since 1.0.0
 *
 * @return string
 */

function _ptb_property_key ($str) {
  return _ptb_f(_ptbify($str));
}

/**
 * Returns only values in the array and removes `{x}_property` key and value.
 *
 * @param array $a
 * @since 1.0
 *
 * @return array
 */

function _ptb_get_only_property_values ($a = array()) {
  foreach ($a as $key => $value) {
    if (_ptb_is_property_type_key($key)) {
      unset($a[$key]);
    }
  }

  return $a;
}

/**
 * Get property type by the given type.
 *
 * @param string $type
 * @since 1.0.0
 *
 * @return object|null
 */

function _ptb_get_property_type ($type) {
  if (is_object($type) && isset($type->type) && is_string($type->type)) {
    $type = $type->type;
  }

  if (empty($type)) {
    return;
  }

  return PTB_Property::factory($type);
}

/**
 * Get property lang.
 *
 * @param object $options
 * @since 1.0.0
 *
 * @return string
 */

function _ptb_get_property_lang ($options = null) {
  $rest = array();

  if (!isset($options)) {
    $options = new stdClass;
  }

  // Fetch all languages from Polylang that is available.
  if (empty($options->lang)) {
    if (_ptb_polylang()) {
      $options->lang = pll_languages_list();
    } else {
      $options->lang = _ptb_get_lang_code();
    }
  }

  if (is_array($options->lang)) {
    // If we have a array and Polylang is supported we can get the right lang.
    if (_ptb_polylang()) {
      $lang = _ptb_get_lang_code();
      if (in_array($lang, $options->lang)) {
        $rest = array_diff($options->lang, array($lang));
        $options->lang = $lang;
      }
    } else {
      // Can't handle multilanguage without Polylang.
      $lang = array_shift($options->lang);
      $rest = $options->lang;
      $options->lang = $lang;
    }
  }

  return array($options->lang, $rest);
}

/**
 * Get property options.
 *
 * @param array $options
 * @since 1.0.0
 *
 * @return object
 */

function _ptb_get_property_options ($options) {
  $defaults = array(
    'title'         => '',
    'no_title'      => false,
    'disabled'      => false,
    'slug'          => '',
    'settings'      => array(),
    'sort_order'    => null,
    'value'         => '',
    'type'          => '',
    'colspan'       => '',
    'lang'          => '',
    'old_slug'      => '',
    'default'       => '',
    'capabilities' => array()
  );

  $options = array_merge($defaults, $options);
  $options = (object)$options;

  if ($options->no_title) {
    $options->title = '';
    $options->colspan = 2;
  }

  if (empty($options->slug)) {
    $options->slug = _ptb_slugify($options->title);
  }

  if (!empty($options->old_slug)) {
    $options->old_slug = _ptb_name($options->old_slug);
  }

  // Generate colspan attribute
  if (!empty($options->colspan)) {
    $options->colspan = _ptb_attribute('colspan', $options->colspan);
  }

  // Get property language.
  list($options->lang, $rest) = _ptb_get_property_lang($options);

  // Add language code to the slug name.
  $options->slug = _ptb_get_lang_field_slug($options->slug, $options->lang);

  // Generate a vaild Page Type Builder meta name.
  $options->slug = _ptb_name($options->slug);

  // Get meta value for the field
  $options->value = ptb_field($options->slug, null, null, $options->lang, $options->old_slug);

  // Add default value if database value is empty.
  if (empty($options->value)) {
    $options->value = $options->default;
  }

  $opt = $options;

  // Only render more properties when we don't use Polylang but want different fields on the page.
  if (!_ptb_polylang() && !empty($rest)) {
    $options->title = '(' . strtoupper($options->lang) . ') ' . $options->title;
    $opt = array($options);
    foreach ($rest as $lang) {
      $o = clone $options;

      // Update title.
      $o->title = '(' . strtoupper($lang) . ') ' . substr($o->title, 4);

      // Add language code to the slug name.
      $o->slug = _ptb_get_lang_field_slug($o->slug, $lang);

      // Generate a vaild Page Type Builder meta name.
      $o->slug = _ptb_name($o->slug);

      // Get meta value for the field
      $o->value = ptb_field($o->slug, null, null, $lang, $o->old_slug);

      $opt[] = $o;
    }
  }

  return $opt;
}

/**
 * Render a property the right way.
 *
 * @param object $property
 * @since 1.0.0
 */

function _ptb_render_property ($property) {
  // Check so type isn't empty and capabilities on the property.
  if (empty($property->type) || !_ptb_current_user_is_allowed($property->capabilities)) {
    return;
  }

  $property_type = _ptb_get_property_type($property->type);

  if (is_null($property_type)) {
    return;
  }

  $property_type->set_options($property);

  // Only render if it's the right language if the definition exist.
  if (_ptb_get_qs('lang') != null && !_ptb_polylang_all()) {
    $render = _ptb_lang_exist(_ptb_get_qs('lang')) && $property->lang === strtolower(_ptb_get_qs('lang'));
  } else {
    $render = true;
  }

  // Render the property.
  if ($render) {
    $property_type->assets();
    $property_type->render();
    $property_type->hidden();
  }
}

/**
 * Render properties the right way.
 *
 * @param array $properties
 * @since 1.0.0
 */

function _ptb_render_properties ($properties) {
  // Don't proceed without any properties
  if (!is_array($properties) || empty($properties)) {
    return;
  }

  // If it's a tab the tabs class will
  // handle the rendering of the properties.
  if (isset($properties[0]->tab) && $properties[0]->tab) {
    new PTB_Admin_Meta_Box_Tabs($properties);
  } else {
    // Sort properties based on `sort_order` value.
    $properties = _ptb_sort_order($properties);

    echo '<table class="ptb-table">';
      echo '<tbody>';

    foreach ($properties as $property) {
      _ptb_render_property($property);
    }

      echo '</tbody>';
    echo '</table>';
  }
}