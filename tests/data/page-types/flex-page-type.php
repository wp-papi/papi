<?php

class Flex_Page_Type extends Papi_Page_Type {

	public function meta() {
		return [
			'description' => 'Vel adipisicing dapibus nostra. Lectus malesuada volutpat aliquet',
			'fill_labels' => true,
			'name'        => 'Flex page',
			'template'    => 'pages/flex-page.php'
		];
	}

	public function remove() {
		return 'editor';
	}

	public function register() {
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
								papi_property( [
									'title' => 'Image left',
									'type'  => 'image'
								] ),
								papi_property( [
									'title' => 'Image right',
									'type'  => 'image'
								] )
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
						],
						[
							'title' => 'List',
							'items' => [
								papi_property( [
									'type'     => 'repeater',
									'title'    => 'Repeater test',
									'slug'     => 'repeater_test_other',
									'settings' => [
										'items' => [
											papi_property( [
												'type'  => 'string',
												'title' => 'Book name',
												'slug'  => 'book_name'
											] ),
											papi_property( [
												'type'  => 'bool',
												'title' => 'Is open?',
												'slug'  => 'is_open'
											] )
										]
									]
								] )
							]
						]
					]
				]
			] )
		] );
	}
}
