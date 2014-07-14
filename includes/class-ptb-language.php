<?php

// Exit if accessed directly
if (!defined('ABSPATH')) exit;

/**
 * Page Type Builder Language.
 *
 * @package PageTypeBuilder
 * @version 1.0.0
 */

class PTB_Language {

  /**
   * ISO 639-1 codes
   *
   * @link http://en.wikipedia.org/wiki/List_of_ISO_639-1_codes
   *
   * @var array
   * @access private
   */

  private $iso_6391 = array(
    'aa', 'ab', 'ae', 'af', 'ak', 'am', 'an',
    'ar', 'as', 'av', 'ay', 'az', 'ba', 'be',
    'bg', 'bh', 'bi', 'bm', 'bn', 'bo', 'br',
    'bs', 'ca', 'ce', 'ch', 'co', 'cr', 'cs',
    'cu', 'cv', 'cy', 'da', 'de', 'dv', 'dz',
    'ee', 'el', 'en', 'eo', 'es', 'et', 'eu',
    'fa', 'ff', 'fi', 'fj', 'fo', 'fr', 'fy',
    'ga', 'gd', 'gl', 'gn', 'gu', 'gv', 'ha',
    'he', 'hi', 'ho', 'hr', 'ht', 'hu', 'hy',
    'hz', 'ia', 'id', 'ie', 'ig', 'ii', 'ik',
    'io', 'is', 'it', 'iu', 'ja', 'jv', 'ka',
    'kg', 'ki', 'kj', 'kk', 'kl', 'km', 'kn',
    'ko', 'kr', 'ks', 'ku', 'kv', 'kw', 'ky',
    'la', 'lb', 'lg', 'li', 'ln', 'lo', 'lt',
    'lu', 'lv', 'mg', 'mh', 'mi', 'mk', 'ml',
    'mn', 'mr', 'ms', 'mt', 'my', 'na', 'nb',
    'nd', 'ne', 'ng', 'nl', 'nn', 'no', 'nr',
    'nv', 'ny', 'oc', 'oj', 'om', 'or', 'os',
    'pa', 'pi', 'pl', 'ps', 'pt', 'qu', 'rm',
    'rn', 'ro', 'ru', 'rw', 'sa', 'sc', 'sd',
    'se', 'sg', 'si', 'sk', 'sl', 'sm', 'sn',
    'so', 'sq', 'sr', 'ss', 'st', 'su', 'sv',
    'sw', 'ta', 'te', 'tg', 'th', 'ti', 'tk',
    'tl', 'tn', 'to', 'tr', 'ts', 'tt', 'tw',
    'ty', 'ug', 'uk', 'ur', 'uz', 've', 'vi',
    'vo', 'wa', 'wo', 'xh', 'yi', 'yo', 'za',
    'zh', 'zu'
  );

  /**
   * Current language.
   *
   * @var string
   * @access private
   */

  private $lang = '';

  /**
   * Default language.
   *
   * @var string
   */

  public static $default = 'en';

  /**
   * Create a new instance of the class and set the language code.
   *
   * @param string $lang
   */

  public function __construct ($lang = 'en') {
    $this->lang = strtolower($lang);
  }

  /**
   * Check if the laguage code is in the ISO 639-1 array.
   *
   * @return bool
   */

  public function exist () {
    return in_array($this->lang, $this->iso_6391);
  }

  /**
   * Get language code.
   *
   * @return string
   */

  public function get_lang_code () {
    return $this->lang;
  }
}