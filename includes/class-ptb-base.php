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
   * @param bool $do
   * @since 1.0
   */

  public function __construct ($do = true) {
    if ($do) {
      $this->setup_actions();
      $this->page_type_class = get_class($this);
      $this->page_type_vars = (object)get_class_vars ($this->page_type_class);
      $this->page_type = _ptb_remove_ptb(strtolower($this->page_type_class));
    }
  }

  /**
   * Setup WordPress actions before we output any meta boxes.
   *
   * @since 1.0
   * @access private
   */

  private function setup_actions () {
    add_action('add_meta_boxes', array($this, 'setup_page'));
  }

  /**
   * Setup WordPress actions after we output meta boxes.
   *
   * @since 1.0
   * @access private
   */

  private function setup_after_actions () {
    add_action('admin_head', array($this, 'autocss'));
    add_action('admin_footer', array($this, 'autojs'));
  }

  /**
   * Load custom css file for page type.
   *
   * @since 1.0
   */

  public function autocss () {
    $name = get_class($this);
    $name = strtolower($name);
    $name = _ptb_dashify($name);
    $file = $name . '.css';

    $custom = _ptb_get_files_in_directory('gui', $file);
    $start = basename(WP_CONTENT_URL);
    $home_url = trailingslashit(home_url());

    foreach ($custom as $path) {
      $url = strstr($path, $start);
      $url = $home_url . $url;
      wp_enqueue_style($file, $url);
    }
  }

  /**
   * Load custom js file for page type.
   *
   * @since 1.0
   */

  public function autojs () {
    $name = get_class($this);
    $name = strtolower($name);
    $name = _ptb_dashify($name);
    $file = $name . '.js';

    $custom = _ptb_get_files_in_directory('gui', $file);
    $start = basename(WP_CONTENT_URL);
    $home_url = trailingslashit(home_url());

    foreach ($custom as $path) {
      $url = strstr($path, $start);
      $url = $home_url . $url;
      wp_enqueue_script($file, $url, array(), '1.0.0', true);
    }
  }

  /**
   * Add new property to the page.
   *
   * @param array $options
   * @since 1.0
   */

   public function property ($options = array()) {
     if (is_array($options)) {
       $options = (object)$options;
     } else {
      $options = $options;
     }

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

     // Generate random title.
     if (!isset($options->title) || empty($options->title)) {
       $options->title = _ptb_random_title();
     }

     // We should be able to remove the title.
     if (isset($options->no_title) && $options->no_title) {
       $options->title = '';
     }

     // If the disable option is true, don't add it to the page.
     if (isset($options->disable) && $options->disable) {
       return null;
     }

     // Set the key to the title slugify.
     if (!isset($options->name) || empty($options->name)) {
       $options->name = _ptb_slugify($options->title);
     }

     // Custom object for properties data.
     if (isset($options->custom)) {
       $options->custom = (object)$options->custom;
     } else {
       $options->custom = new stdClass;
     }

     // Yes, we should output as a table row.
     if (!isset($options->table)) {
       $options->table = true;
     }

     // Property sort order.
     if (!isset($options->sort_order)) {
       $options->sort_order = $this->property_sort_order;
       $this->property_sort_order++;
     } else if (intval($options->sort_order) > $this->property_sort_order) {
       $this->property_sort_order = intval($options->sort_order);
     } else {
       $this->property_sort_order++;
     }

     $options->name = _ptb_name($options->name);

     // Only set the vaue if we don't have value.
     if (!isset($options->value)) {
       $options->value = ptb_value($options->name);
     }

     // Get the property
     $property_type = PTB_Property::factory($options->type);

     // Can't access property since we don't know the property.
     if (is_null($property_type)) {
       return null;
     }

     $property_type->set_options($options);

     if (is_array($property_type->html())) {
       $options->callback_args->html = $property_type->html();
     } else {
       $options->callback_args->html = $property_type->render() . $property_type->hidden();
     }

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

    if (!is_array($options)) {
      $options = array();
    }

    if (!isset($options['title'])) {
      $options['title'] = $title;
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
      $options->sort_order = null;
    }

    if (!isset($options->context)) {
      $options->context = 'normal';
    }

    if (!isset($options->priority)) {
      $options->priority = 'default';
    }

    if (!isset($this->boxes[$options->title])) {
      $this->boxes[$options->title] = (object)array(
        'title' => $options->title,
        'properties' => array(),
        'sort_order' => $options->sort_order,
        'context'   => $options->context,
        'priority'  => $options->priority
      );

      // Box sort order.
      if (!isset($this->boxes[$options->title]->sort_order)) {
        $this->boxes[$options->title]->sort_order = $this->box_sort_order;
        $this->box_sort_order++;
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
        wp_nonce_field(PTB_META_KEY, PTB_META_KEY . '_nonce');
        echo PTB_Html::input('hidden', array(
          'name' => 'ptb_page_type',
          'value' => $this->page_type
        ));
        $this->onetime_html = true;
      }

      if (isset($args['args']['table']) && $args['args']['table']) {
        echo PTB_Html::tag('table', array(
          'class' => 'ptb-table'), false)
          . PTB_Html::start('tbody');
      }

      foreach ($args['args'] as $box) {
        if (isset($box->html)) {
          // All properties don't support thew new array output way so let's package it the right way.
          if (is_string($box->html)) {
            $box->html = array(
              array(
                'action' => 'html',
                'html' => $box->html
                )
              );
          } else if (is_array($box->html) && isset($box->html['action'])) {
            $box->html = array($box->html);
          }

          foreach ($box->html as $html) {
            _ptb_render_property_html($html);
          }
        }
      }

      if (isset($args['args']['table']) && $args['args']['table']) {
        echo PTB_Html::stop('tbody')
          . PTB_Html::stop('table');
      }
     }
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
       if (isset($box->properties[0]) &&
           isset($box->properties[0]->tab) &&
           $box->properties[0]->tab) {
         // It's a tab.
         $args[] = new PTB_Tab($box);
       } else {
         usort($box->properties, function ($a, $b) {
           if (isset($a->sort_order) && isset($b->sort_order)) {
             return $a->sort_order - $b->sort_order;
           } else {
             return 0;
           }
         });
         foreach ($box->properties as $property) {
           if (isset($property) && isset($property->callback_args)) {
             $args[] = $property->callback_args;
           }
         }
         $args['table'] = true;
       }

       // Fetch the post types we should register the meta boxes at.
       $post_types = isset($this->page_type_vars->page_type['post_types']) ?
         $this->page_type_vars->page_type['post_types'] : array('page');

       // Check so we are allowed to use the post type.
       $post_types = array_filter($post_types, function ($p) {
         return _ptb_is_page_type_allowed($p);
       });

       foreach ($post_types as $post_type) {
         $this->add_meta_box($box, $post_type, $args);
       }
     }

     $this->setup_after_actions();
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
    add_meta_box(_ptb_slugify($box->title),
                 _ptb_remove_ptb($box->title),
                 array($this, 'box_callback'),
                 $page_type,
                 $box->context,
                 $box->priority,
                 $args);
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
   * @param string $title
   * @param array $options
   * @param array $properties
   * @since 1.0
   *
   * @return object
   */

  public function tab ($title, $options = array(), $properties = array()) {
    if (empty($properties)) {
      $properties = $options;
      $options = array();
    }

    if (!is_array($options)) {
      $options = array();
    }

    return (object)array(
      'title'      => $title,
      'tab'        => true,
      'options'    => (object)$options,
      'properties' => $properties
    );
  }
}