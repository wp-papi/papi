<?php

// Exit if accessed directly
defined( 'ABSPATH' ) || exit;

/**
 * Papi Property Editor.
 *
 * @package Papi
 * @since 1.2.0
 */

class Papi_Property_Editor extends Papi_Property {

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

	/**
	 * Display property html.
	 *
	 * @since 1.3.0
	 */

	public function html() {
		$options  = $this->get_options();
		$value    = $this->get_value();

		$id = str_replace( '[', '', str_replace( ']', '', $options->slug ) ) . '-' . uniqid();

		wp_editor( $value, $id, array(
			'textarea_name' => $options->slug,
			'media_buttons' => true
		) );

		if ( defined( 'DOING_AJAX' ) && DOING_AJAX ) {
			add_filter( 'mce_external_plugins', '__return_empty_array' );
		}
	}

}
