<?php

class Simple_Page_Type extends Papi_Page_Type {

	/**
	 * Define our Page Type meta data.
	 *
	 * @return array
	 */

	public function page_type() {
		return array(
			'name'        => 'Simple page',
			'description' => 'This is a simple page',
			'template'    => 'pages/simple-page.php',
			'post_type'   => array()
		);
	}

	/**
	 * Define our properties.
	 */

	public function register() {
		// Remove post type support and remove_meta_box.
		$this->remove( array( 'editor', 'commentdiv' ) );

		// Test box property.
		$this->box( papi_property( array(
			'type'  => 'string',
			'title' => 'Name'
		) ) );

		// Will not work.
		$this->box( 1 );

		// Test properties from another method.
		$this->box( array(
			'title'      => 'Content',
			'sort_order' => 100
		), array( $this, 'content_box' ) );

		// Load box from a template file.
		$this->box(
			$this->template(
				dirname( __DIR__ ) . '/boxes/simple.php'
			)
		);
	}

	public function content_box() {
		return array( $this->property( array(
			'type'  => 'string',
			'title' => 'Name'
		) ) );
	}
}
