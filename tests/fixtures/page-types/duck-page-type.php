<?php

class Duck_Page_Type extends Papi_Page_Type {

	/**
	 * Define our Page Type meta data.
	 *
	 * @return array
	 */
	public function meta() {
		return [
			'name'        => 'Duck page',
			'description' => 'This is a duck page',
			'template'    => 'pages/duck-page.php',
			'post_type'   => 'duck'
		];
	}

	/**
	 * Define our properties.
	 */
	public function register() {
	}
}
