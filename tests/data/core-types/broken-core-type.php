<?php

class Book_Core_Type extends Papi_Core_Type {

	/**
	 * Define our Core Type meta data.
	 *
	 * @return array
	 */
	public function meta() {
		return [
			'name' => 'Book core type'
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
