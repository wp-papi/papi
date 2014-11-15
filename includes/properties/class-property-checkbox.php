<?php

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Papi - Property Checkbox
 *
 * @package Papi
 * @version 1.0.0
 */
class PropertyCheckbox extends Papi_Property {

	/**
	 * Generate the HTML for the property.
	 *
	 * @since 1.0.0
	 */

	public function html() {
		// Property options.
		$options = $this->get_options();

		// Database value. Can be null.
		$value = $this->get_value();

		// Property settings from the page type.
		$settings = $this->get_settings( array(
			'items'    => array(),
			'selected' => array()
		) );

		// Override selected setting with
		// database value if not empty.
		if ( ! empty( $value ) ) {
			$settings->selected = $value;
		}

		// Selected setting need to be an array.
		if ( ! is_array( $settings->selected ) ) {
			$settings->selected = array( $settings->selected );
		}

		foreach ( $settings->items as $key => $value ) {

			if ( is_numeric( $key ) ) {
				$key = $value;
			}

			?>
			<input type="checkbox" value="<?php echo $value; ?>"
			       name="<?php echo $options->slug; ?>[]" <?php echo in_array( $value, $settings->selected ) ? 'checked="checked"' : ''; ?> />
			<?php
			echo $key . '<br />';
		}
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
	 * @return array
	 */

	public function format_value( $value, $slug, $post_id ) {
		if ( is_string( $value ) ) {
			return array( $value );
		}

		if ( is_array( $value ) ) {
			return $value;
		}

		return null;
	}

}
