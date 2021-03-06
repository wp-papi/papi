<?php

class Rule_Page_Type extends Papi_Page_Type {

	/**
	 * Define our Page Type meta data.
	 *
	 * @return array
	 */
	public function meta() {
		return [
			'name'        => 'Rule page',
			'description' => 'This is a rule page',
			'template'    => 'pages/rule-page.php',
			'post_type'   => []
		];
	}

	/**
	 * Define our properties.
	 */
	public function register() {
		// Test box property.
		$this->box( 'Hello', papi_property( [
			'type'  => 'string',
			'title' => 'Name'
		] ) );

		$this->box( 'Content', [
			'type'  => 'string',
			'title' => 'Name',
			'slug' => 'name2'
		] );

		$this->box( papi_property( [
			'type'  => 'number',
			'title' => 'Siffran',
			'slug'  => 'siffran'
		] ) );

		$this->box( 'Number', papi_property( [
			'type'  => 'number',
			'title' => 'Number',
			'slug'  => 'number'
		] ) );

		$this->box( 'Rules', [
			papi_property( [
				'type'  => 'number',
				'title' => 'Rules 1',
				'slug'  => 'rules_1',
				'rules' => [
					[
						'operator' => '=',
						'slug'     => 'rules1',
						'value'	   => 123
					]
				]
			] ),
			papi_property( [
				'type'  => 'number',
				'title' => 'Rules 2',
				'slug'  => 'rules_2',
				'rules' => [
					[
						'operator' => 'NOT EXISTS',
						'slug'     => 'rules2'
					]
				]
			] ),
			papi_property( [
				'type'  => 'string',
				'title' => 'Rules 3',
				'slug'  => 'rules_3',
				'rules' => [
					[
						'operator' => '=',
						'slug'     => 'rules_3',
						'value'    => 'hello'
					]
				]
			] )
		] );

		// Support rules with group:
		// slug => group.0.media
		$this->box( [
			'title'      => 'Content',
			'properties' => [
				papi_property( [
					'type'     => 'repeater',
					'title'    => 'List',
					'slug'     => 'list',
					'settings' => [
						'layout'  => 'row',
						'items'  => [
							papi_property( [
								'slug'     => 'group',
								'title'    => 'Group',
								'type'     => 'group',
								'settings' => [
									'items' => [
										papi_property( [
											'type'     => 'radio',
											'title'    => 'Media',
											'slug'     => 'media',
											'settings' => [
												'items' => [
													'Image' => 'image',
													'Video' => 'video',
												],
											],
										] ),
									],
								],
							] ),
							papi_property( [
								'type'  => 'string',
								'title' => 'Video link',
								'slug'  => 'video_link',
								'rules' => [
									[
										'compare' => '=',
										'value'   => 'video',
										'slug'    => 'group.media',
									]
								],
							] ),
						],
					],
				] ),
			],
		] );

		$this->box( 'Group', [
			papi_property( [
				'title'    => 'CTA Button',
				'slug'     => 'cta_link',
				'type'     => 'group',
				'settings' => [
					'items' => [
						papi_property( [
							'title'    => 'Select link type',
							'slug'     => 'selected_link_type',
							'type'     => 'radio',
							'sidebar'  => false,
							'layout'   => 'row',
							'settings' => [
								'selected' => 'none',
								'items'    => [
									'None'              => 'none',
									'Link to page'      => 'link_to_page',
									'Scroll to content' => 'scroll_to_content',
								],
							],
						] ),
						papi_property( [
							'slug'    => 'page_link',
							'type'    => 'link',
							'sidebar' => false,
							'rules'   => [
								[
									'operator' => '=',
									'value'    => 'link_to_page',
									'slug'     => 'selected_link_type',
								],
							],
						] ),
						papi_property( [
							'slug'        => 'scroll_link',
							'description' => 'Button text',
							'type'        => 'string',
							'rules'       => [
								[
									'operator' => '=',
									'value'    => 'scroll_to_content',
									'slug'     => 'selected_link_type',
								],
							],
						] ),
					],
				],
			] ),
		] );
	}
}
