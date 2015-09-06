<?php namespace Foo\Bar;

class Namespace_Page_Type extends \Papi_Page_Type {

	/**
	 * Define our Page Type meta data.
	 *
	 * @return array
	 */
	public function page_type() {
		return [
			'name'        => 'Namespace page',
			'description' => 'This is a namespace page',
			'template'    => 'pages/namespace-page.php'
		];
	}

	/**
	 * Define our properties.
	 */
	public function register() {
	}
}
