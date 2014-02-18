<?php

/**
 * Page Type Builder Base class.
 */

class PTB_Base extends PTB_Properties {

  /**
   * Property sort order number. Starts a zero.
   *
   * @var int
   * @since 1.0
   */

  private $property_sort_order = 0;

  /**
   * Box sort order number. Starts a zero.
   *
   * @var int
   * @since 1.0
   */

  private $box_sort_order = 0;

  /**
   * Default property options.
   *
   * @var array
   * @since 1.0
   */

  private $property_default = array(
    'context'   => 'normal',
    'priority'  => 'default',
    'page_types' => array('page')
  );

  /**
   * Array of box with proprties.
   *
   * @var array
   * @since 1.0
   */

  private $boxes = array();

  /**
   * Output html one time.
   *
   * @var bool
   * @since 1.0
   */

  private $onetime_html = false;

  /**
   * Constructor.
   *
   * @param array $options
   * @since 1.0
   */

  public function __construct () {
    $this->setup_actions();
    $this->page_type = ptb_remove_ptb(strtolower(get_class($this)));
  }

  /**
   * Setup WordPress actions.
   *
   * @since 1.0
   * @access private
   */

  private function setup_actions () {
    add_action('add_meta_boxes', array($this, 'setup_page'));
  }

  /**
   * Add new property to the page.
   *
   * @param array $options
   * @since 1.0
   */

   public function property (array $options = array()) {
     $options = (object)array_merge($this->property_default, $options);
     $options->callback_args = new stdClass;

     // Can't proceed without a type.
     if (!isset($options->type)) {
       return;
     }

     // If the disable option is true, don't add it to the page.
     if (isset($options->disable) && $options->disable) {
       return;
     }

     // Set the key to the title slugify.
     if (!isset($options->key) || empty($options->key)) {
       $options->key = ptb_slugify($options->title);
     }

     if (!isset($options->box) || empty($options->box)) {
       $options->box = 'ptb_ ' . $options->title;
     }

     // Property sort order.
     if (!isset($options->sort_order)) {
       $this->property_sort_order++;
       $options->sort_order = $this->property_sort_order;
     } else if (intval($options->sort_order) > $this->property_sort_order) {
       $this->property_sort_order = intval($options->sort_order);
     } else {
       $this->property_sort_order++;
     }

     $options->callback_args->content = $this->toHTML($options, array(
       'name' => 'ptb_' . ptb_underscorify($options->key),
       'value' => ptb_get_property_value($options->key)
     ));

     if (!isset($this->boxes[$options->box])) {
       $this->boxes[$options->box] = (object)array(
         'name' => $options->box,
         'properties' => array(),
         'sort_order' => $options->box_sort_order
       );

       // Box sort order.
       if (!isset($this->boxes[$options->box]->sort_order)) {
         var_dump(1);
         $this->box_sort_order++;
         $this->boxes[$options->box]->sort_order = $this->box_sort_order;
       } else if (intval($this->boxes[$options->box]->sort_order) > $this->box_sort_order) {
         $this->box_sort_order = intval($this->boxes[$options->box]->sort_order);
       } else {
         $this->box_sort_order++;
       }

     }

     $this->boxes[$options->box]->properties[] = $options;
   }

   /**
    * Output the inner content of the meta box.
    *
    * @param object $post The WordPress post object
    * @param array $args
    * @since 1.0
    */

   public function box_callback ($post, $args) {
     if (isset($args['args']) && is_array($args['args'])) {
      if (!$this->onetime_html) {
        wp_nonce_field('page_type_builder', 'page_type_builder_nonce');
        echo PTB_Html::input('hidden', array(
          'name' => 'ptb_page_type',
          'value' => $this->page_type
        ));
        $this->onetime_html = true;
      }
      echo
      '<table>
        <tbody>';
      foreach ($args['args'] as $box) {
        echo $box->content;
      }
      echo
        '</tbody>
      </table>';
     }
   }

   /**
    * Setup the page.
    *
    * @since 1.0
    */

   public function setup_page () {
     usort($this->boxes, function ($a, $b) {
       return $a->sort_order - $b->sort_order;
     });
     foreach ($this->boxes as $box) {
       $args = array();
       usort($box->properties, function ($a, $b) {
         return $a->sort_order - $b->sort_order;
       });
       foreach ($box->properties as $property) {
         $args[] = $property->callback_args;
       }
       $first_box = $box->properties[0];
       foreach ($first_box->page_types as $page_type) {
         add_meta_box(ptb_slugify($box->name), ptb_remove_ptb($box->name), array($this, 'box_callback'), $page_type, $first_box->context, $first_box->priority, $args);
       }
     }
   }

   /**
    * Remove post tpye support. This function will only work one time on page load.
    *
    * @param string|array $remove_post_type_support
    * @since 1.0
    */

   public function remove ($remove_post_type_support = array(), $post_type = 'page') {
     if (is_string($remove_meta_boxes)) {
       $remove_post_type_support = array($remove_post_type_support);
     }

     if (!isset($this->remove_post_type_support)) {
      $this->remove_post_type_support = array($remove_post_type_support, $post_type);
      add_action('init', array($this, 'remove_post_type_support'), 10);
     }
   }

   /**
    * Admin menu, remove meta boxes.
    *
    * @since 1.0
    */

  public function remove_post_type_support () {
    foreach ($this->remove_post_type_support[0] as $post_type_support) {
      remove_post_type_support($this->remove_post_type_support[1], $post_type_support);
    }
  }
}