<?php

class Tab2_Page_Type extends Papi_Page_Type {

	/**
	 * Define our Page Type meta data.
	 *
	 * @return array
	 */
	public function meta() {
		return [
			'name'        => 'Tab2 page',
			'description' => 'This is a tab2 page',
			'template'    => 'pages/tab2-page.php'
		];
	}

	/**
	 * Remove meta boxes.
	 *
	 * @return array
	 */
	public function remove() {
		return ['editor', 'commentsdiv', 'commentstatusdiv', 'authordiv', 'slugdiv'];
	}

	/**
	 * Define our properties.
	 */
	public function register() {
		$this->box( 'Edit page', [
			papi_tab( 'Content', [
				papi_property( [
					'slug'      => 'post_content',
					'type'      => 'editor',
					'sidebar'   => false,
					'overwrite' => true
				] )
			] ),
			papi_tab( 'Images', [
				papi_property( [
					'title'       => 'Image',
					'slug'        => 'image',
					'type'        => 'image',
					'overwrite'   => true,
					'description' => 'Pretium non irure. Nascetur sit velit quisque venenatis fermentum'
				] ),
				papi_property( [
					'title' => 'Description',
					'slug'  => 'image_description',
					'type'  => 'string'
				] )
			] ),
			papi_tab( [
				'title'      => 'Author',
				'background' => 'white'
			], [
				papi_property( [
					'title' => 'Name',
					'slug'  => 'name',
					'type'  => 'string'
				] )
			] )
		] );
	}
}
