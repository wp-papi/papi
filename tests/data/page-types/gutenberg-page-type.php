<?php

class Gutenberg_Page_Type extends Papi_Page_Type {

	/**
	 * Define our Page Type meta data.
	 *
	 * @return array
	 */
	public function meta() {
		return [
			'name'        => 'Gutenberg page',
			'description' => 'This is a gutenberg page',
			'template'    => 'pages/gutenberg-page.php',
		];
	}

	/**
	 * Define our properties.
	 */
	public function register() {
		$this->block( 'Info', papi_property( [
			'type'  => 'string',
			'title' => 'Book name'
		] ) );
	}
}
