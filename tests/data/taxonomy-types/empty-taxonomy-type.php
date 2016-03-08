<?php

class Empty_Taxonomy_Type extends Papi_Taxonomy_Type {

	/**
	 * Define our Taxonomy Type meta data.
	 *
	 * @return array
	 */
	public function meta() {
		return [
			'name'        => 'Empty taxonomy',
			'description' => 'This is a empty taxonomy',
			'template'    => 'pages/empty-taxonomy.php'
		];
	}

	/**
	 * Define our properties.
	 */
	public function register() {
	}
}
