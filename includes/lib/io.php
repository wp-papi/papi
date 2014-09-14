<?php

/**
 * Page Type Builder I/O functions.
 *
 * @package PageTypeBuilder
 * @version 1.0.0
 */

// Exit if accessed directly
if (!defined('ABSPATH')) exit;

/**
 * Get all register directories with Page Type Builder.
 *
 * @since 1.0.0
 *
 * @return array
 */

function _ptb_get_directories () {
  global $ptb_directories;

  if (empty($ptb_directories) || !is_array($ptb_directories)) {
    return array();
  }

  return $ptb_directories;
}

/**
 * Get all files in directory.
 *
 * @param string $directory
 * @since 1.0.0
 *
 * @return string
 */

function _ptb_get_all_files_in_directory ($directory = '') {
  $result = array();

  if ($handle = opendir($directory)) {
    while (false !== ($file = readdir($handle))) {
      if (!in_array($file, array('..', '.'))) {
        if (is_dir($directory . '/' . $file)) {
          $result = array_merge($result, _ptb_get_all_files_in_directory($directory . '/' . $file));
          $file = $directory . '/' . $file;
          $result[] = preg_replace('/\/\//si', '/', $file);
        } else {
          $file = $directory . '/' . $file;
          $result[] = preg_replace('/\/\//si', '/', $file);
        }
      }
    }
    closedir($handle);
  }

  return $result;
}

/**
 * Get all page type files from the register directories.
 *
 * @since 1.0.0
 *
 * @return array
 */

function _ptb_get_all_page_type_files () {
  $directories = _ptb_get_directories();
  $result = array();

  foreach ($directories as $directory) {
    $result = array_merge($result, _ptb_get_all_files_in_directory($directory));
  }

  return $result;
}

/**
 * Get page type file from page type query.
 *
 * @since 1.0.0
 *
 * @return string
 */

function _ptb_get_page_type_file ($file) {
  $directories = _ptb_get_directories();
  $file = '/' . _ptb_dashify($file) . '.php';
  foreach ($directories as $directory) {
    if (file_exists($directory . $file)) {
      return $directory . $file;
    }
  }
}

/**
 * Get page type base path.
 * This is used for figure out which page type to load on which page.
 *
 * @since 1.0.0
 *
 * @return string
 */

function _ptb_get_page_type_base_path ($file) {
  $directories = _ptb_get_directories();
  foreach ($directories as $directory) {
    if (strpos($file, $directory) !== false) {
      $file = str_replace($directory, '', $file);
    }
  }
  $file = ltrim($file, '/');
  $file = explode('.', $file);
  return $file[0];
}