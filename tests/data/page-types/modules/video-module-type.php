<?php

class Video_Module_Type extends Papi_Module_Type {

	/**
	 * Define our Page Type meta data.
	 *
	 * @return array
	 */
	public function meta() {
		return [
			'name'        => 'Video module',
			'description' => 'This is a top module',
			'template'    => [
				'Video A' => 'video-a.php',
				'Video B' => 'video-b.php'
			]
		];
	}

	/**
	 * Define our properties.
	 */
	public function register() {
	}
}
