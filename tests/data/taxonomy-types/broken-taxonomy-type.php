<?php

class Broken_Taxonomy_Type extends Papi_Taxonomy_Type {

	/**
	 * Define our Taxonomy Type meta data.
	 *
	 * @return array
	 */
	public function meta2() {
		return [
			'id'          => 'custom-taxonomy-type-id',
			'name'        => 'Broken taxonomy',
			'description' => 'This is a broken taxonomy',
			'template'    => 'pages/broken-taxonomy.php'
		];
	}

	/**
	 * Define our properties.
	 */
	public function register() {
	}
}
