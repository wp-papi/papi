# Page Type Builder for WordPress

Page Type Builder for WordPress allows you create page types using the existing page post type or using custom post types. No meta box, fields or so is saved in the database (this may change in the feature). The documentation this project isn't so good, but you can look at the example below and checkout the properties to get a picture of how it works. For those how have work with Page Type Builder in EPiServer you will recognize themselves. Page Type Builder for WordPress is heavily inspired by the Page Type Builder for EPiServer.

Contribution are most welcome! We love it.

**Note: This project and its documentation are still under active development, so use it in production on your own risk**

## Example

The page type class.

```php

<?php

class PTB_Standard_Page extends PTB_Base {

	public static $page_type = array(
		'name' => 'Standard Page',
		'description' => 'Description of standard page',
		'template' => 'page-standard-page.php'
	);
	
	public function __construct () {
		parent::__construct();
		
        $this->box('Content', array(
          $this->property(array(
            'type' => 'PropertyString',
            'title' => 'Heading',
          )),
          $this->property(array(
            'type' => 'PropertyText',
            'title' => 'Text',
          ))
        ));
	}

}

```

## Template

The page will store the value of `template` in `_wp_page_template` so right `page-{x}.php` is loaded in your theme. This isn't nesseary for when you are using custom post types since they are using `single-{x}.php`.

#### Get value

```php
<?php

  echo current_page()->heading;
  
  // or
  echo ptb_value('heading');
  
```

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
