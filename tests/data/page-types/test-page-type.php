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
			'template'    => 'pages/test-page.php'
		];
	}

	/**
	 * Add help tabs.
	 *
	 * @return array
	 */
	public function help() {
		return [
			'Page type'    => 'Class cursus vehicula dolor. Tellus inceptos semper pede reprehenderit habitant.',
			'More details' => 'Convallis morbi fames vivamus cum. Ac metus vivamus sollicitudin.'
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
		$this->box( 'boxes/properties.php' );
	}
}
