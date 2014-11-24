<?php

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Papi Property Bool.
 *
 * @package Papi
 * @version 1.0.0
 */

class Papi_Property_Bool extends Papi_Property {

	/**
	 * The default value.
	 *
	 * @var bool
	 * @since 1.0.0
	 */

	public $default_value = false;

	/**
	 * Generate the HTML for the property.
	 *
	 * @since 1.0.0
	 */

	public function html() {
		// Property options.
		$options = $this->get_options();

		// Database value.
		$value = $this->get_value();

		?>
		<input type="checkbox"
		       name="<?php echo $options->slug; ?>" <?php echo empty( $value ) ? '' : 'checked="checked"'; ?> />
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
	 * @return boolean
	 */

	public function format_value( $value, $slug, $post_id ) {
		return ! empty( $value );
	}

}
