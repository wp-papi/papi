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

}
