<?php

class Big_Page_Type extends Papi_Page_Type {

	/**
	 * Define our Page Type meta data.
	 *
	 * @return array
	 */
	public function meta() {
		return [
			'name'        => 'Big page',
			'description' => 'This is a big page',
			'template'    => 'pages/big-page.php',
		];
	}

	/**
	 * Remove post type support and `remove_meta_box`.
	 *
	 * @return array
	 */
	public function remove() {
		return ['all'];
	}

	/**
	 * Define our properties.
	 */
	public function register() {
		$this->box( [
			'title' =>'Content',
			'properties' => [
				papi_property( [
					'type'  => 'string',
					'title' => 'Book Name',
					'slug'  => 'book_name',
				] ),
				papi_property( [
					'type'  => 'image',
					'title' => 'Book Image',
					'slug'  => 'book_image'
				] ),
			]
		] );
	}
}
