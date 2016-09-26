<?php

if ( ! class_exists( 'Base_Entry_Type' ) ) {
	require_once __DIR__ . '/base-entry-type.php';
}

class Abstract_Entry_Type extends Base_Entry_Type {

	/**
	 * Define our Entry Type meta data.
	 *
	 * @return array
	 */
	public function meta() {
		return [
			'name'        => 'Abstract entry',
			'description' => 'This is a abstract entry'
		];
	}

	/**
	 * Define our properties.
	 */
	public function register() {
		$this->register_content_box();
	}
}
