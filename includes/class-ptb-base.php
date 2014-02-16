<?php

/**
 * Page Type Builder Base class.
 */

class PTB_Base extends PTB_Properties {

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

  public function __construct (array $options = array()) {
    $this->setup_options();
    $this->page_type($options);
    $this->setup_actions();
  }

  /**
   * Setup options keys.
   *
   * @since 1.0
   */

  private function setup_options () {
    foreach ($options as $key => $value) {
      if (isset($this->$key) && is_array($this->$key) && is_array($value)) {
        $this->$key = array_merge($this->$key, $value);
      } else {
        $this->$key = $value;
      }
    }

    $this->page_type = ptb_remove_ptb(strtolower(get_class($this)));
  }

  /**
   * Setup page type with options.
   *
   * @param array $options
   * @since 1.0
   * @access private
   */

  private function page_type (array $options = array()) {
    $options = (object)$options;
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
     if (isset($options->disable) && !$options->disable) {
      return;
     }

     // Set the key to the title slugify.
     if (!isset($options->key) || empty($options->key)) {
       $options->key = ptb_slugify($options->title);
     }

     if (!isset($options->box) || empty($options->box)) {
       $options->box = 'ptb_ ' . $options->title;
     }

     $options->callback_args->content = $this->toHTML($options, array(
       'name' => 'ptb_' . ptb_underscorify($options->key)
       // 'value' =>
     ));

     if (!isset($this->boxes[$options->box])) {
       $this->boxes[$options->box] = array();
     }

     $this->boxes[$options->box][] = $options;
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
        echo PTB_Html::hidden('ptb_page_type', $this->page_type);
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
     foreach ($this->boxes as $key => $box) {
       $args = array();
       foreach ($box as $property) {
         $args[] = $property->callback_args;
       }
       foreach ($box[0]->page_types as $page_type) {
         add_meta_box(ptb_slugify($key), ptb_remove_ptb($key), array($this, 'box_callback'), $page_type, $box[0]->context, $box[0]->priority, $args);
       }
     }
   }

}