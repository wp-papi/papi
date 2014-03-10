<?php

/**
 * Page Type Builder Page Type class.
 */

class PTB_Page_Type {

  /**
   * The name of the page type.
   *
   * @var string
   * @since 1.0
   */
  
  public var $name = '';

  /**
   * The description of the page type.
   *
   * @var string
   * @since 1.0
   */
  
  public var $description = '';

  /**
   * The template of the page type.
   *
   * @var string
   * @since 1.0
   */
  
  public var $template = '';
  
  /**
   * The post types to register the page type with.
   *
   * @var array
   * @since 1.0
   */
  
  public var $post_types = array('page');
  
  /**
   * Load a page type by the file.
   */
  
  public function __construct ($file) {
    if (file_exists($file)) {
      $class_name = get_ptb_class_name($file);
      $page_type = 'page_type';
      $file_name = ptb_remove_ptb(basename($file, '.php'));
      $fields = (object)$class_name::$page_type;
      foreach ($fields as $key => $value) {
        $this->$key = $value;
      }
    }
  }

}