<?php

class Hidden2_Page_Type extends Papi_Page_Type {

	/**
	 * Define our Page Type meta data.
	 *
	 * @return array
	 */
	public function page_type() {
		return [
			'name'          => 'Hidden2 page',
			'description'   => 'This is a hidden2 page',
			'template'      => 'pages/hidden2-page.php',
            'post_type'     => 'hidden',
			'standard_type' => false
		];
	}

	/**
	 * Define our properties.
	 */
	public function register() {
	}
}
