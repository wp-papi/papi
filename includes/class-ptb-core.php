<?php

// Exit if accessed directly
if (!defined('ABSPATH')) exit;

/**
 * Page Type Builder Core class.
 */

class PTB_Core {
  
  /**
   * All page types that WP PTB should be available on.
   */
  
  public $page_types = array('page');

  /**
   * Constructor. Add actions.
   *
   * @since 1.0
   */

  public function __construct () {
    $this->view = new PTB_View;
    $this->page_types = apply_filters('ptb_page_types', $this->page_types);

    if (!is_array($this->page_types)) {
      $this->page_types = array('page');
    }

    add_action('admin_menu', array($this, 'ptb_menu'));
    add_action('admin_head', array($this, 'ptb_admin_head'));
    add_action('admin_footer', array($this, 'ptb_admin_footer'));
    add_filter('admin_body_class', array($this, 'ptb_admin_body_class'));
    add_action('admin_print_footer_scripts', array($this, 'ptb_add_new_link'));
    
    foreach ($this->page_types as $page_type) {
      add_filter('manage_' . $page_type . '_posts_columns', array($this, 'ptb_manage_page_type_posts_columns'));
      add_action('manage_' . $page_type . '_posts_custom_column', array($this, 'ptb_manage_page_type_posts_custom_column'), 10, 2);
    }

    // Load the page type.
    $this->ptb_load();
    
    // On post we need to save our custom data.
    // The action 'save_post' didn't work after 
    // we change how Page Type Builder is loaded.
    if (ptb_is_method('post')) {
      $this->ptb_save_post();
    }
  }

  /**
   * Build up the sub menu for "Page".
   *
   * @since 1.0
   */

  public function ptb_menu () {
    // Remove "Add new" menu item.
    remove_submenu_page('edit.php?post_type=page', 'post-new.php?post_type=page');
    // Add our custom menu item.
    add_submenu_page('edit.php?post_type=page', __('Add new', 'ptb'), __('Add new', 'ptb'), 'manage_options', 'ptb-add-new-page', array($this, 'ptb_view'));
  }
  
  /**
   * Change the "Add new" link on "edit-page" or "page" screen.
   *
   * @since 1.0
   */
  
  public function ptb_add_new_link () {
    $screen = get_current_screen();
    if ($screen->id == 'edit-page' || $screen->id == 'page') { ?>
      <script type="text/javascript">
        jQuery('.wrap h2 .add-new-h2').attr('href', 'edit.php?post_type=page&page=ptb-add-new-page');
      </script>
    <?php
    }
  }

  /**
   * Menu callback that loads right view depending on what the "page" query string says.
   *
   * @since 1.0
   */

  public function ptb_view () {
    $page_view = get_ptb_page_view();

    if (!is_null($page_view)) {
      $this->view->render($page_view);
    } else {
      echo '<h2>Page Type Builder - 404</h2>';
    }
  }

  /**
   * Load right Page Type Builder file if it exists.
   *
   * @since 1.0
   */

  public function ptb_load () {
    $uri = $_SERVER['REQUEST_URI'];
    $post_id = get_ptb_post_id();
    $page_type = get_ptb_page_type($post_id);

    // Only load Page Types on a "page" post type page in admin.
    if (strpos($uri, 'post-new.php?post_type=page') === false && (
      $post_id !== 0 && get_post_type($post_id) != 'page' ||
      isset($_POST['post_type']) && $_POST['post_type'] != 'page' ||
      is_null($page_type))) {
      return;
    }
    
    if (is_null($page_type)) {
      if (ptb_is_method('post') && $_POST['ptb_page_type']) {
        $page_type = $_POST['ptb_page_type'];
      } else {
        $page_type = get_ptb_page_type();
      }
    }

    $page_type = ptb_dashify($page_type);

    $path = PTB_PAGES_DIR . 'ptb-' . $page_type . '.php';

    // Can't proceed without a page type or if the file exists.
    if (!file_exists($path)) {
      return;
    }

    $class_name = get_ptb_class_name($path);

    // No class found.
    if (is_null($class_name)) {
      return;
    }

    // Require and initialize the page type.
    require_once($path);
    new $class_name;
  }

  /**
   * Save post.
   *
   * @since 1.0
   */

  public function ptb_save_post () {
    // Fetch the post id.
    if (isset($_POST['post_ID'])) {
      $post_id = $_POST['post_ID'];
    }
    
    if (!isset($post_id)) {
      return;
    }

    // Check if our nonce is set.
    if (!isset($_POST[PTB_META_KEY . '_nonce'])) {
      return $post_id;
    }

    $nonce = $_POST[PTB_META_KEY . '_nonce'];

    if (!wp_verify_nonce($nonce, PTB_META_KEY)) {
      return $post_id;
    }

    // If this is an autosave, our form has not been submitted, so we don't want to do anything.
    if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
      return $post_id;
    }

    // Check the user's permissions.
    if ('page' == $_POST['post_type']) {
      if (!current_user_can('edit_page', $post_id)) {
        return $post_id;
      }
    }
    
    // Debug code
    //echo'<pre>';
    //print_r($_POST);
    //die();

    // Get only Page Type Builder fields from the POST object.
    $meta_value = get_post_meta($post_id, PTB_META_KEY, true);
    $pattern = '/^ptb\_.*/';
    $keys = preg_grep($pattern, array_keys($_POST));

    // Loop through all keys and set values in the data array.
    foreach ($keys as $key) {
      $_POST[$key] = str_replace('\"', '', $_POST[$key]);
      if ($_POST[$key] == 'on') {
        $data[$key] = true;
      } else {
        $data[$key] = $_POST[$key];
      }
    }
    
    // Since we are storing witch property it is in the $data array
    // we need to remove that and set the property type to the property
    // and make a array of the property type and the value.
    foreach ($data as $key => $value) {
      if (strpos($key, '_property') === false) {
        continue;
      }
      
      $pkey = str_replace('_property', '', $key);
      
      $data[$pkey] = array(
        'type' => $value,
        'value' => $data[$pkey]
      );
      
      unset($data[$key]);
    }
    
    // Don't wont to save random data that's only is used for getting a nicer ui.
    foreach ($data as $key => $value) {
      if (ptb_is_random_title($key)) {
        unset($data[$key]);
      }
    }

    // Get right page type.
    $page_type = isset($data['ptb_page_type']) ? $data['ptb_page_type'] : '';

    // Add, update or delete the meta values.
    if (count($meta_value) == 0 || empty($meta_value)) {
      add_post_meta($post_id, PTB_META_KEY, $data, true);
      add_post_meta($post_id, '_wp_page_template', get_ptb_template($page_type), true);
    } else if (count($meta_value) > 0 && count($data) > 0) {
      update_post_meta($post_id, PTB_META_KEY, $data);
      update_post_meta($post_id, '_wp_page_template', get_ptb_template($page_type));
    } else {
      delete_post_meta($post_id, PTB_META_KEY, $meta_value);
      delete_post_meta($post_id, '_wp_page_template', get_ptb_template($page_type));
    }
  }

  /**
   * Add style to admin head.
   *
   * @since 1.0
   */

  public function ptb_admin_head () {
    echo '<link href="' . PTB_PLUGIN_URL . 'gui/css/ptb.css" type="text/css" rel="stylesheet" />';
  }

  /**
   * Add script to admin footer.
   *
   * @since 1.0
   */

  public function ptb_admin_footer () {
    echo '<script src="' . PTB_PLUGIN_URL . 'gui/js/ptb.js" type="text/javascript"></script>';
  }
  
  /**
   * Add custom body class when it's a page type.
   *
   * @since 1.0
   */
  
  public function ptb_admin_body_class ($classes) {
    global $post;
    $uri = $_SERVER['REQUEST_URI'];
    $post_id = get_ptb_post_id();
    $page_type = get_ptb_page_type($post_id);
    
    if (strpos($uri, 'post-new.php?post_type=page') === false && (
      $post_id !== 0 && get_post_type($post_id) != 'page' ||
      isset($_POST['post_type']) && $_POST['post_type'] != 'page' ||
      is_null($page_type))) {
      return $classes;
    }
    
    if (count(get_page_templates())) {
      $classes .= 'ptb-hide-cpt';
    }
    
    return $classes;
  }
  
  /**
   * Add custom table header to page type.
   *
   * @param array $defaults
   * @since 1.0
   *
   * @return array
   */
  
  public function ptb_manage_page_type_posts_columns ($defaults) {
    $defaults['page_type'] = __('Page Type', 'ptb');
    return $defaults;
  }
  
  /** 
   * Add custom table column to page type.
   *
   * @param string $column_name
   * @param int $post_id
   * @since 1.0
   */
  
  public function ptb_manage_page_type_posts_custom_column ($column_name, $post_id) {
    if ($column_name === 'page_type') {
      $page_type = get_ptb_file_data($post_id);
      if (!is_null($page_type)) {
        echo $page_type->page_type->name;
      } else {
        echo __('Normal page', 'ptb');
      }
    }
  }
}