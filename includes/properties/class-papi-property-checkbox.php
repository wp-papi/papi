<?php

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Papi Property Checkbox.
 *
 * @package Papi
 * @version 1.0.0
 */

class Papi_Property_Checkbox extends Papi_Property {

	/**
	 * The default value.
	 *
	 * @var array
	 * @since 1.0.0
	 */

	public $default_value = array();

	/**
	 * Get default settings.
	 *
	 * @since 1.0.0
	 *
	 * @return array
	 */

	public function get_default_settings() {
		return array(
			'items'    => array(),
			'selected' => array()
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
		if ( is_string( $value ) && ! empty( $value ) ) {
			return array( $value );
		}

		if ( ! is_array( $value ) ) {
			return $this->default_value;
		}

		return $value;
	}

}
