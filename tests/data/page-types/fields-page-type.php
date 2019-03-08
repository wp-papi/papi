<?php

class Fields_Page_Type extends Papi_Page_Type {

	/**
	 * Define our Page Type meta data.
	 *
	 * @return array
	 */
	public function meta() {
		return [
			'name'        => 'Fields page',
			'description' => 'This is a fields page',
			'template'    => 'pages/fields-page.php',
		];
	}

	public function fields( $fields ) {
		return ['name' => 'fields'];
	}

	/**
	 * Define our properties.
	 */
	public function register() {
		$this->box( 'Content', [
			papi_property( [
				'type'  => 'string',
				'title' => 'Name'
			] ),
		] );
	}
}
