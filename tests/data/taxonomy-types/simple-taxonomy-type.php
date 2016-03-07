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
			'taxonomy'    => ['post_tag', 'category', 'test_taxonomy', 'faq'],
			'fill_labels' => true,
			'labels'      => [
				'name'          => 'Simple taxonomy',
				'singular_name' => 'Simple taxonomy'
			]
		];
	}

	/**
	 * Hide taxonomy type for taxonomy.
	 *
	 * @return bool
	 */
	public function display( $taxonomy ) {
		return $taxonomy !== 'test_taxonomy';
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
