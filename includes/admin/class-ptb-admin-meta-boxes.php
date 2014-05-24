<?php

// Exit if accessed directly
if (!defined('ABSPATH')) exit;

/**
 * Page Type Builder Admin Meta Boxes.
 *
 * @package PageTypeBuilder
 * @version 1.0.0
 */

class PTB_Admin_Meta_Boxes {

  /**
   * Page Type Builder Admin Meta Boxes Constructor.
   */

  public function __construct () {
    // Setup actions.
    $this->setup_actions();

    // Setup filters.
    $this->setup_filters();

    // On post we need to save our custom data.
    // The action 'save_post' didn't work after
    // we change how Page Type Builder is loaded.
    if (_ptb_is_method('post')) {
      $this->save_meta_boxes();
    }
  }

  /**
   * Setup actions.
   *
   * @since 1.0.0
   * @access private
   */

  private function setup_actions () {
    // Try to get this to work.
    // add_action('save_post', array($this, 'save_meta_boxes'), 1, 2);
  }

  /**
   * Setup filters.
   *
   * @since 1.0.0
   * @access private
   */

  private function setup_filters () {
    add_filter('wp_insert_post_data', array($this, 'wp_insert_post_data'));
  }

  /**
   * Sanitize data before saving it.
   *
   * @param mixed $value
   * @since 1.0.0
   *
   * @return mixed
   */

  private function santize_data ($value) {
    if (is_array($value)) {
      foreach ($value as $k => $v) {
        if (is_string($v)) {
          $value[$k] = $this->santize_data($v);
        }
      }
    } else if (is_string($value)) {
      $value = remove_trailing_quotes($value);
    }

    return $value;
  }

  /**
   * Get properties data from the post object.
   *
   * @since 1.0.0
   *
   * @return array
   */

  private function get_properties_data () {
    $data = array();
    $pattern = '/^ptb\_.*/';
    $keys = preg_grep($pattern, array_keys($_POST));

    // Loop through all keys and set values in the data array.
    foreach ($keys as $key) {
      if (!isset($_POST[$key])) {
        continue;
      }

      // Fix for input fields that should be true or false.
      if ($_POST[$key] === 'on') {
        $data[$key] = true;
      } else {
        $data[$key] = $this->santize_data($_POST[$key]);
      }
    }

    // Don't wont to save meta nonce field.
    if (isset($data['ptb_meta_nonce'])) {
      unset($data['ptb_meta_nonce']);
    }

    return $data;
  }

  /**
   * Prepare properties data for saving.
   *
   * @param array $data
   * @since 1.0.0
   *
   * @return array
   */

  private function prepare_properties_data (array $data = array()) {
    // Since we are storing witch property it is in the $data array
    // we need to remove that and set the property type to the property
    // and make a array of the property type and the value.
    foreach ($data as $key => $value) {
      $property_type_key = _ptb_property_type_key();

      if (strpos($key, $property_type_key) === false) {
        continue;
      }

      $property_key = str_replace($property_type_key, '', $key);

      // Check if value exists.
      if (isset($data[$property_key])) {
        $data[$property_key] = array(
          'type'  => $value,
          'value' => $data[$property_key]
        );
      }

      unset($data[$key]);
    }

    // Remove random data that only is used for a nicer ui.
    foreach ($data as $key => $value) {
      if (_ptb_is_random_title($key)) {
        unset($data[$key]);
      }
    }

    // Properties holder.
    $properties = array();

    // Run `before_save` on a property class if it exists.
    foreach ($data as $key => $value) {
      $property_type = $value['type'];

      // Get the property class if we don't have it.
      if (!isset($properties[$property_type])) {
        $properties[$property_type] = PTB_Property::factory($property_type);
      }

      $property = $properties[$property_type];

      // Can't handle null properties.
      // Remove it from the data array and continue.
      if (is_null($property)) {
        unset($data[$key]);
        continue;
      }

      // Run `before_save` if it exists on the property class.
      if (method_exists($property, 'before_save')) {
        $data[$key]['value'] = $property->before_save($data[$key]['value']);
      }

      // Apply a filter so this can be changed from the theme also.
      $data[$key] = apply_filters('ptb_property_before_save', $data[$key]);
    }

    // Check so all properties has a value and a type key and that the property is a array.
    $data = array_filter($data, function ($property) {
      return is_array($property) && isset($property['value']) && isset($property['type']);
    });

    return $data;
  }

  /**
   * Get page template from the post data.
   *
   * @param array $data
   * @since 1.0.0
   *
   * @return string
   */

  private function get_page_template (array $data = array()) {
    $post_id = isset($data['post_ID']) ? $data['post_ID'] : 0;
    return _ptb_get_template($post_id);
  }

  /**
   * Get page type from the post data.
   *
   * @param array $data
   * @since 1.0.0
   *
   * @return string
   */

  private function get_page_type (array $data = array()) {
    return h($data['ptb_page_type'], '');
  }

  /**
   * Pre save page template and page type.
   *
   * @param int $post_id
   * @since 1.0.0
   */

  private function pre_save ($post_id) {
    // Can't proceed without a post id.
    if (is_null($post_id)) {
      return;
    }

    // Data to save.
    $data = array(
      '_wp_page_template' => $this->get_page_template($_POST)
    );

    // Get the page type.
    $page_type = $this->get_page_type($_POST);
    $page_type_key = _ptb_get_page_type_meta_key();
    $data[$page_type_key] = $page_type;

    foreach ($data as $key => $value) {
      // Get the existing value if we have any.
      $meta_value = get_post_meta($post_id, $key, true);

      if (is_null($meta_value)) {
        // Add post meta key and value.
        add_post_meta($post_id, $key, $value, true);
      } else if (!is_null($meta_value) && !is_null($value)) {
        // Update post meta key and value.
        update_post_meta($post_id, $key, $value);
      } else {
        // Delete post meta row.
        delete_post_meta($post_id, $key);
      }
    }
  }

  /**
   * Change the page template value in WordPress post data
   * before it saving it. WordPress won't save page template
   * because it don't exists as a page template in the theme.
   *
   * We only set the page template value to the our page template
   * to prevent WordPress from saving it.
   *
   * @param array $data
   * @since 1.0.0
   *
   * @return array
   */

  public function wp_insert_post_data ($data) {
    if (!_ptb_is_method('post')) {
      return $data;
    }

    $page_template = $this->get_page_template($_POST);

    // Set the page template to our page template only to prevent WordPress from saving it.
    if (!is_null($page_template) || !empty($page_template)) {
      $data['page_template'] = $page_template;
    }

    return $data;
  }

  /**
   * Save meta boxes.
   *
   * @since 1.0.0
   */

  public function save_meta_boxes () {
    // Fetch the post id.
    if (isset($_POST['post_ID'])) {
      $post_id = $_POST['post_ID'];
    }

    // Can't proceed without a post id.
    if (!isset($post_id) || empty($post_id)) {
      return;
    } else {
      $post = get_post($post_id);
    }

    // Can't proceed without a post id or a post.
    if (!isset($post) || empty($post)) {
      return;
    }

    // Don't save meta boxes for revisions or autosaves
    if (defined('DOING_AUTOSAVE') || is_int(wp_is_post_revision($post)) || is_int(wp_is_post_autosave($post))) {
      return;
    }

    // Check if our nonce is vailed.
    if (empty($_POST['ptb_meta_nonce']) || !wp_verify_nonce($_POST['ptb_meta_nonce'], 'ptb_save_data')) {
      return;
    }

    // Check the post being saved has the same id as the post id. This will prevent other save post events.
    if (empty($_POST['post_ID']) || $_POST['post_ID'] != $post_id) {
      return;
    }

    // Convert post id to int if is a string.
    if (is_string($post_id)) {
      $post_id = intval($post_id);
    }

    // Check the user's permissions.
    // Todo, check custom post types.
    if (isset($_POST['post_type']) && in_array(strtolower($_POST['post_type']), array('page', 'post'))) {
      if (!current_user_can('edit_' . strtolower($_POST['post_type']), $post_id)) {
        return;
      }
    }

    // Get properties data.
    $data = $this->get_properties_data();

    // Prepare property data.
    $data = $this->prepare_properties_data($data);

    // Pre save page template and page type.
    $this->pre_save($post_id);

    // Save, update or delete all fields.
    foreach ($data as $key => $property) {

      // Property data.
      $property_key = _ptb_property_key($key);
      $property_value = $property['value'];

      // Property type data.
      $property_type_key = _ptb_property_type_key(_f($key)); // has to remove '_' + key also.
      $property_type_value = $property['type'];

      // Get the existing value if we have any.
      $meta_value = get_post_meta($post_id, $property_key, true);

      if (is_null($meta_value)) {
        // Add the property data if we have any.
        if (!is_null($property_value)) {
          add_post_meta($post_id, $property_key, $property_value, true);
        }

        // Add the property data type if we have any.
        if (!is_null($property_type_value)) {
          add_post_meta($post_id, $property_type_key, $property_type_value, true);
        }
      } else if (!is_null($meta_value) && !is_null($property_value)) {
        // Update the property data.
        update_post_meta($post_id, $property_key, $property_value);

        // Update the property type data if we have any.
        if (!is_null($property_type_value)) {
          update_post_meta($post_id, $property_type_key, $property_type_value);
        }
      } else {
        // Delete property.
        delete_post_meta($post_id, $property_key);

        // Delete property type.
        delete_post_meta($post_id, $property_type_key);
      }
    }

  }
}
