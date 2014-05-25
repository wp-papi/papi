<?php

/**
 * Page Type Builder Page Type.
 *
 * @package PageTypeBuilder
 */

class PTB_Page_Type {

  /**
   * The name of the page type.
   *
   * @var string
   * @since 1.0
   */

  public $name = '';

  /**
   * The description of the page type.
   *
   * @var string
   * @since 1.0
   */

  public $description = '';

  /**
   * The template of the page type.
   *
   * @var string
   * @since 1.0
   */

  public $template = '';

  /**
   * The post types to register the page type with.
   *
   * @var array
   * @since 1.0
   */

  public $post_types = array('page');

  /**
   * The page type. It's the name of the class.
   *
   * @var string
   * @since 1.0
   */

  public $page_type = '';

  /**
   * The file name of the page type file.
   *
   * @var string
   * @since 1.0
   */

  public $file_name = '';

  /**
   * The file path of the page type file.
   *
   * @var string
   * @since 1.0
   */

  public $file_path = '';

  /**
   * Load a page type by the file.
   *
   * @param string $file
   * @since 1.0
   */

  public function __construct ($file_path) {
    // Check so we have a file that exists.
    if (!is_string($file_path) || !file_exists($file_path) || !is_file($file_path)) {
      return;
    }

    // The variable key each page type class should have.
    $page_type = 'page_type';

    // Setup file and page type variables.
    $this->file_path = $file_path;
    $this->page_type = _ptb_get_class_name($this->file_path);
    $this->file_name = basename($this->file_path, '.php');

    // Try to load the page type class.
    if (!class_exists($this->page_type)) {
      require_once($this->file_path);
    }

    // Check so we have the page type meta array.
    $page_type_class = $this->page_type;

    if (!isset($page_type_class::$page_type)) {
      throw new PTB_Exception('page_type_meta is null');
    }

    // Filter all fields.
    $fields = $this->filter_page_type_fields($page_type_class::$page_type);

    // Add each field as a variable.
    foreach ($fields as $key => $value) {
      $this->$key = $value;
    }

    // Set a default value to post types array if we don't have a array or a empty array.
    if (!is_array($this->post_types) || empty($this->post_types)) {
      $this->post_types = array('page');
    }
  }

  /**
   * Create a new instance of the page type file.
   *
   * @since 1.0
   *
   * @return object
   */

  public function new_class () {
    if (!class_exists($this->page_type)) {
      require_once($this->file_path);
    }
    return new $this->page_type;
  }

  /**
   * Check so we have a name on the page type.
   *
   * @since 1.0
   *
   * @return bool
   */

  public function has_name () {
    return isset($this->name) && !empty($this->name);
  }

  /**
   * Filter page type fields. Some keys aren't allowed to use.
   *
   * @param array $arr
   * @since 1.0
   *
   * @return array
   */

  private function filter_page_type_fields ($arr = array()) {
    $res = array();
    $not_allowed = array('file_name', 'page_type');
    foreach ($arr as $key => $value) {
      if (in_array(strtolower($key), $not_allowed)) {
        continue;
      }
      $res[$key] = $value;
    }
    return $res;
  }

}