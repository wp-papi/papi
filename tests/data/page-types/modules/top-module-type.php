<?php

class Top_Module_Type extends Papi_Page_Type {

	/**
	 * Define our Page Type meta data.
	 *
	 * @return array
	 */
	public function page_type() {
		return [
			'name'        => 'Top module',
			'post_type'   => 'module',
			'description' => 'This is a top module'
		];
	}

	/**
	 * Define our properties.
	 */
	public function register() {
	}
}
