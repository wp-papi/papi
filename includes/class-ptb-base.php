<?php

// Exit if accessed directly
if (!defined('ABSPATH')) exit;

/**
 * Page Type Builder Base class.
 */

class PTB_Base {

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
     $options = $this->setup_property($options);
     
     // Can't work with nullify properties.
     if (is_null($options)) {
      return;
     }
     
     return $options;
   }
    
   /**
    * Setup the property.
    *
    * @param object $options
    * @since 1.0
    *
    * @return null|object.
    */
    
   public function setup_property ($options) {
     $options = (object)$options;
     $options->callback_args = new stdClass;

     // Can't proceed without a type.
     if (!isset($options->type) && !PTB_Property::exists($options->type)) {
       return null;
     }

     // If the disable option is true, don't add it to the page.
     if (isset($options->disable) && $options->disable) {
       return null;
     }

     // Set the key to the title slugify.
     if (!isset($options->name) || empty($options->name)) {
       $options->name = ptb_slugify($options->title);
     }
    
     // Setup box options.
     if (!isset($options->box) || !is_array($options->box)) {
       $options->box = array(
        'title' => $options->title
       );
     }
     
     if (is_array($options->box)) {
      $options->box = (object)$options->box;
     }
    
     // Custom object for properties data.
     if (isset($options->custom)) {
       $options->custom = (object)$options->custom;
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

     $options->name = ptb_underscorify(ptbify($options->name));
     $options->value = ptb_get_property_value($options->name);

     // Get the property
     $property = PTB_Property::factory($options->type);
     $property->set_options($options);
     
     $options->callback_args->html = $property->render() . $property->hidden();
       
     return $options;
  }
  
  /**
   * Add new box.
   *
   * @param string $title
   * @param array $options
   * @param array $items
   * @since 1.0
   */
  
  public function box ($title, $options = array(), $properties = array()) {
    if (empty($properties)) {
      $properties = $options;
      $options = array();
    }
    
    if (empty($options)) {
      $options = array(
        'title' => $title
      );
    }
    
    $this->setup_box($options);
    
    if (isset($this->boxes[$title])) {
      $this->boxes[$title]->properties = $properties;
    }
  }
  
  /**
   * Setup box.
   *
   * @param object $options
   * @since 1.0
   */
  
  public function setup_box ($options) { 
    $options = (object)$options;
    
    if (!isset($options->sort_order)) {
      $options->sort_order = 0;
    }
    
    if (!isset($options->page_types) || !is_array($options->page_types)) {
      $options->page_types = array('page');
    }
    
    if (!isset($this->boxes[$options->title])) {
      $this->boxes[$options->title] = (object)array(
        'title' => $options->title,
        'properties' => array(),
        'sort_order' => $options->sort_order,
        'page_types' => $options->page_types,
        'context'   => 'normal',
        'priority'  => 'default'
      );

      // Box sort order.
      if (!isset($this->boxes[$options->title]->sort_order)) {
        $this->box_sort_order++;
        $this->boxes[$options->title]->sort_order = $this->box_sort_order;
      } else if (intval($this->boxes[$options->title]->sort_order) > $this->box_sort_order) {
        $this->box_sort_order = intval($this->boxes[$options->title]->sort_order);
      } else {
        $this->box_sort_order++;
      }
    }
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
      if (isset($args['args']['table']) && $args['args']['table']) { 
        echo
        '<table class="ptb-table">
          <tbody>';
      }
      foreach ($args['args'] as $box) {
        if (isset($box->html)) {
          echo $box->html;
        }
      }
      if (isset($args['args']['table']) && $args['args']['table']) {
        echo
          '</tbody>
        </table>';
      }
     }
  }
  
  /**
   * Render tabs html.
   *
   * @param object $box
   * @since 1.0
   *
   * @return object
   */
  
  public function render_tabs ($box) {
    $html_tabs = '<ul class="ptb-tabs">';
    $html_content = '<div class="ptb-tabs-content">';
    $first_tab = $box->properties[0];
    foreach ($box->properties as $tab) {
      $html_tabs .= '<li ' . ($first_tab->title == $tab->title ? 'class="active"' : '') . '>';
      $html_tabs .= '<a href="#" data-ptb-tab="' . ptb_slugify(ptbify($tab->title)) . '">'. $tab->title . '</a></li>';
      $html_content .= '<div data-ptb-tab="' . ptb_slugify(ptbify($tab->title)) . '" ' . ($first_tab->title == $tab->title ? 'class="active"' : '') . '>
        <table class="ptb-table">
          <tbody>';
      foreach ($tab->properties as $property) {
        $html_content .= $property->callback_args->html;
      }
      $html_content .= '</tbody>
        </table>
      </div>';
    }
    $html_content .= '</div>';
    $html_tabs .= '</ul>';
    return (object)array(
      'html' => $html_tabs . $html_content
    );
  }

  /**
   * Setup the page.
   *
   * @since 1.0
   */

   public function setup_page () {
     $tabs = array();
     usort($this->boxes, function ($a, $b) {
       return $a->sort_order - $b->sort_order;
     });
     foreach ($this->boxes as $box) {
       $args = array();
       if (isset($box->properties[0]) && isset($box->properties[0]->tab) && $box->properties[0]->tab) {
         $args[] = $this->render_tabs($box);
       } else {
         usort($box->properties, function ($a, $b) {
          return $a->sort_order - $b->sort_order;
         });
         foreach ($box->properties as $property) {
           $args[] = $property->callback_args;
         }
         $args['table'] = true;
       }
       foreach ($box->page_types as $page_type) {
         $this->add_meta_box($box, $page_type, $args);
       }
    }
  }

  /**
   * Add meta box.
   *
   * @param object $box
   * @param string $page_type
   * @param array $args
   * @since 1.0
   */
  public function add_meta_box ($box, $page_type, $args) {
    add_meta_box(ptb_slugify($box->title), ptb_remove_ptb($box->title), array($this, 'box_callback'), $page_type, $box->context, $box->priority, $args);
  }

  /**
   * Remove post tpye support. This function will only work one time on page load.
   *
   * @param string|array $remove_post_type_support
   * @since 1.0
   */

  public function remove ($remove_post_type_support = array(), $post_type = 'page') {
    if (is_string($remove_post_type_support)) {
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
  
  /**
   * Add a new tab.
   *
   * @param string $name
   * @param array $properties
   * @since 1.0
   *
   * @return object
   */
  
  public function tab ($title, $properties = array()) {
    return (object)array(
      'title' => $title,
      'tab' => true,
      'properties' => $properties
    );
  }
}