<?php

class Flex_Page_Type extends Papi_Page_Type {

	public function page_type() {
		return [
			'description' => 'Vel adipisicing dapibus nostra. Lectus malesuada volutpat aliquet',
			'fill_labels' => true,
			'name'        => 'Flex page',
			'template'    => 'pages/flex-page.php'
		];
	}

	public function register() {

		$this->remove( 'editor' );

		$this->box( 'Content', [
			papi_property( [
				'title'    => 'Sections',
				'type'     => 'flexible',
				'sidebar'  => false,
				'settings' => [
					'items' => [
						[
							'title' => 'Twitter',
							'items' => [
								papi_property( [
									'title'    => 'Twitter name 1',
									'type'     => 'string'
								] ),
								papi_property( [
									'title'    => 'Twitter name 2',
									'type'     => 'string'
								] ),
								papi_property( [
									'title'    => 'Twitter name 3',
									'type'     => 'string'
								] )
							]
						],
						[
							'title' => 'Images',
							'items' => [
								[
									'title' => 'Image left',
									'type'  => 'image'
								],
								[
									'title' => 'Image right',
									'type'  => 'image'
								]
							]
						],
						[
							'title' => 'Editor',
							'items' => [
								papi_property( [
									'title' => 'Editor',
									'type'  => 'editor'
								] )
							]
						]
					]
				]
			] )
		] );
	}

	public function display( $post_type ) {
		return false;
	}
}
