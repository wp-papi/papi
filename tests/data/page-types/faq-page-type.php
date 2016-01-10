<?php

class FAQ_Page_Type extends Papi_Page_Type {

	/**
	 * Define our Page Type meta data.
	 *
	 * @return array
	 */
	public function meta() {
		return [
			'name'         => 'FAQ page',
			'description'  => 'This is a faq page',
			'template'     => 'pages/faq-page.php',
			'_not_used'    => 'Not used',
			'thumbnail'    => 'faq.png',
			'post_type'    => 'faq',
			'fill_labels'  => true,
			'labels'       => [
				'nan_item' => 'Not a number item'
			],
			'child_types'  => [
				'simple-page-type',
				null,
				'fake'
			]
		];
	}

	/**
	 * Remove meta boxes.
	 *
	 * @return array
	 */
	public function remove() {
		return ['div', 'test_meta_box'];
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
