<?php

class Editor_Page_Type extends Papi_Page_Type {

	/**
	 * Define our Page Type meta data.
	 *
	 * @return array
	 */
	public function meta() {
		return [
			'name'        => 'Editor page',
			'description' => 'This is a editor page',
			'template'    => 'pages/editor-page.php'
		];
	}

	/**
	 * Define our properties.
	 */
	public function register() {
		$this->box( 'Content', [
			papi_property( [
				'type'      => 'editor',
				'title'     => 'Editor'
			] )
		] );
	}
}
