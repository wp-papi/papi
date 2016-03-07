<?php

if ( ! class_exists( 'FAQ_Extra_Taxonomy_Type' ) ) {
	require_once __DIR__ . '/faq-extra-taxonomy-type.php';
}

class FAQ_Extra2_Taxonomy_Type extends FAQ_Extra_Taxonomy_Type {

	/**
	 * Define our Taxonomy Type meta data.
	 *
	 * @return array
	 */
	public function meta() {
		return [
			'name'         => 'FAQ Extra2 taxonomy',
			'description'  => 'This is a faq extra2 taxonomy',
			'template'     => 'pages/faq-extra2-taxonomy.php'
		];
	}

	/**
	 * Define our properties.
	 */
	public function register() {
		$this->box( 'Content', [
			papi_property( [
				'description' => 'FAQ 3',
				'post_type'   => 'faq',
				'type'        => 'image',
				'title'       => 'image'
			] )
		] );
	}
}
