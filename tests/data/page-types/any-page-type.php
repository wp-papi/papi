<?php

class Any_Page_Type extends Papi_Page_Type {

	/**
	 * Define our Page Type meta data.
	 *
	 * @return array
	 */
	public function meta() {
		return [
			'name'        => 'Any page',
			'description' => 'This is a any page',
			'template'    => 'pages/any-page.php',
			'post_type'   => 'any'
		];
	}

	/**
	 * Define our properties.
	 */
	public function register() {
		$this->box( 'Content', papi_property( [
			'type'  => 'string',
			'title' => 'Name'
		] ) );
	}
}
