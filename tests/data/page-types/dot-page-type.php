<?php

class Dot_Page_Type extends Papi_Page_Type {

	/**
	 * Define our Page Type meta data.
	 *
	 * @return array
	 */
	public function meta() {
		return [
			'name'         => 'Dot page',
			'description'  => 'This is a dot page',
			'template'     => 'pages.dot'
		];
	}

	/**
	 * Add custom body classes.
	 *
	 * @return array
	 */
	public function body_classes() {
		return ['custom-css-class'];
	}

	/**
	 * Define our properties.
	 */
	public function register() {
	}
}
