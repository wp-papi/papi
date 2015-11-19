<?php

if ( ! class_exists( 'FAQ_Extra_Page_Type' ) ) {
	require_once __DIR__ . '/faq-extra-page-type.php';
}

class FAQ_Extra2_Page_Type extends FAQ_Extra_Page_Type {

	/**
	 * Define our Page Type meta data.
	 *
	 * @return array
	 */
	public function page_type() {
		return [
			'name'         => 'FAQ Extra2 page',
			'description'  => 'This is a faq extra2 page',
			'template'     => 'pages/faq-extra2-page.php',
			'post_type'    => 'faq',
			'fill_labels'  => true
		];
	}

	public function remove() {
		return 'editor';
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
