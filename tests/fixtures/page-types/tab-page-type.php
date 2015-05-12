<?php

class Tab_Page_Type extends Papi_Page_Type {

	/**
	 * Define our Page Type meta data.
	 *
	 * @return array
	 */

	public function page_type() {
		return [
			'name'        => 'Tab page',
			'description' => 'This is a tab page',
			'template'    => 'pages/tab-page.php'
		];
	}

	public function register() {
		// Add tabs to a box.
		$this->box( 'Tabs', [
			$this->tab( 'Content', [
				$this->property( [
					'type'  => 'string',
					'title' => 'Name'
				] )
			] ),

			$this->tab(
				papi_template( dirname( __DIR__ ) . '/tabs/content.php' )
			)
		] );

		$this->box( 'Tabs not working', [
			$this->tab( 1 )
		] );
	}

}
