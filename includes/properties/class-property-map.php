<?php

// Exit if accessed directly
if (!defined('ABSPATH')) exit;

/**
 * Page Type Builder - Property Google Map
 *
 * @package PageTypeBuilder
 * @version 1.0.0
 */

class PropertyMap extends PTB_Property {

  /**
   * Generate the HTML for the property.
   *
   * @since 1.0.0
   */

  public function html () {
    // Property options.
    $options = $this->get_options();

    ?>
    <style type="text/css">
      .map-canvas {
        width: 100%;
        height: 400px;
      }
    </style>
    <div id="<?php echo $options->slug; ?>" class="map-canvas"></div>
    <?php
  }

  /**
   * Output custom JavaScript for the property.
   *
   * @since 1.0.0
   * @throws PTB_Exception
   */

  public function js () {
    // Property options.
    $options = $this->get_options();

    // Property settings.
    $settings = $this->get_settings(array(
      'api_key' => '',
      'latlng'  => ''
    ));

    if (empty($settings->api_key)) {
      return;
    }

    $api_key = $settings->api_key;

    ?>
    <script type="text/javascript" src="https://maps.googleapis.com/maps/api/js?key=<?php echo $api_key; ?>&sensor=false"></script>
    <script type="text/javascript">
      function updateLatitudeLangitude (position) {
        var el = document.querySelectorAll('input[name="<?php echo $options->slug; ?>"]');
        if (el.length) {
          el[0].value = [position.lat(), position.lng()].join(', ');
        }
      }

      function initialize() {
        <?php

          // Database value.
          $value = $this->get_value();

          if (is_null($value) || empty($value)) {
            if (!empty($settings->latlng)) {
              $value = explode(',', trim($settings->latlng));
              $lat = $value[0];
              $lng = $value[1];
            } else {
              $lat = '59.32893';
              $lng = '18.06491';
            }
          } else {
            $value = explode(',', trim($value));
            $lat = $value[0];
            $lng = $value[1];
          }
        ?>
        var ptbLatLng = new google.maps.LatLng(<?php echo $lat; ?>, <?php echo $lng; ?>);

        var mapOptions = {
          center: ptbLatLng,
          zoom: 14
        };

        var map = new google.maps.Map(document.getElementById("<?php echo $options->slug; ?>"), mapOptions);

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
   * Generate the html for the "place" input.
   *
   * @since 1.0.0
   */

  public function input () {
    $options = $this->get_options();
    ?>
    <input type="text" name="<?php echo $options->slug; ?>" value="<?php echo $options->value; ?>" class="ptb-fullwidth ptb-property-map-input" placeholder="<?php _e('Latitude and longitude'); ?>"  />
    <?php
  }

  /**
   * Render the final html that is displayed in the table.
   *
   * @since 1.0.0
   */

  public function render () {
    $options = $this->get_options();
    ?>
      <tr>
        <td>
          <?php
            $this->label();
            $this->helptext();
          ?>
        </td>
        <td>
          <?php
            $this->input();
            $this->html();
          ?>
        </td>
      </tr>
    <?php
  }
}