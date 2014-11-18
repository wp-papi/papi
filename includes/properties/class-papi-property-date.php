<?php

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Papi - Property Date
 *
 * @package Papi
 * @version 1.0.0
 */
class Papi_Property_Date extends Papi_Property {

	/**
	 * Generate the HTML for the property.
	 *
	 * @since 1.0.0
	 */

	public function html() {
		$options = $this->get_options();
		$value   = $this->get_value();

		if ( is_integer( $value ) ) {
			$value = date( 'Y-m-d', $value );
		}

		?>
		<input type="text" name="<?php echo $options->slug; ?>" value="<?php echo $value; ?>" data-papi-property="date"/>
	<?php
	}

	/**
	 * Convert value to integer.
	 *
	 * @param string $value
	 * @param string $slug
	 * @param int $post_id
	 *
	 * @since 1.0.0
	 *
	 * @return int
	 */

	public function format_value( $value, $slug, $post_id ) {
		return intval( $value );
	}

	/**
	 * Save the date as Unix timestamp.
	 *
	 * @param string $value
	 * @param string $slug
	 * @param int $post_id
	 *
	 * @since 1.0.0
	 *
	 * @return int
	 */

	public function update_value( $value, $slug , $post_id ) {
		return strtotime( $value );
	}

}
