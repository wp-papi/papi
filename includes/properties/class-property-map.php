<?php

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Papi - Property Google Map
 *
 * @package Papi
 * @version 1.0.0
 */
class PropertyMap extends Papi_Property {

	/**
	 * Generate the HTML for the property.
	 *
	 * @since 1.0.0
	 */

	public function html() {
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
	 */

	public function js() {
		// Property options.
		$options = $this->get_options();

		// Property settings.
		$settings = $this->get_settings( array(
			'api_key' => '',
			'latlng'  => ''
		) );

		if ( empty( $settings->api_key ) ) {
			return;
		}

		?>
		<script type="text/javascript"
		        src="https://maps.googleapis.com/maps/api/js?key=<?php echo $settings->api_key; ?>&sensor=false"></script>
		<script type="text/javascript">
			<?php
				// Database value.
				$value = $this->get_value();

				if (isset($value['lat']) && isset($value['lng'])) {
				  $lat = $value['lat'];
				  $lng = $value['lng'];
				} else {
				  if (!empty($settings->latlng)) {
					$value = explode(',', trim($settings->latlng));
					$lat = $value[0];
					$lng = $value[1];
				  } else {
					$lat = '59.32893';
					$lng = '18.06491';
				  }
				}
			  ?>

			(function ($) {

				var geocoder = new google.maps.Geocoder (), latLng = new google.maps.LatLng (<?php echo $lat; ?>, <?php echo $lng; ?>), mapOptions = {
						center: latLng, zoom: 14
					}, map, maker;

				function geocodePosition (pos) {
					geocoder.geocode ({
						latLng: pos
					}, function (responses) {
						if (responses && responses.length) {
							console.log (responses[0].formatted_address);
							updateMarkerAddress (responses[0].formatted_address);
						} else {
							updateMarkerAddress ('<?php _e("Cannot determine address at this location.", "papi"); ?>');
						}
					});
				}

				function updateLatitudeLangitude (position) {
					$ ('input[name="<?php echo $options->slug; ?>[lat]"]').val (position.lat ());
					$ ('input[name="<?php echo $options->slug; ?>[lng]"]').val (position.lng ());
				}

				function updateMarkerAddress (address) {
					$ ('input[name="<?php echo $options->slug; ?>[address]"]').val (address);
				}

				function initialize () {
					map = new google.maps.Map (document.getElementById ("<?php echo $options->slug; ?>"), mapOptions);

					marker = new google.maps.Marker ({
						position: latLng, map: map, draggable: true, title: 'Select position'
					});

					google.maps.event.addListener (marker, 'drag', function () {
						updateLatitudeLangitude (marker.getPosition ());
						geocodePosition (marker.getPosition ());
					});

					google.maps.event.addListener (marker, 'dragend', function () {
						updateLatitudeLangitude (marker.getPosition ());
						geocodePosition (marker.getPosition ());
					});
				}

				google.maps.event.addDomListener (window, 'load', initialize);

				$ ('.papi-property-map-search').on ('papi/update_map', function () {
					var $this = $ (this), val = $this.val ();

					geocoder.geocode ({
						'address': val
					}, function (results, status) {
						if (status == google.maps.GeocoderStatus.OK) {
							var loc = results[0].geometry.location;
							var latLng = new google.maps.LatLng (loc.lat (), loc.lng ());
							marker.setPosition (latLng);
							map.setCenter (latLng);
							updateLatitudeLangitude (marker.getPosition ());
						}
					});
				});

				$ ('.papi-property-map-search').on ('keypress', function (e) {
					if (e.which == 13) {
						e.preventDefault ();
						$ (this).trigger ('papi/update_map');
					}
				});

				$ ('.papi-property-map-search').on ('blur', function (e) {
					e.preventDefault ();
					$ (this).trigger ('papi/update_map');
				});

			}) (jQuery);

		</script>
	<?php
	}

	/**
	 * Generate the html for searching adress.
	 *
	 * @since 1.0.0
	 */

	public function input() {
		$options = $this->get_options();
		$value   = $this->get_value(array());

		?>
		<input type="search" name="<?php echo $options->slug; ?>[address]"
		       value="<?php echo isset( $value['address'] ) ? $value['address'] : ''; ?>" class="papi-property-map-search"
		       placeholder="<?php _e( 'Search for address...' ); ?>"/>
		<input type="hidden" name="<?php echo $options->slug; ?>[lat]"
		       value="<?php echo isset( $value['address'] ) ? $value['lat'] : ''; ?>"/>
		<input type="hidden" name="<?php echo $options->slug; ?>[lng]"
		       value="<?php echo isset( $value['address'] ) ? $value['lng'] : ''; ?>"/>
	<?php
	}

	/**
	 * Render the final html that is displayed in the table.
	 *
	 * @since 1.0.0
	 */

	public function render() {
		?>
		<tr>
			<td>
				<?php
				$this->label();
				$this->instruction();
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
