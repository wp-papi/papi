<?php

// Exit if accessed directly
if (!defined('ABSPATH')) exit;

/**
 * Page Type Builder - Property Google Map
 */

class PropertyMap extends PTB_Property {

  /**
   * Get the html for output.
   *
   * @since 1.0
   *
   * @return string
   */

  public function html () {
    return PTB_Html::input('text', array(
      'name' => $this->get_options()->name,
      'value' => $this->get_options()->value
    ));
  }
  
  /**
   * Output custom css for the property.
   *
   * @since 1.0
   */
  
  public function css () {
    echo '<style type="text/css">';
    echo '#map-canvas { height: 400px; }';
    echo '</style>';
  }
  
  /**
   * Output custom JavaScript for the property.
   *
   * @since 1.0
   */
  
  public function js () {
    echo '<script type="text/javascript" src="https://maps.googleapis.com/maps/api/js?key=API_KEY&sensor=false"></script>';
    ?>
    <script type="text/javascript"></script>
    <?php
  }

}