<?php

class Look_Module_Type extends Papi_Page_Type {

	/**
	 * Define our Page Type meta data.
	 *
	 * @return array
	 */
	public function meta() {
		return [
			'name'        => 'Look page',
			'description' => 'This is a look page',
			'post_type'   => []
		];
	}

	/**
	 * Define our properties.
	 */
	public function register() {
	}
}
