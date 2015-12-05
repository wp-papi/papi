<?php

class Empty_Page_Type extends Papi_Page_Type {

	/**
	 * Define our Page Type meta data.
	 *
	 * @return array
	 */
	public function meta() {
		return [
			'name'        => 'Empty page',
			'description' => 'This is a empty page',
			'template'    => 'pages/empty-page.php'
		];
	}

	/**
	 * Define our properties.
	 */
	public function register() {
	}
}
