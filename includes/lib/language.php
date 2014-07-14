<?php

/**
 * Page Type Builder Language Functions.
 *
 * @package PageTypeBuilder
 * @version 1.0.0
 */

// Exit if accessed directly
if (!defined('ABSPATH')) exit;

/**
 * Get current language code. Should follow ISO 639-1.
 *
 * @link http://en.wikipedia.org/wiki/List_of_ISO_639-1_codes
 *
 * @return string
 */

function ptb_get_lang_code () {
  if (!defined('PTB_LANG_CODE') && !is_string(PTB_LANG_CODE) && count(PTB_LANG_CODE) > 2) {
    return PTB_Language::$default;
  }

  $lang = new PTB_Language(PTB_LANG_CODE);

  if (!$lang->exists()) {
    return PTB_Language::$default;
  }

  return $lang->get_lang_code();
}

/**
 * Get field slug from with right language code.
 *
 * Format: `{lang_code}_{field_slug}`
 *
 * @param string $slug
 *
 * @return string
 */

function _ptb_get_lang_field_slug ($slug = '') {
  $lang = ptb_get_lang_code();
  return $lang . '_' . $slug;
}

/**
 * Remove language code from field slug if exists.
 *
 * @param string $slug
 *
 * @return string
 */

function _ptb_remove_lang_field_slug ($slug = '') {
 $pattern = '/^([a-z]{2})\_/';
 preg_match($pattern, $slug, $matches);

 if (!empty($matches) && is_array($matches)) {
   $code = $matches[0];
   $lang = new PTB_Language($code);
   return $lang->exists() ? substr($slug, 3) : $slug;
 }

 return $slug;
}