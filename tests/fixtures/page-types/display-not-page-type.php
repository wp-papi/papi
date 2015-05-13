<?php

class Display_Not_Page_Type extends Papi_Page_Type {

	/**
	 * Define our Page Type meta data.
	 *
	 * @return array
	 */

	public function page_type() {
		return [
			'name'         => 'Display not page',
			'description'  => 'This is a display not page',
			'template'     => 'pages/display-not-page.php',
			'_not_used'    => 'Not used',
			'capabilities' => array( 'kvack' )
		];
	}

	/**
	 * Define our properties.
	 */

	public function register() {
	}

	/**
	 * Do not display this page type.
	 */

	public function display( $post_type ) {
		return 'hello';
	}
}
