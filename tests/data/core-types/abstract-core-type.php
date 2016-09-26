<?php

if ( ! class_exists( 'Base_Core_Type' ) ) {
	require_once __DIR__ . '/base-core-type.php';
}

class Abstract_Core_Type extends Base_Core_Type {

	/**
	 * Define our Core Type meta data.
	 *
	 * @return array
	 */
	public function meta() {
		return [
			'name'        => 'Abstract core',
			'description' => 'This is a abstract core'
		];
	}
}
