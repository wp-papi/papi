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
      $property = _ptb_get_property_options($box_property);
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
    $options = _ptb_h($options, array());
    $this->options = array_merge($this->default_options, $options);
    $this->options = (object)$this->options;
    $this->options->slug = _ptb_slugify($this->options->title);

    $properties = $this->box_property($properties);

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

    // Setup actions.
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
    ?>
    <input type="hidden" name="ptb_page_type" value="<?php echo _ptb_get_page_type_meta_value(); ?>" />
    <?php

    // Render the properties.
    _ptb_render_properties($args['args']);
  }
}