<?php

class FAQ_Page_Type extends Papi_Page_Type {

	/**
	 * Define our Page Type meta data.
	 *
	 * @return array
	 */
	public function page_type() {
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
	 * Define our properties.
	 */
	public function register() {
		$this->box( 'Content', [
			papi_property( [
				'post_type' => 'faq',
				'type'      => 'string',
				'title'     => 'Question'
			] ),
			papi_property( [
				'post_type' => 'faq',
				'type'      => 'text',
				'title'     => 'Answer'
			] ),
			papi_property( [
				'type'     => 'text',
				'title'    => 'Type',
				'disabled' => true,
				'display'  => false
			] )
		] );
	}
}
