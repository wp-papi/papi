# Papi

[![Build Status](https://travis-ci.org/wp-papi/papi.svg?branch=master)](https://travis-ci.org/wp-papi/papi)

**Note: This project and its documentation are still under active development, so use it in production on your own risk**

Papi has a different approach on how to work with fields and page types in WordPress. The idea is coming from how Page Type Builder in EPiServer works and has been loved by the developers.

So we though why don’t use the same approach in WordPress? Papi is today running in production and has been easy to work with when it came to add new fields. Papi don’t have any admin user interface where should add all fields, we use classes in PHP, where one class represents one page type and in your class you add all fields you need. It’s that easy!

[Documentation](http://wp-papi.github.io/)

![](http://wp-papi.github.io/assets/images/papi/add-new-page-type-view.png)

![](http://wp-papi.github.io/assets/images/papi/start-page-example-page.png)

#### Register page types directory

```php
<?php

// In your functions file
register_page_types_directory('path/to/page-types/directory');

// If you do this is mu-plugins directory or outside your theme.
function my_register_page_types_directory () {
  register_page_types_directory('path/to/page-types/directory');
}
add_action('after_setup_theme', 'my_register_page_types_directory');

?>
```

#### The page type class.

```php
<?php

/**
 * The about us page type.
 */

class About_Us_Page_Type extends Papi_Page_Type {

	/**
	 * Define our Page Type meta data.
	 *
	 * @return array
	 */

	public function page_type() {
		return [
			'name'        => 'About us',
			'description' => 'About the company',
			'template'    => 'pages/about-us.php'
		];
	}

	/**
	 * Register our properties.
	 */

	public function register() {

		// Remove comments meta box
		$this->remove( 'comments' );

		// Url properties
		$this->box( 'Social media links', array(
			$this->property( [
				'type'  => 'url',
				'title' => 'Twitter url',
				'slug'  => 'twitter_url'
			] ),
			$this->property( [
				'type'  => 'url',
				'title' => 'Facebook url',
				'slug'  => 'facebook_url'
			] )
		) );

		// Repeater property
		$this->box( 'Images', [
			$this->property( [
				'type'     => 'repeater',
				'title'    => 'Images',
				'slug'     => 'images',
				'sidebar'  => false,
				'settings' => [
					'items' => [
						$this->property( [
							'type'  => 'image',
							'title' => 'Image'
						] ),
						$this->property( [
							'type'  => 'text',
							'title' => 'Image description'
						] )
					]
				]
			] )
		] );
	}
}
```

#### Getting property values

There are three ways to get the property value. We can call on `current_page` object that is a object with all properties.

Example: `echo current_page()->twitter_link`

Or we can use `echo papi_field('twitter_link');`

Or we can use `the_papi_field('twitter_link');`

## Contribute

Visit our [contribute](http://wp-papi.github.io/contribute/) page.
