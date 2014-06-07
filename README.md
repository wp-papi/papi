# Page Type Builder for WordPress

Page Type Builder for WordPress allows you create page types using the existing page post type or using custom post types. The documentation this project isn't so good, but you can look at the example below and checkout the properties to get a picture of how it works. For those how have work with Page Type Builder in EPiServer you will recognize themselves. Page Type Builder for WordPress is heavily inspired by the Page Type Builder for EPiServer.

**Note: This project and its documentation are still under active development, so use it in production on your own risk**

![](http://public.forsmo.me/wp-ptb/add-new-page.png?v2)

![](http://public.forsmo.me/wp-ptb/about-us-page-type.png?v2)

[Watch the demo video](https://dl.dropboxusercontent.com/u/4660032/Page%20Type%20Builder%20for%20WordPress/page-type-builder-for-wordpress-intro-1.m4v). Note that demo video don't include the new way of defining the page type meta via static function.

## Example

The page type class.

```php

<?php

class About_Us_Page_Type extends PTB_Page_Data {

  /**
   * Define our Page Type meta data.
   *
   * @return array
   */

  public static function page_type () {
    return array(
      'name'        => 'About us',
      'description' => 'About the company',
      'template'    => 'pages/about-us.php'
    );
  }

  /**
   * Register our properties.
   */

  public function __construct () {
    parent::__construct();

    // Remove comments meta box
    $this->remove('comments');

    // Add social media links meta box
    $this->box('Social media links', array(
      $this->property(array(
        'type'  => 'PropertyUrl',
        'title' => 'Twitter link',
        'slug'  => 'twitter_link'
      )),
      $this->property(array(
        'type'  => 'PropertyUrl',
        'title' => 'Facebook link',
        'slug'  => 'facebook_link'
      ))
    ));

    // Add Google Maps meta box for our office position
    $this->box('Our offfice position', array(
      $this->property(array(
        'type'     => 'PropertyMap',
        'title'    => 'Position',
        'slug'     => 'position',
        'settings' => array(
          'api_key' => 'Google Maps API key'
        )
      ))
    ));
  }
}
?>

```

#### Getting property values

There are three ways to get the property value. We can call on `current_page` object that is a object with all properties and the `WP_POST` properties.

Example: `echo current_page()->twitter_link`

Or we can use `echo ptb_field('twitter_link');`

Or we can use `the_ptb_field('twitter_link');`

## Contribute

Everyone is welcome to contribute with patches, bug-fixes and new features.

1. Create an [issue](https://github.com/wp-ptb/wp-ptb/issues) on Github so the community can comment on your idea.
2. Fork `wp-ptb` on Github.
3. Create a new branch: `git checkout -b my_branch`.
4. Commit your changes.
5. Push to your branch: `git push origin my_branch`.
6. Create a pull request against `master` branch. (This will change after 1.0 release)

**Note:**

* If you are making several changes at once please divide them into multiple pull requests.
