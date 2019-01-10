<?php

class Book2_Page_Type extends Papi_Page_Type {

	/**
	 * Define our Page Type meta data.
	 *
	 * @return array
	 */
	public function meta() {
		return [
			'name'               => 'Book2 page',
			'description'        => 'This is a book2 page',
			'template'           => 'pages/book2-page.php',
			'standard_type'      => false,
			'post_type'          => 'book2',
			'tags'               => ['Book'],
		];
	}

	/**
	 * Define our properties.
	 */
	public function register() {
		$this->box( 'Info', papi_property( [
			'type'  => 'string',
			'title' => 'Book name'
		] ) );
	}
}
