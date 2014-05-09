<?php

// Exit if accessed directly
if (!defined('ABSPATH')) exit;

/**
 * Page Type Builder Meta Boxes.
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
        // Sanitize the text.
        $data[$key] = sanitize_text_field($_POST[$key]);

        // Remove trailing quotes.
        $data[$key] = remove_trailing_quotes($data[$key]);
      }
    }
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
      $property_type_key = _ptb_property_type_key();

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

    //
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
    }

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
    $page_type = h($data['ptb_page_type'], '');
    return _ptb_get_template($page_type);
  }

  /**
   * Save meta boxes.
   *
   * @param int $post_id
   * @param object $post
   * @since 1.0.0
   */

  public function save_meta_boxes ($post_id, $post) {
    // Can't proceed without a post id or a post.
    if (empty($post_id) || empty($post)) {
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
    if (empty($_POST['post_id']) || $_POST['post_id'] != $post_id) {
      return;
    }

    // Check the user's permissions.
    if (!current_user_can('edit_post', $post_id)) {
      return;
    }

    // Get properties data.
    $data = $this->get_properties_data();

    // Prepare property data.
    $data = $this->prepare_properties_data($data);

    // Save, update or delete all fields.
    foreach ($data as $key => $property) {

      // Property data.
      $property_key = _ptb_property_key($key); // a function that will return '_' + key
      $property_value = $data['value'];

      // Property type data.
      $property_type_key = _ptb_property_type_key($key); // has to remove '_' + key also.
      $property_type_value = $data['type'];

      $meta_value = get_post_meta($post_id, $property_key, true);

      if (is_null($meta_value)) {
        // Add the property data.
        add_post_meta($post_id, $property_key, $property_value, true);

        // Add the property data type.
        add_post_meta($post_id, $property_type_key, $property_type_value, true);
      } else if (!is_null($meta_value) && !is_null($property_value)) {
        update_post_meta($post_id, $property_key, $property_value);
        update_post_meta($post_id, $property_type_key, $property_type_value);
      } else {
        delete_post_meta($post_id, $property_key, $property_value);
        delete_post_meta($post_id, $property_type_key, $property_type_value);
      }

      // add '_wp_page_template' also.
    }

    /*

    array(
      'x' => array(
        'type'  => 'PropertyString',
        'value' => 'En rubrik'
      )
    )

    */

  }

  public function save_page_template ($action = 'add', $value = '') {
    switch ($action) {
      case 'add':
    }
  }
}