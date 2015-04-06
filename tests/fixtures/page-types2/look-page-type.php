<?php

class Look_Module_Type extends Papi_Page_Type {

	/**
	 * Define our Page Type meta data.
	 *
	 * @return array
	 */

	public function page_type() {
		return array(
			'name'        => 'Look page',
			'description' => 'This is a look page',
			'post_type'   => array()
		);
	}

	/**
	 * Define our properties.
	 */

	public function register() {
	}
}
