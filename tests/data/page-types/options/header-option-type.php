<?php

class Header_Option_Type extends Papi_Option_Type {

	public function option_type() {
		return [
			'name' => 'Header',
			'menu' => 'options-general.php'
		];
	}

	public function register() {

		$this->box( 'Options', [

			papi_property( [
				'title' => 'Image',
				'type' => 'image'
			] ),

			papi_property( [
				'title' => 'Name',
				'type' => 'string'
			] )

		] );

		$this->box( 'Children', [
			papi_property( [
				'type'     => 'string',
				'title'    => 'Name',
				'slug'     => 'name_levels',
				'settings' => [
					'items' => [
						papi_property([
							'type'     => 'string',
							'title'	   => 'Child name',
							'slug'     => 'child_name',
							'settings' => [
								'items' => [
									[
										'type'  => 'string',
										'title' => 'Child child name',
										'slug'  => 'child_child_name'
									],
									null
								]
							]
						])
					]
				]
			] )
		] );

		$this->box('Children 2', [
			papi_property( [
				'type'     => 'string',
				'title'    => 'Name',
				'slug'     => 'name_levels_2',
				'settings' => [
					'items' => [
						[
							papi_property( [
								'type'  => 'string',
								'title'	=> 'Child name 2',
								'slug'  => 'child_name_2'
							] )
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
