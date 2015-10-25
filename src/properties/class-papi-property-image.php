<?php

/**
 * WordPress media image property.
 */
class Papi_Property_Image extends Papi_Property_File {

	/**
	 * File type.
	 *
	 * @var string
	 */
	protected $file_type  = 'image';

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
