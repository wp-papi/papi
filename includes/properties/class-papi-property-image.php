<?php

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Papi Property Image.
 *
 * @package Papi
 * @since 1.0.0
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
		$value    = papi_to_array( $this->get_value() );

		// Keep only objects.
		$value = papi_get_only_objects( $value );

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
			<a class="check" href="#" data-papi-options='{"id":"<%= id %>"}'>X</a>
			<div class="attachment-preview">
				<div class="thumbnail">
					<div class="centered">
						<img src="<%= image %>"/>
						<input type="hidden" value="<%= id %>" name="<%= slug %>"/>
					</div>
				</div>
			</div>
		</script>

		<div class="papi-property-image <?php echo $css_classes; ?>">
			<p class="papi-image-select <?php echo $show_button ? '' : 'hidden'; ?>">
				<?php
				if ( ! $settings->gallery ) {
					_e( 'No image selected', 'papi' );
				}
				?>
				<button class="button"
				        data-slug="<?php echo $slug; ?>"><?php _e( 'Add image', 'papi' ); ?></button>
			</p>
			<div class="attachments">
				<?php
				if ( is_array( $value ) ):
					foreach ( $value as $key => $image ):
						$url = wp_get_attachment_thumb_url( $image->id );
						?>
						<div class="attachment">
							<a class="check" href="#" data-papi-options='{"id":"<%= id %>"}'>X</a>
							<div class="attachment-preview">
								<div class="thumbnail">
									<div class="centered">
										<img src="<?php echo $url; ?>"/>
										<input type="hidden" value="<?php echo $image->id; ?>" name="<?php echo $slug; ?>"/>
									</div>
								</div>
							</div>
						</div>
					<?php
					endforeach;
				endif;
				?>
			</div>
			<div class="clear"></div>
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
				$att = get_post( $value );
				$mine = array(
					'alt'         => trim( strip_tags( get_post_meta( $value, '_wp_attachment_image_alt', true ) ) ),
					'caption'     => trim( strip_tags( $att->post_excerpt ) ),
					'description' => trim( strip_tags( $att->post_content ) ),
					'id'          => intval( $value ),
					'is_image'    => wp_attachment_is_image( $value ),
					'title'       => $att->post_title,
					'url'         => wp_get_attachment_url( $value ),
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
