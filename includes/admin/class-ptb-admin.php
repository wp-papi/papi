<?php

// Exit if accessed directly
if (!defined('ABSPATH')) exit;

/**
 * Page Type Builder Admin.
 *
 * @package PageTypeBuilder
 * @version 1.0.0
 */

final class PTB_Admin {

  /**
   * The instance of Page Type Builder Core.
   *
   * @var object
   * @since 1.0.0
   */

  private static $instance;

  /**
   * Page Type Bulider Core instance.
   *
   * @since 1.0.0
   *
   * @return object
   */

  public static function instance () {
    if (!isset(self::$instance)) {
      self::$instance = new PTB_Admin;
      self::$instance->setup_globals();
      self::$instance->setup_actions();
      self::$instance->setup_filters();
      self::$instance->setup_ptb();
    }
    return self::$instance;
  }

  /**
   * Page Type Builder Admin Constructor.
   */

  public function __construct () {}

  /**
   * Setup globals.
   *
   * @since 1.0.0
   * @access private
   */

  private function setup_globals () {
    $this->view = new PTB_Admin_View;
    $this->meta_boxes = new PTB_Admin_Meta_Boxes;
    $this->options_pages = new PTB_Admin_Options_Pages;
  }

  /**
   * Setup actions.
   *
   * @since 1.0.0
   * @access private
   */

  private function setup_actions () {
    add_action('admin_menu', array($this, 'admin_menu'));
    add_action('admin_head', array($this, 'admin_head'));
    add_action('admin_footer', array($this, 'admin_footer'));
  }

  /**
   * Setup filters.
   *
   * @since 1.0.0
   * @access private
   */

  private function setup_filters () {
    add_filter('admin_body_class', array($this, 'admin_body_class'));

    $post_types = _ptb_get_post_types();

    // Add post type columns to eavery post types that is used.
    foreach ($post_types as $post_type) {
      add_filter('manage_' . $post_type . '_posts_columns', array($this, 'manage_page_type_posts_columns'));
      add_action('manage_' . $post_type . '_posts_custom_column', array($this, 'manage_page_type_posts_custom_column'), 10, 2);
    }
  }

  /**
   * Build up the sub menu for "Page".
   *
   * @since 1.0.0
   */

  public function admin_menu () {
    $post_types = _ptb_get_post_types();
    $settings = _ptb_get_settings();
    $page_types = _ptb_get_all_page_types(true);

    // If we don't have any page types don't change any menu items.
    if (empty($page_types)) {
      return;
    }

    foreach ($post_types as $post_type) {

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
   * Add style to admin head.
   *
   * @since 1.0.0
   */

  public function admin_head () {
    echo '<link href="' . PTB_PLUGIN_URL . 'gui/css/style.css" type="text/css" rel="stylesheet" />';
    wp_enqueue_media();
  }

  /**
   * Add script to admin footer.
   *
   * @since 1.0.0
   */

  public function admin_footer () {
    // Output the main JavaScript file.
    echo '<script src="' . PTB_PLUGIN_URL . 'gui/js/main.js" type="text/javascript"></script>';

    // Find which screen and post that are in use.
    $screen = get_current_screen();
    $post_type = _ptb_get_wp_post_type();

    // Get the core settings.
    $settings = _ptb_get_settings();

    // Check if we should show one post type or not and create the right url for that.
    if (isset($settings[$post_type]) && isset($settings[$post_type]['only_page_type'])) {
      $url = _ptb_get_page_new_url($settings[$post_type]['only_page_type'], $post_type, false);
    } else {
      $url = "edit.php?post_type=$post_type&page=ptb-add-new-page,$post_type";
    }

    // If we are in the edit-page or has the post type register we output the jQuery code that change the "Add new" link.
    if ($screen->id == 'edit-page' || in_array($post_type, $settings['post_types'])) { ?>
      <script type="text/javascript">
        var current = jQuery('#adminmenu').find('li > a[href="<?php echo $url; ?>"]').attr('href');
        if (current === '<?php echo $url; ?>') {
          jQuery('.wrap h2 .add-new-h2').attr('href', '<?php echo $url; ?>');
        }
      </script>
    <?php
    }
  }


  /**
   * Add custom body class when it's a page type.
   *
   * @since 1.0.0
   */

  public function admin_body_class ($classes) {
    global $post;

    $uri = $_SERVER['REQUEST_URI'];
    $post_id = _ptb_get_post_id();
    $page_type = _ptb_get_page_type_meta_value($post_id);
    $post_type = _ptb_get_wp_post_type();

    if (!in_array($post_type, _ptb_get_post_types())) {
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
   * @since 1.0.0
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
   * @since 1.0.0
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
   * Menu callback that loads right view depending on what the "page" query string says.
   *
   * @since 1.0.0
   */

  public function render_view () {
    if (isset($_GET['page']) && strpos($_GET['page'], 'ptb') !== false) {
      $page = str_replace('ptb-', '', $_GET['page']);
      $page_view = preg_replace('/\,.*/', '', $page);
    } else {
      $page_view = null;
    }

    if (!is_null($page_view)) {
      $this->view->render($page_view);
    } else {
      echo '<h2>Page Type Builder - 404</h2>';
    }
  }

  /**
   * Load right Page Type Builder file if it exists.
   *
   * @since 1.0.0
   */

  public function setup_ptb () {
    $uri = $_SERVER['REQUEST_URI'];
    $post_id = _ptb_get_post_id();
    $page_type = _ptb_get_page_type_meta_value($post_id);
    $post_type = _ptb_get_wp_post_type();

    // If the post type isn't in the post types array we can't proceed.
    if (in_array($post_type, array('revision', 'nav_menu_item'))) {
      return;
    }

    // If we have a null page type we need to find which page type to use.
    if (is_null($page_type)) {
      if (_ptb_is_method('post') && isset($_POST['ptb_page_type']) && $_POST['ptb_page_type']) {
        $page_type = $_POST['ptb_page_type'];
      } else {
        $page_type = _ptb_get_page_type_meta_value();
      }
    }

    // Get the path to the page type file.
    $path = _ptb_get_page_type_file($page_type);

    if (is_null($page_type)) {
      return;
    }

    // Load the page type and create a new instance of it.
    $page_type = _ptb_get_page_type($path);

    if (is_null($page_type)) {
      return;
    }

    // Create a new class of the page type.
    $page_type->new_class();
  }

}
