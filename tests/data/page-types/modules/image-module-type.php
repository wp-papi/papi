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
			'description' => 'This is a image module',
			'template'    => [
				[
					'template' => 'layout-a.php',
					'label'    => 'Layout A'
				],
				[
					'template' => 'layout-b.php',
					'label'    => 'Layout B'
				]
			]
		];
	}

	/**
	 * Define our properties.
	 */
	public function register() {
	}
}
