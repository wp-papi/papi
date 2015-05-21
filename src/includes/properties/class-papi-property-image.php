<?php

// Exit if accessed directly
defined( 'ABSPATH' ) || exit;

/**
 * Papi Property Image.
 *
 * @package Papi
 * @since 1.0.0
 */

class Papi_Property_Image extends Papi_Property {

	/**
	 * Format the value of the property before it's returned to the theme.
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
				$mine = [
					'alt'         => trim( strip_tags( get_post_meta( $value, '_wp_attachment_image_alt', true ) ) ),
					'caption'     => trim( strip_tags( $att->post_excerpt ) ),
					'description' => trim( strip_tags( $att->post_content ) ),
					'id'          => intval( $value ),
					'is_image'    => wp_attachment_is_image( $value ),
					'title'       => $att->post_title,
					'url'         => wp_get_attachment_url( $value ),
				];

				return (object) array_merge( $meta, $mine );
			} else {
				return $value;
			}
		} else if ( is_array( $value ) ) {
			foreach ( $value as $k => $v ) {
				$value[$k] = $this->format_value( $v, $slug, $post_id );
			}

			return $value;
		} else {
			return $value;
		}
	}

	/**
	 * Get default settings.
	 *
	 * @since 1.0.0
	 *
	 * @return array
	 */

	public function get_default_settings() {
		return [
			'gallery' => false
		];
	}

	/**
	 * Get default value.
	 *
	 * @since 1.3.0
	 *
	 * @return stdClass
	 */

	public function get_default_value() {
		return new stdClass;
	}

	/**
	 * Display property html.
	 *
	 * @since 1.0.0
	 */

	public function html() {
		$settings = $this->get_settings();
		$value    = papi_to_array( $this->get_value() );

		// Keep only valid objects.
		$value = array_filter( $value, function ( $item ) {
			return is_object( $item ) && isset( $item->id ) && ! empty( $item->id );
		} );

		$slug        = $this->html_name();
		$show_button = empty( $value );
		$css_classes = '';

		if ( $settings->gallery ) {
			$css_classes .= ' gallery ';
			$slug .= '[]';
			$show_button = true;
		}
		?>

		<div class="papi-property-image <?php echo $css_classes; ?>">
			<p class="papi-image-select <?php echo $show_button ? '' : 'papi-hide'; ?>">
				<input type="hidden" value="" name="<?php echo $slug; ?>"/>
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
	 * Render image template.
	 * that will be used in image backbone view.
	 *
	 * @since 1.3.0
	 */

	public function render_image_template() {
		?>
		<script type="text/template" id="tmpl-papi-property-image">
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
		<?php
	}

	/**
	 * Setup actions.
	 *
	 * @since 1.3.0
	 */

	protected function setup_actions() {
		add_action( 'admin_head', [$this, 'render_image_template'] );
	}
}
