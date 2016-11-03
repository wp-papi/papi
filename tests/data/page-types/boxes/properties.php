<?php

return [
	'title' => 'Properties',

	// Bool
	papi_property( [
		'type'  => 'bool',
		'title' => 'Bool test',
		'slug'  => 'bool_test'
	] ),

	// Checkbox
	papi_property( [
		'type'     => 'checkbox',
		'title'    => 'Checkbox test',
		'slug'     => 'checkbox_test',
		'settings' => [
			'items' => [
				'White' => '#ffffff',
				'Black' => '#000000'
			]
		]
	] ),

	// Color
	papi_property( [
		'type'  => 'color',
		'title' => 'Color test',
		'slug'  => 'color_test'
	] ),

	// Datetime
	papi_property( [
		'type'  => 'datetime',
		'title' => 'Datetime test',
		'slug'  => 'datetime_test'
	] ),

	// Divider
	papi_property( [
		'description' => 'Some information here',
		'type'        => 'divider',
		'title'       => 'Divider test',
		'slug'        => 'divider_test'
	] ),

	// Dropdown
	papi_property( [
		'type'     => 'dropdown',
		'title'    => 'Dropdown test',
		'slug'     => 'dropdown_test',
		'settings' => [
			'items' => [
				'White' => '#ffffff',
				'Black' => '#000000'
			]
		]
	] ),

	// Dropdown 2
	papi_property( [
		'type'     => 'dropdown',
		'title'    => 'Dropdown test 2',
		'slug'     => 'dropdown_test_2',
		'settings' => [
			'placeholder'   => 'Pick one',
			'items'         => [
				'White' => '#ffffff',
				'Black' => '#000000'
			]
		]
	] ),

	// Dropdown 3
	papi_property( [
		'type'     => 'dropdown',
		'title'    => 'Dropdown test 3',
		'slug'     => 'dropdown_test_3',
		'settings' => [
			'multiple'      => true,
			'placeholder'   => 'Pick multiple',
			'items'         => [
				0,
				1,
				2,
				3,
				4,
				5
			]
		]
	] ),

	// Editor
	papi_property( [
		'type'  => 'editor',
		'title' => 'Editor test',
		'slug'  => 'editor_test'
	] ),

	// Editor
	papi_property( [
		'type'  => 'editor',
		'title' => 'Editor2 test',
		'slug'  => 'editor2_test',
	] ),

	// Email
	papi_property( [
		'type'  => 'email',
		'title' => 'Email test',
		'slug'  => 'email_test'
	] ),

	// File
	papi_property( [
		'type'  => 'file',
		'title' => 'File test',
		'slug'  => 'file_test'
	] ),

	// File 2
	papi_property( [
		'type'     => 'file',
		'title'    => 'File test 2',
		'slug'     => 'file_test_2',
		'settings' => [
			'multiple' => true
		]
	] ),

	// Flexible
	papi_property( [
		'type'     => 'flexible',
		'title'    => 'Flexible test',
		'slug'     => 'flexible_test',
		'settings' => [
			'items' => [
				'twitter' => [
					'title' => 'Twitter',
					'items' => [
						papi_property( [
							'type'  => 'string',
							'title' => 'Twitter name',
							'slug'  => 'twitter_name'
						] )
					]
				],
				'posts' => [
					'title' => 'Posts',
					'items' => [
						papi_property( [
							'type'  => 'post',
							'title' => 'Post one',
							'slug'  => 'post_one'
						] ),
						papi_property( [
							'type'  => 'post',
							'title' => 'Post two',
							'slug'  => 'post_two'
						] )
					]
				]
			]
		]
	] ),

	// Gallery
	papi_property( [
		'type'  => 'gallery',
		'title' => 'Gallery test',
		'slug'  => 'gallery_test'
	] ),

	// Group
	papi_property( [
		'title'    => 'Group test',
		'slug'     => 'group_test',
		'type'     => 'group',
		'settings' => [
			'items' => [
				papi_property( [
					'title'    => 'Page',
					'slug'     => 'page',
					'type'     => 'post',
					'settings' => [
						'post_type' => 'page'
					]
				] ),
				papi_property( [
					'type'     => 'string',
					'title'    => 'Page title',
					'slug'     => 'page_title'
				] )
			]
		]
	] ),

	// Hidden
	papi_property( [
		'type'  => 'hidden',
		'title' => 'Hidden test',
		'slug'  => 'hidden_test'
	] ),

	// Html
	papi_property( [
		'type'  => 'html',
		'title' => 'Html test',
		'slug'  => 'html_test',
		'settings' => [
			'html' => '<p>Hello, world!</p>'
		]
	] ),

	// Html 2
	papi_property( [
		'type'  => 'html',
		'title' => 'Html test 2',
		'slug'  => 'html_test_2',
		'settings' => [
			'html' => 'properties_output_html'
		]
	] ),

	// Html save
	papi_property( [
		'type'  => 'html',
		'title' => 'Html save test',
		'slug'  => 'html_save_test',
		'settings' => [
			'save' => true,
			'html' => '<p>Hello, world!</p>'
		]
	] ),

	// Image
	papi_property( [
		'type'  => 'image',
		'title' => 'Image test',
		'slug'  => 'image_test'
	] ),

	// Link
	papi_property( [
		'type'  => 'link',
		'title' => 'Link test',
		'slug'  => 'link_test'
	] ),

	// Number
	papi_property( [
		'type'  => 'number',
		'title' => 'Number test',
		'slug'  => 'number_test'
	] ),

	// Post
	papi_property( [
		'type'  => 'post',
		'title' => 'Post test',
		'slug'  => 'post_test'
	] ),

	// Radio
	papi_property( [
		'type'     => 'radio',
		'title'    => 'Radio test',
		'slug'     => 'radio_test',
		'settings' => [
			'items' => [
				'White' => '#ffffff',
				'Black' => '#000000'
			]
		]
	] ),

	// Reference
	papi_property( [
		'type'  => 'reference',
		'title' => 'Reference test',
		'slug'  => 'reference_test'
	] ),

	// Relationship
	papi_property( [
		'type'  => 'relationship',
		'title' => 'Relationship test',
		'slug'  => 'relationship_test'
	] ),

	// Relationship 2
	papi_property( [
		'type'     => 'relationship',
		'title'    => 'Relationship test 2',
		'slug'     => 'relationship_test_2',
		'settings' => [
			'items' => [
				[
					'id'    => 2,
					'title' => 'Two'
				],
				[
					'id'    => 1,
					'title'	=> 'One'
				]
			]
		]
	] ),

	// Repeater
	papi_property( [
		'type'     => 'repeater',
		'title'    => 'Repeater test',
		'slug'     => 'repeater_test',
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
	] ),

	// Repeater with repeater inside
	papi_property( [
		'type'     => 'repeater',
		'title'    => 'Repeater with child test',
		'slug'     => 'repeater_with_child_test',
		'settings' => [
			'items' => [
				papi_property( [
					'type'     => 'repeater',
					'title'    => 'Repeater child test',
					'slug'     => 'repeater_child_test',
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
	] ),

	// String
	papi_property( [
		'type'  => 'string',
		'title' => 'String test',
		'slug'  => 'string_test'
	] ),

	// String html
	papi_property( [
		'type'     => 'string',
		'title'    => 'String html test',
		'slug'     => 'string_html_test',
		'settings' => [
			'allow_html' => true
		]
	] ),

	// Term
	papi_property( [
		'type'     => 'term',
		'title'    => 'Term test',
		'slug'     => 'term_test',
		'settings' => [
			'taxonomy' => 'test_taxonomy',
		]
	] ),

	// Text
	papi_property( [
		'type'  => 'text',
		'title' => 'Text test',
		'slug'  => 'text_test'
	] ),

	// Text html
	papi_property( [
		'type'     => 'text',
		'title'    => 'Text html test',
		'slug'     => 'text_html_test',
		'settings' => [
			'allow_html' => true
		]
	] ),

	// Url
	papi_property( [
		'type'  => 'url',
		'title' => 'Url test',
		'slug'  => 'url_test'
	] ),

	// Url mediauploader
	papi_property( [
		'type'     => 'url',
		'title'    => 'Url mediauploader test',
		'slug'     => 'url_mediauploader_test',
		'settings' => [
			'mediauploader' => true
		]
	] ),

	// User
	papi_property( [
		'type'     => 'user',
		'title'    => 'User test',
		'slug'     => 'user_test',
		'settings' => [
			'placeholder' => 'Select user'
		]
	] )
];
