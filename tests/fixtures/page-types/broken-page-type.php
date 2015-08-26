<?php

class Broken_Page_Type extends Papi_Page_Type {

	/**
	 * Change meta method to look for.
	 *
	 * @var string
	 */

	public $_meta_method = 'page_type_meta';

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
}
