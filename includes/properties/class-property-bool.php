<?php

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Papi - Property Bool
 *
 * @package Papi
 * @version 1.0.0
 */
class PropertyBool extends Papi_Property {

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
		       name="<?php echo $options->slug; ?>" <?php echo empty( $value ) ? '' : 'checked="checked"'; ?>
		       class="<?php echo $this->css_classes(); ?>"/>
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
