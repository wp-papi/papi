<?php

class Module_Page_Type extends Papi_Page_Type {

	/**
	 * Define our Page Type meta data.
	 *
	 * @return array
	 */
	public function meta() {
		return [
			'name'        => 'Module page',
			'description' => 'This is a module page',
			'template'    => 'pages/module-page.php'
		];
	}

	/**
	 * Define our properties.
	 */
	public function register() {
		$this->box( 'Content', [
			papi_property( [
				'type'  => 'module',
				'title' => 'Module',
			] )
		] );
	}
}
