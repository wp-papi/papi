<?php

class Info_Content_Type extends Papi_Content_Type {

	/**
	 * Define our content type meta data.
	 *
	 * @return array
	 */
	public function meta() {
		return [
			'name'        => 'Info content type',
			'description' => 'This is a Info content type',
			'sort_order'  => 500
		];
	}

	/**
	 * Define our properties.
	 */
	public function register() {
		$this->box( 'Info', papi_property( [
			'type'  => 'string',
			'title' => 'Info'
		] ) );
	}
}
