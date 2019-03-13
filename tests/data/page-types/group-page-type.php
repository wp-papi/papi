<?php

class Group_Page_Type extends Papi_Page_Type {

	/**
	 * Define our Page Type meta data.
	 *
	 * @return array
	 */
	public function meta() {
		return [
			'name'        => 'Group page',
			'description' => 'This is a group page',
			'template'    => 'pages/group-page.php',
		];
	}

	/**
	 * Remove post type support and `remove_meta_box`.
	 *
	 * @return array
	 */
	public function remove() {
		return ['all'];
	}

	/**
	 * Define our properties.
	 */
	public function register() {
		$this->box( [
			'title' => 'Content',
			'props' => [
				papi_property( [
					'title'    => 'Group',
					'type'     => 'group',
					'settings' => [
						'items' => [
							papi_property( [
								'type'  => 'string',
								'title' => 'Title',
								'slug'  => 'title',
							] ),
							papi_property( [
								'title' => 'Preamble',
								'slug'  => 'preamble',
								'desc'  => 'Max length: 175 characters',
								'type'  => 'text',
							] ),
						]
					]
				] )
			]
		] );
	}
}
