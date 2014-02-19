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
    return '<div id="map-canvas"></div>';
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
   *
   * @throws Exception
   */
  
  public function js () {
    if (isset($this->get_options()->special) && isset($this->get_options()->special->api_key)) {
      $api_key = $this->get_options()->special;
    } else {
      throw new Exception('You need to provide a api key for PropertyMap since we are using Google Maps');
    }
    ?>
    <script type="text/javascript" src="https://maps.googleapis.com/maps/api/js?key=<?php echo $api_key; ?>&sensor=false"></script>
    <script type="text/javascript">
      function updateLatitudeLangitude (position) {
        var el = document.querySelectorAll('input#<?php echo $this->get_options()->name; ?>');
        if (el.length) {
          el[0].value = [position.lat(), position.lng()].join(', ');
        }
      }
      
      function initialize() {
        <?php 
          if (is_null($this->get_options()->value) || empty($this->get_options()->value)) {
            $lat = '59.32893';
            $lng = '18.06491';
          } else {
            $value = explode(',', trim($this->get_options()->value));
            $lat = $value[0];
            $lng = $value[1];
          }
        ?>
        var ptbLatLng = new google.maps.LatLng(<?php echo $lat; ?>, <?php echo $lng; ?>);
        
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
    return PTB_Html::input('text', array(
      'name' => $this->get_options()->name,
      'id' => $this->get_options()->name,
      'class' => 'ptb-halfwidth',
      'value' => $this->get_options()->value
    ));
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