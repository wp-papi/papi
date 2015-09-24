<?php

class Hidden_Page_Type extends Papi_Page_Type {

	/**
	 * Define our Page Type meta data.
	 *
	 * @return array
	 */
	public function page_type() {
		return [
			'name'          => 'Hidden page',
			'description'   => 'This is a hidden page',
			'template'      => 'pages/hidden-page.php',
            'post_type'     => 'hidden',
			'standard_type' => true
		];
	}

	/**
	 * Define our properties.
	 */
	public function register() {
	}
}
