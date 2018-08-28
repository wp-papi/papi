<?php

class Image_Module_Type extends Papi_Module_Type {

	/**
	 * Define our Page Type meta data.
	 *
	 * @return array
	 */
	public function meta() {
		return [
			'name'        => 'Image module',
			'description' => 'This is a top module',
			'template'    => [
				'Layout A' => 'layout-a.php',
				'Layout B' => 'layout-b.php'
			]
		];
	}

	/**
	 * Define our properties.
	 */
	public function register() {
	}
}
