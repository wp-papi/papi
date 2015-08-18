<?php

class Dot2_Page_Type extends Papi_Page_Type {

	/**
	 * Define our Page Type meta data.
	 *
	 * @return array
	 */

	public function page_type() {
		return [
			'name'         => 'Dot2 page',
			'description'  => 'This is a dot2 page',
			'template'     => 'pages.dot2.php'
		];
	}

	/**
	 * Define our properties.
	 */

	public function register() {
	}
}
