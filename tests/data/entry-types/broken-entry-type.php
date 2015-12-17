<?php

class Book_Entry_Type extends Papi_Entry_Type {

	/**
	 * Define our entry type meta data.
	 *
	 * @return array
	 */
	public function meta2() {
		return [
			'name' => 'Book entry type'
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
