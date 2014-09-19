<?php

// Exit if accessed directly
if (!defined('ABSPATH')) exit;

/**
 * Papi Admin Meta Box.
 *
 * @package Papi
 * @version 1.0.0
 */

class Papi_Admin_Meta_Box {

  /**
   * Contains all root level properties in this meta box.
   *
   * @var array
   * @since 1.0.0
   */

  private $properties = array();

  /**
   * Meta box default options.
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
   * Meta box options.
   *
   * @var object
   * @since 1.0.0
   */

  private $options;

  /**
   * Setup actions.
   *
   * @since 1.0.0
   */

  private function setup_actions () {
    add_action('add_meta_boxes', array($this, 'setup_meta_box'));
  }

  /**
   * Box property is a property that is direct on the box function with the property function.
   *
   * @param array $properties
   * @since 1.0.0
   *
   * @return array
   */

  private function box_property ($properties) {
    $box_property = array_filter($properties, function ($property) {
      return !is_object($property) && !is_array($property);
    });

    if (!empty($box_property)) {
      $property = _papi_get_property_options($box_property);
      if (!$property->disabled) {
        $properties[] = $property;
      }
    }

    return $properties;
  }

  /**
   * Constructor.
   *
   * @param array $options
   * @param array $properties
   * @since 1.0.0
   */

  public function __construct ($options = array(), $properties = array()) {
    $options = _papi_h($options, array());
    $options = array_merge($this->default_options, $options);
    $this->options = (object)$options;
    $this->options->slug = _papi_slugify($this->options->title);

    $properties = $this->box_property($properties);

    // Fix so the properties array will have the right order.
    $properties = array_reverse($properties);

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

    $this->setup_actions();
  }

  /**
   * Add property.
   *
   * @param object $property
   * @since 1.0.0
   */

  public function add_property ($property) {
    $this->properties[] = $property;
  }

  /**
   * Setup meta box.
   */

  public function setup_meta_box () {
    add_meta_box(
      $this->options->slug,
      _papi_remove_papi($this->options->title),
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

    wp_nonce_field('papi_save_data', 'papi_meta_nonce');
    ?>
    <input type="hidden" name="papi_page_type" value="<?php echo _papi_get_page_type_meta_value(); ?>" />
    <?php

    // Render the properties.
    _papi_render_properties($args['args']);
  }
}
