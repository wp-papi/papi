<?php

class Post_Archive_Type extends Papi_Archive_Type {

	/**
	 * Define our Page Type meta data.
	 *
	 * @return array
	 */
	public function meta() {
		return [
			'name'      => 'Post Archive',
			'post_type' => 'post'
		];
	}

	/**
	 * Define our properties.
	 */
	public function register() {
		$this->box( 'Content', [
			papi_property( [
				'type'  => 'string',
				'title' => 'Name',
			] )
		] );
	}
}
