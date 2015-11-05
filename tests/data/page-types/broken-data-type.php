<?php

class Book_Data_Type extends Papi_Data_Type {

	/**
	 * Define our Data Type meta data.
	 *
	 * @return array
	 */
	public function page_type() {
		return [
			'name' => 'Book data type'
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
