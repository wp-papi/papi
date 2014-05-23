<?php

/**
 * Page Type Builder Page class
 */

class PTB_Page {

  /**
   * The WordPress post.
   *
   * @var object
   * @since 1.0
   */

  private $post;

  /**
   * The Page type data.
   *
   * @var object.
   * @since 1.0
   */

  private $page_type;

  /**
   * Create a new instance of the class.
   *
   * @param int $post_id
   */

  public function __construct ($post_id = 0) {
    $this->id = $post_id;
    $this->setup_post();
    $this->setup_page();
  }

  /**
   * Setup page variables. This will not setup any variables from the WordPress post.
   *
   * @since 1.0
   */

  private function setup_page () {
    // Can't proceed if we haven't a post object.
    if (!$this->has_post()) {
      return;
    }

    // The path to the page type file.
    $path = _ptb_get_page_type_file(_ptb_get_page_type_meta_value($this->id));

    // The page type object.
    $this->page_type = _ptb_get_page_type($path);

    // Can't proceed without a page type.
    if (is_null($this->page_type)) {
      return;
    }

    // The page type name. Example: "Standard Page".
    $this->page_type_name = $this->page_type->name;

    // The page type. Example: "PTB_Standard_Page".
    $this->page_type = $this->page_type->page_type;
  }

  /**
   * Setup post variables for the WordPress post.
   *
   * @since 1.0
   */

  private function setup_post () {
    $this->post = get_post($this->id);

    if (!isset($this->post)) {
      return;
    }
    // variables

    // create ptb_get_page_type_name function in ptb-functions.php

    foreach ($this->post as $key => $value) {
      // maybe we should remove "post_" and/or remove the variables.
      // some idéas
      // page name,
      // page content
      // page data
      // created
      // created by
      $this->$key = $value;
    }

    // utility methods
    // is logged in
    // current page url
    // access
  }

  /**
   * Get Page Type Builder Property value.
   *
   * @param string $key
   * @since 1.0.0
   *
   * @return mixed
   */

  private function get_value ($key) {
    $property_key = _ptb_property_key($key);
    $property_value = get_post_meta($this->id, $property_key, true);

    if (is_null($property_value)) {
      return null;
    }

    $property_type_key = _ptb_property_type_key($property_key);
    $property_type_value = get_post_meta($this->id, $property_type_key, true);

    if (is_null($property_type_value)) {
      return null;
    }

    // The convert takes a array as argument so let's make one.
    if (!is_array($property_value)) {
      return $this->convert(array(
        'type'  => $property_type_value,
        'value' => $property_value
      ));
    }

    // Property List has array with properties.
    // Remove `ptb_` key and property key.
    foreach ($property_value as $ki => $vi) {
      if (is_array($property_value[$ki])) {
        foreach ($property_value[$ki] as $k => $v) {
          if (_ptb_is_property_type_key($k)) {
            unset($property_value[$ki][$k]);
          } else {
            $ptk = _ptb_property_type_key($k);
            $kn = _ptb_remove_ptb($k);
            $property_value[$ki][$kn] = $this->convert(array(
              'type' => $property_value[$ki][$ptk],
              'value' => $v
            ));
            unset($property_value[$ki][$k]);
          }
        }
      } else {
        $property_value[$ki] = $this->convert(array(
          'type'  => $property_type_value,
          'value' => $property_value[$ki]
        ));
      }
    }

    return array_filter($property_value);
  }

  /**
   * Convert property value with the property type converter.
   *
   * @param array $property
   * @since 1.0
   *
   * @return mixed|null
   */

  private function convert ($property = array()) {
    if (is_array($property)) {
      if (isset($property['value']) && isset($property['type'])) {
        $type = $property['type'];
        $property_type = PTB_Property::factory($type);

        // Can't convert since we don't know the property.
        if (is_null($property_type)) {
          return $property['value'];
        }

        return $property_type->convert($property['value']);
      }

      if (isset($property['value'])) {
        return $property['value'];
      }

      return $property;
    }
    return $property;
  }

  /**
   * Check if the page has the post object and that it's not null
   *
   * @since 1.0
   *
   * @return bool
   */

  public function has_post () {
    return $this->post != null;
  }

  /**
   * Get the WordPress post object.
   *
   * @since 1.0
   *
   * @return object
   */

  public function get_post () {
    return $this->post;
  }

  /**
   * Get the permalink for the page.
   *
   * @since 1.0
   *
   * @return string
   */

  public function get_permalink () {
    return get_permalink($this->id);
  }

  /**
   * Get the post status of a page.
   *
   * @since 1.0
   *
   * @return string
   */

  public function get_status () {
    return get_post_status($this->id);
  }

  /**
   * Get Page Type Builder Property value.
   *
   * @param string $key
   * @since 1.0.0
   *
   * @return mixed
   */

  public function __get ($name) {
    return $this->get_value($name);
  }

}