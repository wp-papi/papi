<?php

// Exit if accessed directly
if (!defined('ABSPATH')) exit;

/**
 * Unit tests covering property functionality.
 *
 * @package PageTyepBuilder
 */

class WP_PTB_Property extends WP_UnitTestCase {

  public function test_render_property () {
    $property = _ptb_get_property_options(array(
      'type'  => 'PropertyString',
      'title' => 'Heading',
      'slug'  => 'heading'
    ));

    _ptb_render_property($property);
  }

}