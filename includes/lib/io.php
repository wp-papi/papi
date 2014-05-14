<?php

/**
 * Page Type Builder I/O Functions.
 *
 * @package PageTypeBuilder
 * @version 1.0.0
 */

// Exit if accessed directly
if (!defined('ABSPATH')) exit;

/**
 * Get all registered directories.
 *
 * @param string $find
 * @since 1.0.0
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
    $dirs = array_filter($dirs, function ($dir) use ($directory) {
      return is_dir(rtrim($directory, '/') . '/' . $dir);
    });

    if (empty($dirs)) {
      $result[] = $directory;
    } else {
      foreach ($dirs as $dir) {
        if ($dir == $find) {
          $result[] = $directory . '/' . $dir;
        }
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
 * @since 1.0.0
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