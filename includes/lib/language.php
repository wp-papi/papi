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
 * Check if lang code exists or not.
 *
 * @param string $lang
 *
 * @return bool
 */

function _ptb_lang_exist ($lang = '') {
  $lang = new PTB_Language($lang);
  return $lang->exist();
}

/**
 * Get default language code.
 *
 * @return string
 */

function _ptb_lang_default () {
  if (defined('PTB_POLYLANG') && PTB_POLYLANG) {
    return pll_default_language();
  } else if (defined('PTB_LANG_DEFAULT') && _ptb_lang_exist(PTB_LANG_DEFAULT)) {
    return strtolower(PTB_LANG_DEFAULT);
  } else {
    return 'en';
  }
}


/**
 * Get current language code. Should follow ISO 639-1.
 *
 * @link http://en.wikipedia.org/wiki/List_of_ISO_639-1_codes
 *
 * @return string
 */

function _ptb_get_lang_code () {
  if (defined('PTB_POLYLANG') && PTB_POLYLANG) {
    $lang_code = pll_current_language();
    if ($lang_code === false) {
      $lang_code = _ptb_lang_default();
    }
  } else if (isset($_GET['lang']) && _ptb_lang_exist($_GET['lang'])) {
    $lang_code = $_GET['lang'];
  } else {
    $lang_code = _ptb_lang_default();
  }

  $lang = new PTB_Language($lang_code);
  return $lang->get_lang_code();
}

/**
 * Get field slug from with right language code.
 *
 * Format: `{lang_code}_{field_slug}`
 *
 * @param string $slug
 * @param string $lang
 *
 * @return string
 */

function _ptb_get_lang_field_slug ($slug = '', $lang = '') {
  $lang = _ptb_lang_exist($lang) ? $lang : _ptb_get_lang_code();

  if (!preg_match('/^(\_|)(ptb|)[a-z]{2}\_/', $slug)) {
    return $lang . '_' . $slug;
  }

  return $slug;
}

/**
 * Remove language code from field slug if exists.
 *
 * @param string $slug
 * @param string $lang
 *
 * @return string
 */

function _ptb_remove_lang_field_slug ($slug = '', $lang = '') {
  $slug = _ptb_remove_ptb($slug);

  if (_ptb_lang_exist($lang)) {
    return substr($slug, 3);
  }

  $pattern = '/^([a-z]{2})\_/';
  preg_match($pattern, $slug, $matches);

  if (!empty($matches) && is_array($matches)) {
    $code = $matches[0];
    $lang = new PTB_Language($code);
    return $lang->exist() ? substr($slug, 3) : $slug;
  }

  return $slug;
}