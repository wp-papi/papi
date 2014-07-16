<?php

// Exit if accessed directly
if (!defined('ABSPATH')) exit;

/**
 * Page Type Builder Admin Meta Box.
 *
 * @package PageTypeBuilder
 * @version 1.0.0
 */

class PTB_Admin_Meta_Box {

  /**
   * Contains all root level properties in this meta box.
   *
   * @var array
   * @since 1.0.0
   */

  private $properties = array();

  /**
   * Box default options.
   *
   * @var array
   * @since 1.0.0
   */

  private $default_options = array(
    'context'    => 'normal',
    'priority'   => 'default',
    'post_type'  => 'page',
    'sort_order' => null,
    'properties' => array()
  );

  /**
   * Constructor.
   *
   * @param array $options
   * @param array $properties
   * @since 1.0.0
   */

  public function __construct ($options = array(), $properties = array()) {
    if (!is_array($options)) {
      $options = array();
    }

    $this->options = array_merge($this->default_options, $options);
    $this->options = (object)$this->options;
    $this->options->slug = _ptb_slugify($this->options->title);

    foreach ($properties as $property) {
      if (is_array($property)) {
        foreach ($property as $p) {
          if (is_object($p)) {
            $this->properties[] = $p;
          }
        }
      } else if (is_object($property)) {
        $this->properties[] = $property;
      }
    }

    // some sorting here would be nice,
    // english
    // ------
    // fields
    // ------
    // swedish
    // ------
    // fields

    // Setup actions.
    $this->setup_actions();
  }

  /**
   * Setup actions.
   *
   * @since 1.0.0
   */

  private function setup_actions () {
    add_action('add_meta_boxes', array($this, 'setup_meta_box'));
  }

  /**
   * Setup meta box.
   */

  public function setup_meta_box () {
    add_meta_box(
      $this->options->slug,
      _ptb_remove_ptb($this->options->title),
      array($this, 'render_meta_box'),
      $this->options->post_type,
      $this->options->context,
      $this->options->priority,
      $this->properties
    );
  }

  /**
   * Render the meta box
   *
   * @param array $post
   * @param array $args
   */

  public function render_meta_box ($post, $args) {
    if (!is_array($args) || !isset($args['args'])) {
      return;
    }

    wp_nonce_field('ptb_save_data', 'ptb_meta_nonce');

    // Output hidden field with page type value.
    echo PTB_Html::input('hidden', array(
      'name' => 'ptb_page_type',
      'value' => _ptb_get_page_type_meta_value()
    ));

    // Render the properties.
    _ptb_render_properties($args['args']);
  }
}