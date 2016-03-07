<?php

if ( ! class_exists( 'FAQ_Taxonomy_Type' ) ) {
	require_once __DIR__ . '/faq-Taxonomy-type.php';
}

class FAQ_Extra_Taxonomy_Type extends FAQ_Taxonomy_Type {

	/**
	 * Define our Taxonomy Type meta data.
	 *
	 * @return array
	 */
	public function meta() {
		return [
			'name'         => 'FAQ Extra taxonomy',
			'description'  => 'This is a faq extra taxonomy',
			'template'     => 'pages/faq-extra-taxonomy.php'
		];
	}

	/**
	 * Define our properties.
	 */
	public function register() {
		$this->box( 'Content', [
			papi_property( [
				'description' => 'FAQ 2',
				'post_type'   => 'faq',
				'type'        => 'string',
				'title'       => 'Name'
			] )
		] );
	}
}
