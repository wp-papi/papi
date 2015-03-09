<?php

// Exit if accessed directly
defined( 'ABSPATH' ) || exit;

/**
 * Papi Property Editor.
 *
 * @package Papi
 * @since 1.2.0
 */

class Papi_Property_Editor extends Papi_Property_Text {

	/**
	 * Get default settings.
	 *
	 * @since 1.2.0
	 *
	 * @return array
	 */

	public function get_default_settings() {
		return array(
			'editor' => true
		);
	}

	/**
	 * Format the value of the property before we output it to the application.
	 *
	 * @param mixed $value
	 *
	 * @since 1.2.2
	 *
	 * @return array
	 */

	public function format_value( $value, $slug, $post_id ) {
		return apply_filters( 'the_content', $value );
	}

}
