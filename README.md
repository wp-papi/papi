# Page Type Builder for WordPress

**Not ready for production yet**

### Example

The definition `PTB_DIR` is used to tell where the page type files lives in your WordPress installation.

The page type class.

```php

<?php

class PTB_Standard_Page extends PTB_Base {

	public static $page_type = array(
		'name' => 'Standard Page',
		'description' => 'Description of standard page',
		'filename' => 'path-to-file-or-file-name-in-theme-dir.php'
	);
	
	public function __construct () {
		parent::__construct();
		
		$this->property(array(
			'type' => self::PropertyString,
			'title' => 'Heading',
			'box' => 'Intro'
		));
	}

}

```

On the `page.php` you need to run `ptb_load_page();` so it loads the right page. And then you can access `current_page()` function that returns the post object megered with the page type builder array for this page.

So, `current_page()->heading` will return the value of the heading input field.