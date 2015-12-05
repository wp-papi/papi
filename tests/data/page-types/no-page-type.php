<?php

class No_Page_Type extends Papi_Page_Type_Meta {

	/**
	 * Define our Page Type meta data.
	 *
	 * @return array
	 */
	public function meta() {
		return [
			'name'        => 'No page',
			'description' => 'This is a no page',
			'template'    => 'pages/no-page.php'
		];
	}

	/**
	 * Define our properties.
	 */
	public function register() {
	}
}
