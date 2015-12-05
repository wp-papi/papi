<?php

class Dot_Page_Type extends Papi_Page_Type {

	/**
	 * Define our Page Type meta data.
	 *
	 * @return array
	 */
	public function meta() {
		return [
			'name'         => 'Dot page',
			'description'  => 'This is a dot page',
			'template'     => 'pages.dot'
		];
	}

	/**
	 * Define our properties.
	 */
	public function register() {
	}
}
