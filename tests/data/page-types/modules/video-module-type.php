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
			'description' => 'This is a video module',
			'template'    => [
				[
					'template' => 'video-b.php',
					'label'    => 'Video B',
				],
				[
					'template' => 'video-a.php',
					'label'    => 'Video A',
					'default'  => true
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
