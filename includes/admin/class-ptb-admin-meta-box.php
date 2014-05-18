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
   * Output html once.
   *
   * @var bool
   * @since 1.0
   */

  private $output_once = false;

  /**
   * Page Type Builder Admin Box Constructor.
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

    $this->properties = array_merge($this->properties, $properties);

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
      _ptb_slugify($this->options->title),
      _ptb_remove_ptb($this->options->title),
      array($this, 'render_meta_box'),
      $this->options->post_type,
      $this->options->context,
      $this->options->priority,
      array(
        'table' => !empty($this->properties) ? $this->properties[0]->table : false,
        'properties' => $this->properties
      )
    );
  }

  /**
   * Render the meta box
   *
   * @param array $post
   * @param array $args
   */

  public function render_meta_box ($post, $args) {
    if (!is_array($args) && !isset($args['args'])) {
      return;
    }

    wp_nonce_field('ptb_save_data', 'ptb_meta_nonce');

    if (isset($args['args']['table']) && $args['args']['table']) {
      echo '<table class="ptb-table">';
        echo '<tbody>';
    }

    $properties = $args['args']['properties'];

    foreach ($properties as $property) {
      if (empty($property->type)) {
        continue;
      }

      $property_type = _ptb_get_property($property->type);

      if (is_null($property_type)) {
        continue;
      }

      $property_type->set_options($property);

      // Render the property.
      $property_type->render();
      $property_type->hidden();
    }

    if (isset($args['args']['table']) && $args['args']['table']) {
        echo '</tbody>';
      echo '</table>';
    }
  }
}

// FUNCTIONS BELOW THIS COMMENT THAT SHOULD BE MOVED INTO FUNCTIONS FILES

function _ptb_render_property($property) {
  if (!is_object($property)) {
    return;
  }

  // New way to output html for a property.
  if (method_exists('html', $property)) {
    $property->html();
  } else {
    // Old way to output html for a property.
    echo $property->html;
  }
}