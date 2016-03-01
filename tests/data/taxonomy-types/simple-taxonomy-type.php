<?php

class Simple_Taxonomy_Type extends Papi_Taxonomy_Type {

	/**
	 * Define our Taxonomy Type meta data.
	 *
	 * @return array
	 */
	public function meta() {
		return [
			'name'        => 'Simple taxonomy',
			'description' => 'This is a simple taxonomy',
			'template'    => 'pages/simple-taxonomy.php',
			'taxonomy'    => 'post_tag'
		];
	}

	/**
	 * Define our properties.
	 */
	public function register() {
		$this->box( 'Content', [
			papi_property( [
				'type'  => 'string',
				'title' => 'Name',
			] ),
			papi_property( [
				'type'  => 'editor',
				'title' => 'Text'
			] )
		] );
	}
}
