<?php

/**
 * Page Type Builder Page Data.
 *
 * @package PageTypeBuilder
 */

abstract class PTB_Page_Data {

  /**
   * Contains all register properties on this page.
   * Will only contain root level properties.
   *
   * @var array
   * @since 1.0.0
   */

  private $properties = array();

  /**
   * Page Type Builder Page Data Constructor.
   *
   * @since 1.0.0
   */

  public function __construct () {
    // Setup globals.
    $this->setup_globals();

    // Setup actions.
    $this->setup_actions();
  }

  /**
   * Setup globals.
   *
   * @since 1.0.0
   */

  private function setup_globals () {
    // Maybe should be static?
    $this->page_type = static::$page_type;
  }

  /**
   * Setup actions.
   *
   * @since 1.0.0
   */

  private function setup_actions () {}

  /**
   * Add new meta box with properties.
   *
   * @param string $title.
   * @param array $options
   * @param array $items
   * @since 1.0.0
   */

  protected function box ($title = '', $options = array(), $properties = array()) {
    // Options is optional value.
    if (empty($properties)) {
      $properties = $options;
      $options = array();
    }

    // Move title into options.
    if (!isset($options['title'])) {
      $options['title'] = $title;
    }

    $this->box = new PTB_Admin_Meta_Box($options, $properties);
  }

  /**
   * Add new property to the page.
   *
   * @param array $options
   * @since 1.0.0
   *
   * @return array
   */

  protected function property ($options = array()) {
    return _ptb_get_property_options($options);
  }

  /**
   * Remove post type support. Runs once, on page load.
   *
   * @param array $post_type_support
   * @since 1.0.0
   */

  protected function remove ($remove_post_type_support = array()) {
    $this->remove_post_type_support = $remove_post_type_support;
    add_action('init', array($this, 'remove_post_type_support'));
  }

  /**
   * Remove post type support action.
   *
   * @since 1.0.0
   */

  public function remove_post_type_support () {
    // Get all post types.
    $post_types = $this->get_post_types();

    // Loop through all post type support to remove and all post types.
    foreach ($this->remove_post_type_support as $post_type_support) {
      foreach ($post_types as $post_type) {
        remove_post_type_support($post_type, $post_type_support);
      }
    }
  }
}