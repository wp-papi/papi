<?php

/**
 * Page Type Builder Base class.
 */

class PTB_Base {

  /**
   * Data holder.
   *
   * @var array
   * @since 1.0
   */

  private $data = array();

  /**
   * Public functions that should not be called on when we load the page.
   *
   * @var array
   * @since 1.0
   */

  private $dont_call = array('add_meta_boxes', 'save_post');

  /**
   * Constructor.
   *
   * @param array $options
   * @since 1.0
   */

  public function __construct (array $options = array()) {
    $this->page_type($options);
    $this->setup_actions();
    $this->setup_page();
  }

  /**
   * Setup page type with options.
   *
   * @param array $options
   * @since 1.0
   */

  private function page_type (array $options = array()) {
    $options = (object)$options;
  }

  private function setup_actions () {
    add_action('add_meta_boxes', array($this, 'add_meta_boxes'));
    add_action('save_post', array($this, 'save_post'));
  }

  public function add_meta_boxes () {
    add_meta_box('url_page_id', __('URLs to fetch content from', 'isoscreamscreen'), 'url_page_custom_box', 'page');
  }

  /**
   * Get property from the data array.
   *
   * @param string $property
   *
   * @return mixed
   */

  public function __get ($property) {
    if (array_key_exists($property, $this->data)) {
      return $this->data[$property];
    }

    return null;
  }

  /**
   * Set property in the data array with the value.
   *
   * @param string $property
   * @since 1.0
   */

  public function __set ($property, $value) {
    $this->data[$property] = $value;
  }

  /**
   * Check if property is set or not.
   *
   * @param string $property
   * @since 1.0
   *
   * @return bool
   */

  public function __isset ($property) {
    return isset($this->data[$property]);
  }

  /**
   * Unset property from the data array.
   *
   * @param string $property
   * @since 1.0
   *
   * @return bool
   */

  public function __unset ($property) {
    unset($this->data[$property]);
  }

  /**
   * Collect all public methods from the class.
   *
   * @param object $klass
   * @since 1.0
   * @access private
   *
   * @return array
   */

   private function collect_methods ($klass) {
     $page_methods = get_class_methods($klass);
     $parent_methods = get_class_methods(get_parent_class($klass));
     return array_diff($page_methods, $parent_methods);
   }

   /**
    * Collect all public vars from the class.
    *
    * @param object $klass
    * @since 1.0
    * @access private
    *
    * @return array
    */

   private function collect_vars ($klass) {
     $page_vars = get_object_vars($klass);
     $parent_vars = get_class_vars(get_parent_class($this));
     return array_diff($page_vars, $parent_vars);
   }

   private function setup_page () {
     $public_methods = $this->collect_methods($this);
     $public_vars = $this->collect_vars($this);

     var_dump($public_methods);
   }

}