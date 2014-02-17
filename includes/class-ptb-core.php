<?php

/**
 * Page Type Builder Core class.
 */

class PTB_Core {

  /**
   * Nonce key.
   *
   * @var string
   * @since 1.0
   */

  private $nonce_key = 'page_type_builder';

  /**
   * Constructor. Add actions.
   *
   * @since 1.0
   */

  public function __construct () {
    $this->view = new PTB_View;
    add_action('admin_menu', array($this, 'ptb_menu'));
    add_action('plugins_loaded', array($this, 'ptb_load'));
    add_action('save_post', array($this, 'ptb_save_post'));
    add_action('admin_head', array($this, 'ptb_admin_head'));
    add_action('admin_footer', array($this, 'ptb_admin_footer'));
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
    $post_id = isset($_GET['post']) ? $_GET['post'] : 0;
    $page_type = ptb_get_page_type($post_id);

    // Only load Page Types on a "page" post type page in admin.
    if (strpos($uri, 'post-new.php?post_type=page') === false && (
      $post_id !== 0 && get_post_type($post_id) != 'page' ||
      isset($_POST['post_type']) && $_POST['post_type'] != 'page' ||
      is_null($page_type))) {
      return;
    }
    
    if (is_null($page_type)) {
      if ($_SERVER['REQUEST_METHOD'] === 'POST' && $_POST['ptb_page_type']) {
        $page_type = $_POST['ptb_page_type'];
      } else {
        $page_type = get_ptb_page_type();
      }
    }

    $page_type = ptb_dashify($page_type);
    $path = PTB_DIR . 'ptb-' . $page_type . '.php';

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
   * @param object $post
   * @since 1.0
   */

  public function ptb_save_post ($post_id) {
    // Check if our nonce is set.
    if (!isset($_POST['page_type_builder_nonce'])) {
      return $post_id;
    }

    $nonce = $_POST['page_type_builder_nonce'];

    if (!wp_verify_nonce($nonce, 'page_type_builder')) {
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

    // Get only Page Type Builder fields from the POST object.
    $meta_value = get_post_meta($post_id, $this->nonce_key, true);
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

    $page_type = isset($data['ptb_page_type']) ? $data['ptb_page_type'] : '';

    // Add, update or delete the meta values.
    if (count($meta_value) == 0 || empty($meta_value)) {
      add_post_meta($post_id, $this->nonce_key, $data, true);
      add_post_meta($post_id, '_wp_page_template', ptb_get_template($page_type), true);
    } else if (count($meta_value) > 0 && count($data) > 0) {
      update_post_meta($post_id, $this->nonce_key, $data);
      update_post_meta($post_id, '_wp_page_template', ptb_get_template($page_type));
    } else {
      delete_post_meta($post_id, $this->nonce_key, $meta_value);
      delete_post_meta($post_id, '_wp_page_template', ptb_get_template($page_type));
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
}