<?php

/**
 *  Page Type Builder Page Types class.
 */

class PTB_Page_Types {

  /**
   * Path to the page types.
   *
   * @var string
   * @since 1.0
   */

  private var $path;

  /**
   * Static array that contains the page types.
   *
   * @var array
   * @since 1.0
   */

  private static $page_types = array();

  /**
   * Constructor.
   */

  public function __construct () {
    $files = _ptb_get_files_in_directory('page-types');
  }

  /**
   * Fetch all page types that exists in the page types folder.
   *
   * @since 1.0
   *
   * @return array
   */

  private function fetch_all () {
    $files = glob($this->path . '*');
    $res = array();

    foreach ($files as $file) {
      $res[] = new PTB_Page_Type($file);
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

  private function get_page_type_meta ($file) {
    $class_name = _ptb_get_class_name($file);
    $page_type = 'page_type';

    require_once($file);

    return (object)array(
      'file_name' => ptb_remove_ptb(basename($file, '.php')),
      'meta' => (object)$class_name::$page_type
    );
  }

}