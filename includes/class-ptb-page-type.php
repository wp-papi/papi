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
    if (!file_exists($file)) {
      throw new PTB_Exception();
    }
    
    $class_name = get_ptb_class_name($file);
    $page_type = 'page_type';
    $file_name = ptb_remove_ptb(basename($file, '.php'));
    $fields = (object)$class_name::$page_type;
    foreach ($fields as $key => $value) {
      $this->$key = $value;
    }
  }
  
  private function load_file ($file) {
    if (!file_exists($file)) {
      return null;
    }
    
    $this->class_name = $this->get_class_name($file);
    $this->file_name = ptb_remove_ptb(basename($file, '.php'));
  }

  /**
   * Get class name from page type file.
   *
   * @param string $file
   * @since 1.0
   *
   * @return string|null
   */
  
  private function get_class_name ($file) {
    $content = file_get_contents($file);
    $tokens = token_get_all($content);
    $class_token = false;
    $class_name = null;

    foreach ($tokens as $token) {
      if (is_array($token)) {
        if ($token[0] === T_CLASS) {
          $class_token = true;
        } else if ($class_token && $token[0] === T_STRING) {
          $class_name = $token[1];
          $class_token = false;
        }
      }
    }

    return $class_name;
  }

}