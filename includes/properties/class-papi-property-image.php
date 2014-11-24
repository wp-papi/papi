<?php

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Papi Property Image.
 *
 * @package Papi
 * @version 1.0.0
 */

class Papi_Property_Image extends Papi_Property {

	/**
	 * The default value.
	 *
	 * @var null
	 * @since 1.0.0
	 */

	public $default_value = null;

	/**
	 * Get default settings.
	 *
	 * @since 1.0.0
	 *
	 * @return array
	 */

	public function get_default_settings() {
		return array(
			'gallery' => false
		);
	}

	/**
	 * Generate the HTML for the property.
	 *
	 * @since 1.0.0
	 */

	public function html() {
		$options  = $this->get_options();
		$settings = $this->get_settings();
		$value    = _papi_to_array( $this->get_value() );

		$value    = array_filter( $value, function ( $image ) {
			return is_object( $image );
		});

		$slug        = $options->slug;
		$show_button = empty( $value );
		$css_classes = '';

		if ( $settings->gallery ) {
			$css_classes .= ' gallery ';
			$slug .= '[]';
			$show_button = true;
		}

		?>

		<script type="text/template" id="tmpl-papi-image">
			<a href="#" data-papi-options='{"id":"<%= id %>"}'>x</a>
			<img src="<%= image %>"/>
			<input type="hidden" value="<%= id %>" name="<%= slug %>"/>
		</script>

		<div class="wrap papi-property-image <?php echo $css_classes; ?>">
			<p class="papi-image-select <?php echo $show_button ? '' : 'hidden'; ?>">
				<?php
				if ( ! $settings->gallery ) {
					_e( 'No image selected', 'papi' );
				}
				?>
				<button class="button"
				        data-slug="<?php echo $slug; ?>"><?php _e( 'Add image', 'papi' ); ?></button>
			</p>
			<ul>
				<?php
				if ( is_array( $value ) ):
					foreach ( $value as $key => $image ):
						$url = wp_get_attachment_thumb_url( $image->id );
						?>
						<li>
							<a href="#">x</a>
							<img src="<?php echo $url; ?>"/>
							<input type="hidden" value="<?php echo $image->id; ?>" name="<?php echo $slug; ?>"/>
						</li>
					<?php
					endforeach;
				endif;
				?>
			</ul>
		</div>

	<?php
	}

	/**
	 * Format the value of the property before we output it to the application.
	 *
	 * @param mixed $value
	 * @param string $slug
	 * @param int $post_id
	 *
	 * @since 1.0.0
	 *
	 * @return mixed
	 */

	public function format_value( $value, $slug, $post_id ) {
		if ( is_numeric( $value ) ) {
			$meta = wp_get_attachment_metadata( $value );
			if ( isset( $meta ) && ! empty( $meta ) ) {
				$mine = array(
					'is_image' => true,
					'url'      => wp_get_attachment_url( $value ),
					'id'       => intval( $value )
				);

				return (object) array_merge( $meta, $mine );
			} else {
				return $value;
			}
		} else if ( is_array( $value ) ) {
			foreach ( $value as $k => $v ) {
				$value[ $k ] = $this->format_value( $v, $slug, $post_id );
			}

			return $value;
		} else {
			return $value;
		}
	}
}
