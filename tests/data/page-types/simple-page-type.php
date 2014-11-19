<?php

class Simple_Page_Type extends Papi_Page_Type {

	/**
	 * Define our Page Type meta data.
	 *
	 * @return array
	 */

	public function page_type() {
		return array(
			'name'        => 'Simple page',
			'description' => 'This is a simple page',
			'template'    => 'pages/simple-page.php'
		);
	}

	/**
	 * Define our properties.
	 */

	public function register() {
	}
}
