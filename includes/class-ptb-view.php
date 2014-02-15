<?php

/**
 * Page Type Builder View class.
 */

class PTB_View {

  /**
   * Path to view dir.
   */

  private $path = '';

  /**
   * View constructor.
   *
   * @since 1.0
   */

  public function __construct ($path = '') {
    $this->path = !empty($path) ? $path : PTB_PLUGIN_DIR . 'views/';
  }

  /**
   * Check if file exists.
   *
   * @param string $file
   * @since 1.0
   *
   * @return bool
   */

  public function exists ($file) {
    return file_exists($this->file($file));
  }

  /**
   * Render file.
   *
   * @param string $file
   * @since 1.0
   *
   * @return string|null
   */

  public function render ($file) {
    if (!empty($file) && $this->exists($file)) {
      return require_once($this->file($file));
    }

    return null;
  }

  /**
   * Display file.
   *
   * @param string $file
   * @since 1.0
   */

  public function display ($file) {
    $html = $this->render($file);

    if (!is_null($html)) {
      echo $html;
    }
  }

  /**
   * Get full path to file with php exstention.
   *
   * @param string $file
   * @since 1.0
   * @access private
   *
   * @return string
   */

  private function file ($file) {
    return $this->path . $file . '.php';
  }

}