<?php

class Test_Page_Type extends Papi_Page_Type {

	/**
	 * Define our Page Type meta data.
	 *
	 * @return array
	 */
	public function meta() {
		return [
			'fill_labels' => true,
			'name'        => 'Test page',
			'template'    => 'pages/test-page.php',
			'show_screen_options'=>false
		];
	}

	/**
	 * Add help tabs.
	 *
	 * @return array
	 */
	public function help() {
		return [
			'hej' => 'du'
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
	 * Register properties.
	 */
	public function register() {
		$this->box( 'Content', [
			papi_property( [
				'type'  => 'string',
				'title' => 'Name'
			] )
		] );
	}
}
