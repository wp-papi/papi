<?php

class Name_Page_Type extends Papi_Page_Type {

	/**
	 * Define our Page Type meta data.
	 *
	 * @return array
	 */
	public function meta() {
		return [
			'name'         => 'Name page',
			'description'  => 'This is a name page',
			'template'     => [
				[
					'template' => 'layout-a.php',
					'label'    => 'Layout A'
				],
				[
					'template' => 'layout-b.php',
					'label'    => 'Layout B'
				]
			]
		];
	}

	/**
	 * Define our properties.
	 */
	public function register() {
		  $this->box( 'Your Name', [
            papi_property( 'properties/name.php', [
                'slug'  => 'my_name_is'
            ] )
        ] );
	}
}
