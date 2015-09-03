<?php

class Book_Page_Type extends Papi_Page_Type {

	/**
	 * Define our Page Type meta data.
	 *
	 * @return array
	 */
	public function page_type() {
		return [
			'name'               => 'Book page',
			'description'        => 'This is a book page',
			'template'           => 'pages/book-page.php',
			'standard_page_type' => false,
			'post_type'          => 'book'
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
