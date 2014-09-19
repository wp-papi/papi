<?php

// Exit if accessed directly
if (!defined('ABSPATH')) exit;

/**
 * Unit tests covering property functionality.
 *
 * @package Act
 */

class WP_ACT_Property extends WP_UnitTestCase {

  public function test_render_property () {
    $property = _act_get_property_options(array(
      'type'  => 'PropertyString',
      'title' => 'Heading',
      'slug'  => 'heading'
    ));

    _act_render_property($property);
  }

}
