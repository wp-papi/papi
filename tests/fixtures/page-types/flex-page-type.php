<?php

class Flex_Page_Type extends Papi_Page_Type {

	public function page_type() {
		return array(
			'description' => 'Vel adipisicing dapibus nostra. Lectus malesuada volutpat aliquet',
			'fill_labels' => true,
			'name'        => 'Flex page',
			'template'    => 'pages/flex-page.php'
		);
	}

	public function register() {

		$this->remove( array(
			'editor'
		) );

		$this->box( 'Content', array(

			papi_property( array(
				'title'    => 'Sections',
				'type'     => 'flexible',
				'sidebar'  => false,
				'settings' => array(
					'items'  => array(
						array(
							'title' => 'Twitter',
							'items' => array(
								papi_property( array(
									'title'    => 'Twitter name 1',
									'type'     => 'string'
								) ),
								papi_property( array(
									'title'    => 'Twitter name 2',
									'type'     => 'string'
								) ),
								papi_property( array(
									'title'    => 'Twitter name 3',
									'type'     => 'string'
								) )
							)
						),
						array(
							'title' => 'Images',
							'items' => array(
								array(
									'title' => 'Image left',
									'type'  => 'image'
								),
								array(
									'title' => 'Image right',
									'type'  => 'image'
								)
							)
						),
						array(
							'title' => 'Editor',
							'items' => array(
								papi_property( array(
									'title' => 'Editor',
									'type'  => 'editor'
								) )
							)
						)
					)
				)
			) )
		) );

	}

}
