<?php

if ( ! class_exists( 'FAQ_Page_Type' ) ) {
	require_once __DIR__ . '/faq-page-type.php';
}

class FAQ_Extra_Page_Type extends FAQ_Page_Type {

	/**
	 * Define our Page Type meta data.
	 *
	 * @return array
	 */
	public function page_type() {
		return [
			'name'         => 'FAQ Extra page',
			'description'  => 'This is a faq extra page',
			'template'     => 'pages/faq-extra-page.php',
			'post_type'    => 'faq',
			'fill_labels'  => true
		];
	}

	public function remove() {
		return 'blog';
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
