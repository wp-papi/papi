<?php

// Exit if accessed directly
defined( 'ABSPATH' ) || exit;

/**
 * Papi Property Image.
 *
 * @package Papi
 */

class Papi_Property_Image extends Papi_Property_File {

	/**
	 * File type.
	 *
	 * @var string
	 */

	protected $file_type  = 'image';

	/**
	 * Format the value of the property before it's returned to the theme.
	 *
	 * @param mixed $value
	 * @param string $slug
	 * @param int $post_id
	 *
	 * @return mixed
	 */

	public function format_value( $value, $slug, $post_id ) {
		$value = parent::format_value( $value, $slug, $post_id );

		if ( is_object( $value ) ) {
			$value->alt = trim( strip_tags( get_post_meta( $value, '_wp_attachment_image_alt', true ) ) );
		}

		return $value;
	}

	/**
	 * Get labels.
	 *
	 * @return array
	 */

	public function get_labels() {
		return [
			'add'     => __( 'Add image', 'papi' ),
			'no_file' => __( 'No image selected', 'papi' )
		];
	}

}
