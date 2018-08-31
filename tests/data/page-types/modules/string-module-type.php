<?php

class String_Module_Type extends Papi_Module_Type {

	/**
	 * Define our Page Type meta data.
	 *
	 * @return array
	 */
	public function meta() {
		return [
			'name'        => 'String module',
			'description' => 'This is a string module',
			'template'    => [
				'string-a.php',
				'string-b.php'
			]
		];
	}

	/**
	 * Define our properties.
	 */
	public function register() {
	}
}
