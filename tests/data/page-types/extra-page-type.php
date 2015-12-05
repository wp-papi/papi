<?php

class Extra_Page_Type extends Papi_Page_Type {

	/**
	 * Define our Page Type meta data.
	 *
	 * @return array
	 */
	public function meta() {
		return [
			'name'        => 'Extra page',
			'description' => 'This is a extra page',
			'template'    => 'pages/extra-page.php'
		];
	}

	/**
	 * Define our properties.
	 */
	public function register() {

		$this->box( 'Content', [

			papi_property( [
				'type'      => 'editor',
				'title'     => 'Editor test',
				'slug'      => 'post_content',
				'overwrite' => true
			] )

		] );

	}
}
