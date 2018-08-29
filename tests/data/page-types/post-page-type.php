<?php

class Post_Page_Type extends Papi_Page_Type {

	/**
	 * Define our Page Type meta data.
	 *
	 * @return array
	 */
	public function meta() {
		return [
			'name'        => 'Post page',
			'description' => 'This is a post page',
			'template'    => 'pages/post-page.php',
			'post_type'   => ['post', 'item', 'attachment']
		];
	}

	/**
	 * Define our properties.
	 */
	public function register() {
		$this->box( 'Content', [
			papi_property( [
				'type'     => 'post',
				'title'    => 'Post',
				'settings' => [
					'placeholder' => 'Select post'
				]
			] )
		] );
	}
}
