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
}
