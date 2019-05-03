<?php

class Gutenberg_Page_Type extends Papi_Page_Type {

	/**
	 * Define our Page Type meta data.
	 *
	 * @return array
	 */
	public function meta() {
		return [
			'name'        => 'Gutenberg page',
			'description' => 'This is a gutenberg page',
			'template'    => 'pages/gutenberg-page.php',
		];
	}

	/**
	 * Define our properties.
	 */
	public function register() {
		$this->block( 'Info', papi_property( [
			'type'  => 'string',
			'title' => 'Book name'
		] ) );

		$this->block( [
			'title'      => 'Modules',
			papi_property( [
				'sidebar'  => false,
				'slug'     => 'modules',
				'title'    => 'Modules',
				'type'     => 'flexible',
				'settings' => [
					'layout' => 'row',
					'items'  => [
						[
							'title' => 'Image & text block',
							'slug'  => 'image-text-block',
							'items' => [
								papi_property( [
									'title' => 'Title',
									'type'  => 'string',
									'slug'  => 'title',
								] ),
								papi_property( [
									'title' => 'Text',
									'type'  => 'text',
									'slug'  => 'text',
								] ),
								papi_property( [
									'title' => 'Image',
									'type'  => 'image',
									'slug'  => 'image',
								] ),
								papi_property( [
									'title' => 'Link',
									'type'  => 'link',
									'slug'  => 'link',
								] ),
								papi_property( [
									'title' => 'Button text',
									'type'  => 'string',
									'slug'  => 'button_text',
								] ),
							],
						],
						[
							'title' => 'Files',
							'slug'  => 'files',
							'items' => [
								papi_property( [
									'title'    => 'Files',
									'slug'     => 'files',
									'type'     => 'repeater',
									'settings' => [
										'add_new_label' => 'Add new file',
										'layout'        => 'table',
										'items'         => [
											papi_property( [
												'title' => 'File',
												'type'  => 'file',
												'slug'  => 'source',
											] ),

											papi_property( [
												'title' => 'Title',
												'type'  => 'string',
												'slug'  => 'title',
											] ),
										],
									],
								] ),

								papi_property( [
									'title' => 'Image',
									'type'  => 'image',
									'slug'  => 'image',
								] ),

								papi_property( [
									'title' => 'Title',
									'type'  => 'string',
									'slug'  => 'title',
								] ),

								papi_property( [
									'title' => 'Description',
									'type'  => 'string',
									'slug'  => 'description',
								] ),
							],
						]
					],
				],
			] ),
		] );
	}
}
