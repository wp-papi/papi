<?php

class Front_Page_Type extends Papi_Front_Page_Type {

	/**
	 * Define our Page Type meta data.
	 *
	 * @return array
	 */
	public function meta() {
		return [
			'name'          => 'Front page',
			'description'   => 'This is a front page',
			'template'      => 'pages/front-page.php',
			'standard_type' => false,
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
