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
		'template' => 'path-to-file-in-theme-dir.php'
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

The page will store the value of `template` in `_wp_page_template` so right `page-{x}.php` is loaded in your theme.git 

So, `current_page()->heading` will return the value of the heading input field.