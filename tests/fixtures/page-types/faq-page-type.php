<?php

class FAQ_Page_Type extends Papi_Page_Type {

	/**
	 * Define our Page Type meta data.
	 *
	 * @return array
	 */

	public function page_type() {
		return array(
			'name'         => 'FAQ page',
			'description'  => 'This is a faq page',
			'template'     => 'pages/faq-page.php',
			'_not_used'    => 'Not used',
			'capabilities' => array( 'kvack' ),
			'thumbnail'    => 'faq.png',
			'post_type'    => 'faq'
		);
	}

	/**
	 * Define our properties.
	 */

	public function register() {
		$this->box('Content', array(
			papi_property(array(
				'type'  => 'string',
				'title' => 'Question'
			)),
			papi_property(array(
				'type'  => 'text',
				'title' => 'Answer'
			))
		));
	}
}
