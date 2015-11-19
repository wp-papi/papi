<?php

class Book_Content_Type extends Papi_Content_Type {

	/**
	 * Define our content type meta data.
	 *
	 * @return array
	 */
	public function page_type() {
		return [
			'name' => 'Book content type'
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
