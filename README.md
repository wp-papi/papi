# Papi

[![Build Status](https://travis-ci.org/wp-papi/papi.svg?branch=master)](https://travis-ci.org/wp-papi/papi)

Papi allows you create page types using the existing page post type or using custom post types. The documentation this project isn't so good, but you can look at the example below and checkout the properties to get a picture of how it works. For those how have work with Page Type Builder in EPiServer you will recognize themselves. Page Type Builder for WordPress is heavily inspired by the Page Type Builder for EPiServer.

[Documentation](http://wp-papi.github.io/)

**Note: This project and its documentation are still under active development, so use it in production on your own risk**

![](http://public.forsmo.me/wp-ptb/add-new-page.png?v4)

![](http://public.forsmo.me/wp-ptb/about-us-page-type.png?v3)

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

		// Add social media links meta box
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

		// Add Google Maps meta box for our office position
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

Everyone is welcome to contribute with patches, bug-fixes and new features.

1. Create an [issue](https://github.com/wp-papi/papi/issues) on Github so the community can comment on your idea.
2. Fork `papi` on Github.
3. Create a new branch: `git checkout -b my_branch`.
4. Commit your changes.
5. Push to your branch: `git push origin my_branch`.
6. Create a pull request against `master` branch. (This will change after 1.0 release)

**Note:**

* If you are making several changes at once please divide them into multiple pull requests.
