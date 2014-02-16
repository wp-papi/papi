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
    'post_type' => 'page'
  );

  /**
   * Constructor.
   *
   * @param array $options
   * @since 1.0
   */

  public function __construct (array $options = array()) {
    $this->page_type($options);
    $this->setup_actions();
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
    add_action('add_meta_boxes', array($this, 'properties'));
    add_action('save_post', array($this, 'save_post'));
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
     $callback_args = array();

     // Can't proceed without a type.
     if (!isset($options->type)) {
       return;
     }

     // Set the key to the title slugify.
     if (!isset($options->key) || empty($options->key)) {
       $options->key = ptb_slugify($options->title);
     }

     // $html = $this->html($options->type);

     $callback_args['content'] = $this->toHTML($options->type, array(
       'name' => 'ptb_' . ptb_underscorify($options->key)//,
       // 'value' =>
     ));

     add_meta_box($options->key, $options->title, array($this, 'property_callback'), 'page', $options->context, $options->priority, $callback_args);
   }

   /**
    * Output the inner content of the meta box.
    *
    * @param object $post The WordPress post object
    * @param array $args
    * @since 1.0
    */

   public function property_callback ($post, $args) {
     if (isset($args['args']) && isset($args['args']['content'])) {
       echo $args['args']['content'];
     }
   }

}