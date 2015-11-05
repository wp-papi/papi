<?php

class Info_Data_Type extends Papi_Data_Type {

	/**
	 * Define our Data Type meta data.
	 *
	 * @return array
	 */
	public function meta() {
		return [
			'name'        => 'Info data type',
			'description' => 'This is a Info data type'
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
