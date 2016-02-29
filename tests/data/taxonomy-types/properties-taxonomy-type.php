<?php

class Properties_Taxonomy_Type extends Papi_Taxonomy_Type {

	/**
	 * Define our Page Type meta data.
	 *
	 * @return array
	 */
	public function meta() {
		return [
			'name'        => 'Properties taxonomy type',
			'description' => 'This is a properties taxonomy page',
			'template'    => 'pages/properties-taxonomy-page.php',
			'taxonomy'    => 'post_tag'
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
		$this->box( papi_template( __DIR__ . '/../page-types/boxes/properties.php' ) );
	}
}