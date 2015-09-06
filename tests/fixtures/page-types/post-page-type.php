<?php

class Post_Page_Type extends Papi_Page_Type {

	/**
	 * Define our Page Type meta data.
	 *
	 * @return array
	 */
	public function page_type() {
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
	}
}
