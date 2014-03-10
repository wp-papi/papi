<?php

class PTB_Page_Type {
  
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
    $this->path = PTB_PAGES_DIR;
    $files = glob(PTB_PAGES_DIR . '*');
  }
  
  private function get_files () {
    return glob($this->path . '*');
  }
  
}