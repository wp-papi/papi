<?php

class Simple_Page_Type extends Papi_Page_Type {

	/**
	 * Define our Page Type meta data.
	 *
	 * @return array
	 */

	public function page_type() {
		return [
			'name'        => 'Simple page',
			'description' => 'This is a simple page',
			'template'    => 'pages/simple-page.php',
			'post_type'   => []
		];
	}

	/**
	 * Define our properties.
	 */

	public function register() {
		// Remove post type support and remove_meta_box.
		$this->remove( [ 'editor', 'commentdiv' ] );

		// Test box property.
		$this->box( 'Hello', papi_property( [
			'type'  => 'string',
			'title' => 'Name'
		] ) );

		$this->box( 'Content', [
			'type'  => 'string',
			'title' => 'Name',
			'slug' => 'name2'
		] );

		$this->box( papi_property( [
			'type'  => 'number',
			'title' => 'Siffran',
			'slug'  => 'siffran'
		] ) );

		// Will not work.
		$this->box( 1 );

		// Box without a empty title.
		$this->box( [
			'title' => ''
		], [
			papi_property( [
				'type'  => 'string',
				'title' => 'Country'
			] )
		] );

		// Test properties from another method.
		$this->box( [
			'title'      => 'Content',
			'sort_order' => 100
		], [ $this, 'content_box' ] );

		// Load box from a template file.
		$this->box(
			$this->template(
				dirname( __DIR__ ) . '/boxes/simple.php'
			)
		);

		$this->box( 'Children', [
			papi_property( [
				'type'     => 'string',
				'title'    => 'Name',
				'slug'     => 'name_levels',
				'settings' => [
					'items' => [
						papi_property([
							'type'     => 'string',
							'title'	   => 'Child name',
							'settings' => [
								'items' => [
									papi_property( [
										'type'  => 'string',
										'title' => 'Child child name'
									] )
								]
							]
						])
					]
				]
			] )
		] );
	}

	public function content_box() {
		return [ $this->property( [
			'type'  => 'string',
			'title' => 'Name'
		] ) ];
	}
}
