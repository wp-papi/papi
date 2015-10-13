<?php

class Identifier_Page_Type extends Papi_Page_Type {

	/**
	 * Define our Page Type meta data.
	 *
	 * @return array
	 */
	public function page_type() {
		return [
			'id'          => 'custom-page-type-id',
			'name'        => 'Identifier page',
			'description' => 'This is a identifier page',
			'template'    => 'pages/identifier-page.php'
		];
	}

	/**
	 * Define our properties.
	 */
	public function register() {
	}
}
