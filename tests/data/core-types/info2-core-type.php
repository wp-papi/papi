<?php

if ( ! class_exists( 'Info_Core_Type' ) ) {
	require_once __DIR__ . '/info-core-type.php';
}

class Info2_Core_Type extends Info_Core_Type {

	/**
	 * Define our Core Type meta data.
	 *
	 * @return array
	 */
	public function meta() {
		return [
			'name'        => 'Info2 core type',
			'description' => 'This is a Info2 core type'
		];
	}

}
