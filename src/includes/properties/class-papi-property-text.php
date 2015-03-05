<?php

// Exit if accessed directly
defined( 'ABSPATH' ) || exit;

/**
 * Papi Property Text.
 *
 * @package Papi
 * @since 1.0.0
 */

class Papi_Property_Text extends Papi_Property {

	/**
	 * Get default settings.
	 *
	 * @since 1.0.0
	 *
	 * @return array
	 */

	public function get_default_settings() {
		return array(
			'editor' => false
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
		$value    = $this->get_value();

		// Output which type the text property acts like so we can run
		// right filter later.
		?>
			<input type="hidden" name="_<?php echo $options->slug; ?>_type" value="<?php echo $settings->editor ? 'editor' : 'text'; ?>" />
		<?php

		if ( $settings->editor ) {
			$id = str_replace( '[', '', str_replace( ']', '', $options->slug ) ) . '-' . uniqid();
			wp_editor( $value, $id, array(
				'textarea_name' => $options->slug
			) );
		} else {
			?>
			<textarea name="<?php echo $options->slug; ?>"
			          class="papi-property-text"><?php echo strip_tags( $value ); ?></textarea>
		<?php
		}
	}

	/**
	 * Format the value of the property before we output it to the application.
	 *
	 * @param mixed $value
	 * @param string $slug
	 * @param int $post_id
	 * @param bool $is_admin
	 *
	 * @since 1.2.2
	 *
	 * @return array
	 */

	public function format_value( $value, $slug, $post_id, $is_admin ) {
		if ( $is_admin ) {
			return $value;
		}

		$type = get_post_meta( $post_id, '_' . $slug . '_type', true );

		if ($type === 'editor') {
			return apply_filters( 'the_content', $value );
		}

		return $value;
	}
}
