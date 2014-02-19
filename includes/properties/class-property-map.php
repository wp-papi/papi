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
    $html = '<div id="map-canvas"></div>';
    return $html;
  }
  
  /**
   * Output custom css for the property.
   *
   * @since 1.0
   */
  
  public function css () {
    echo '<style type="text/css">';
    echo '#map-canvas { width: 100%; height: 400px; }';
    echo '</style>';
  }
  
  /**
   * Output custom JavaScript for the property.
   *
   * @since 1.0
   */
  
  public function js () {
    ?>
    <script type="text/javascript" src="https://maps.googleapis.com/maps/api/js?key=AIzaSyCYn-cYmKSOx290fSSvNDugi-U6qpJZe60&sensor=false"></script>
    <script type="text/javascript">
      function updateLatitudeLangitude (position) {
        var el = document.getElementById('ptb_map_cord');
        el.value = [position.lat(), position.lng()].join(',');
      }
      
      function initialize() {
        var ptbLatLng = new google.maps.LatLng(59.32893, 18.06491);
        
        var mapOptions = {
          center: ptbLatLng,
          zoom: 14
        };
        
        var map = new google.maps.Map(document.getElementById("map-canvas"), mapOptions);
        
        var marker = new google.maps.Marker({
          position: ptbLatLng,
          map: map,
          draggable: true,
          title: 'Select position'
        });
        
        google.maps.event.addListener(marker, 'drag', function() {
          updateLatitudeLangitude(marker.getPosition());
        });
        
        google.maps.event.addListener(marker, 'dragend', function() {
          updateLatitudeLangitude(marker.getPosition());
        });
      }
      google.maps.event.addDomListener(window, 'load', initialize);
    </script>
    <?php
  }
  
  /**
   * Get the input html for the "place" input.
   *
   * @since 1.0
   *
   * @return string
   */
  
  public function input () {
    return '<input type="text" name="ptb_map_cord" id="ptb_map_cord" class="ptb-halfwidth" />';
  }

  /**
   * Render the final html that is displayed in the table.
   *
   * @since 1.0
   *
   * @return string
   */

  public function render () {
    $html = PTB_Html::td($this->html(), array('colspan' => 2));
    $html = PTB_Html::tr($html);
    $label = PTB_Html::td($this->label());
    $label .= PTB_Html::td($this->input());
    $html .= PTB_Html::tr($label);
    return $html;
  }

}