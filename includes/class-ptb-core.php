<?php

// Exit if accessed directly
if (!defined('ABSPATH')) exit;

/**
 * Page Type Builder Core class.
 */

final class PTB_Core2 {

  /**
   * The settings for Page Type Builder
   * Can be overriden by the filter `ptb_settings`.
   *
   * @var array
   * @since 1.0
   */

  private $settings = array();

  /**
   * All page types that WP PTB should be available on.
   */

  public $post_types = array('page');

  /**
   * Constructor. Add actions.
   *
   * @since 1.0
   */

  public function __construct () {
    $this->view = new PTB_View;

    $post_types = apply_filters('ptb_post_types', $this->post_types);

    // If is a array and not empty we replace the post type array with the new one.
    if (is_array($this->post_types) && !empty($this->post_types)) {
      $this->post_types = $post_types;
    }

    // Add actions
    add_action('admin_menu', array($this, 'admin_menu'));
    add_action('admin_head', array($this, 'admin_head'));
    add_action('admin_footer', array($this, 'admin_footer'));

    // Add filters.
    add_filter('admin_body_class', array($this, 'admin_body_class'));

    // Add post type columns to eavery post types that is used.
    foreach ($this->post_types as $post_type) {
      add_filter('manage_' . $post_type . '_posts_columns', array($this, 'manage_page_type_posts_columns'));
      add_action('manage_' . $post_type . '_posts_custom_column', array($this, 'manage_page_type_posts_custom_column'), 10, 2);
    }

    // Load the page type.
    $this->ptb_load();

    // On post we need to save our custom data.
    // The action 'save_post' didn't work after
    // we change how Page Type Builder is loaded.
    if (_ptb_is_method('post')) {
      $this->save_post();
    }
  }

  /**
   * Build up the sub menu for "Page".
   *
   * @since 1.0
   */

  public function admin_menu () {
    $settings = $this->get_settings();

    foreach ($this->post_types as $post_type) {

      // Remove "Add new" menu item.
      remove_submenu_page('edit.php?post_type=' . $post_type, 'post-new.php?post_type=' . $post_type);

      if (isset($settings[$post_type]) && isset($settings[$post_type]['only_page_type'])) {
        $url = _ptb_get_page_new_url($settings[$post_type]['only_page_type'], $post_type, false);
        // Add our custom menu item.
        add_submenu_page('edit.php?post_type=' . $post_type,
                         __('Add New', 'ptb'),
                         __('Add New', 'ptb'),
                         'manage_options',
                         $url);
      } else {
        // Add our custom menu item.
        add_submenu_page('edit.php?post_type=' . $post_type,
                         __('Add New', 'ptb'),
                         __('Add New', 'ptb'),
                         'manage_options',
                         'ptb-add-new-page,' . $post_type,
                         array($this, 'render_view'));
      }
    }
  }

  /**
   * Menu callback that loads right view depending on what the "page" query string says.
   *
   * @since 1.0
   */

  public function render_view () {
    $page_view = _ptb_get_page_view();

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
    $post_id = _ptb_get_post_id();
    $page_type = _ptb_get_page_page_type($post_id);
    $post_type = _ptb_get_wp_post_type();

    // If the post type isn't in the post types array we can't proceed.
    if (!in_array($post_type, $this->post_types)) {
      return;
    }

    // If we have a null page type we need to find which page type to use.
    if (is_null($page_type)) {
      if (_ptb_is_method('post') && isset($_POST['ptb_page_type']) && $_POST['ptb_page_type']) {
        $page_type = $_POST['ptb_page_type'];
      } else {
        $page_type = _ptb_get_page_page_type();
      }
    }

    // Get the path to the page type file.
    $path = _ptb_get_page_type_file($page_type);

    // Load the page type and create a new instance of it.
    $page_type = new PTB_Page_Type($path);

    // Check so we have any data.
    if (!$page_type->has_name()) {
      return;
    }

    // Create a new class of the page type.
    $page_type->new_class();
  }

  /**
   * Save post.
   *
   * @since 1.0
   */

  public function save_post () {
    // Fetch the post id.
    if (isset($_POST['post_ID'])) {
      $post_id = $_POST['post_ID'];
    }

    // Can't proceed without any post id.
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
    // echo'<pre>';
    // print_r($_POST);
    // die();

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
      $property_key = _ptb_property_type_key();

      if (strpos($key, $property_key) === false) {
        continue;
      }

      $pkey = str_replace($property_key, '', $key);

      // Check if value exists.
      if (isset($data[$pkey])) {
        $data[$pkey] = array(
          'type' => $value,
          'value' => $data[$pkey]
        );
      }

      unset($data[$key]);
    }

    // Don't wont to save random data that's only is used for getting a nicer ui.
    foreach ($data as $key => $value) {
      if (_ptb_is_random_title($key)) {
        unset($data[$key]);
      }
    }

    // Get right page type.
    $page_type = isset($data['ptb_page_type']) ? $data['ptb_page_type'] : '';
    $page_template = _ptb_get_template($page_type);

    // Add, update or delete the meta values.
    if (count($meta_value) == 0 || empty($meta_value)) {
      add_post_meta($post_id, PTB_META_KEY, $data, true);

      // Only update page template if we have one.
      if (!is_null($page_template)) {
        add_post_meta($post_id, '_wp_page_template', $page_template, true);
      }
    } else if (count($meta_value) > 0 && count($data) > 0) {
      update_post_meta($post_id, PTB_META_KEY, $data);

      // Only update page template if we have one.
      if (!is_null($page_template)) {
        update_post_meta($post_id, '_wp_page_template', $page_template);
      }
    } else {
      delete_post_meta($post_id, PTB_META_KEY, $meta_value);

      // Only update page template if we have one.
      if (!is_null($page_template)) {
        delete_post_meta($post_id, '_wp_page_template', $page_template);
      }
    }
  }

  /**
   * Add style to admin head.
   *
   * @since 1.0
   */

  public function admin_head () {
    echo '<link href="' . PTB_PLUGIN_URL . 'gui/css/style.css" type="text/css" rel="stylesheet" />';
  }

  /**
   * Add script to admin footer.
   *
   * @since 1.0
   */

  public function admin_footer () {
    // Output the main JavaScript file.
    echo '<script src="' . PTB_PLUGIN_URL . 'gui/js/main.js" type="text/javascript"></script>';

    // Find which screen and post that are in use.
    $screen = get_current_screen();
    $post_type = _ptb_get_wp_post_type();

    // Get the core settings.
    $settings = $this->get_settings();

    // Check if we should show one post type or not and create the right url for that.
    if (isset($settings[$post_type]) && isset($settings[$post_type]['only_page_type'])) {
      $url = _ptb_get_page_new_url($settings[$post_type]['only_page_type'], $post_type, false);
    } else {
      $url = "edit.php?post_type=$post_type&page=ptb-add-new-page,$post_type";
    }

    // If we are in the edit-page or has the post type register we output the jQuery code that change the "Add new" link.
    if ($screen->id == 'edit-page' || in_array($post_type, $this->post_types)) { ?>
      <script type="text/javascript">
        jQuery('.wrap h2 .add-new-h2').attr('href', '<?php echo $url; ?>');
      </script>
    <?php
    }
  }

  /**
   * Add custom body class when it's a page type.
   *
   * @since 1.0
   */

  public function admin_body_class ($classes) {
    global $post;

    $uri = $_SERVER['REQUEST_URI'];
    $post_id = _ptb_get_post_id();
    $page_type = _ptb_get_page_page_type($post_id);
    $post_type = _ptb_get_wp_post_type();

    if (!in_array($post_type, $this->post_types)) {
      return;
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

  public function manage_page_type_posts_columns ($defaults) {
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

  public function manage_page_type_posts_custom_column ($column_name, $post_id) {
    if ($column_name === 'page_type') {
      $page_type = _ptb_get_file_data($post_id);
      if (!is_null($page_type)) {
        echo $page_type->name;
      } else {
        echo __('Standard Page', 'ptb');
      }
    }
  }

  /**
   * Get Page Type Builder settings after we run apply filter on it.
   *
   * @since 1.0
   *
   * @return array
   */

  public function get_settings () {
    return apply_filters('ptb_settings', $this->settings);
  }
}