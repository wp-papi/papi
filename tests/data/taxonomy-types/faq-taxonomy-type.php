<?php

class FAQ_Taxonomy_Type extends Papi_Taxonomy_Type {

	/**
	 * Define our Taxonomy Type meta data.
	 *
	 * @return array
	 */
	public function meta() {
		return [
			'name'         => 'FAQ taxonomy',
			'description'  => 'This is a faq taxonomy',
			'template'     => 'pages/faq-taxonomy.php',
			'taxonomy'     => 'faq',
			'fill_labels'  => true
		];
	}

	/**
	 * Define our properties.
	 */
	public function register() {
		$this->box( 'Content', [
			papi_property( [
				'description' => 'FAQ 1',
				'post_type'   => 'faq',
				'type'        => 'string',
				'title'       => 'Question'
			] ),
			papi_property( [
				'description' => 'FAQ 1',
				'post_type'   => 'faq',
				'type'        => 'text',
				'title'       => 'Answer'
			] ),
			papi_property( [
				'description' => 'FAQ 1',
				'type'        => 'text',
				'title'       => 'Type',
				'disabled'    => true,
				'display'     => false
			] )
		] );
	}
}
