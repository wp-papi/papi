<?php

class PTB_Standard_Page extends PTB_Base {

  public function __construct () {
    parent::__construct(array(
      'name' => '',
      'description' => '',
      'filename' => '',
      'availablepagetypes' => ''
    ));
  }

  /**
   * Add our custom properties to standard page.
   */

  public function properties () {

    // Name property.
    $this->property(array(
      'type' => self::PropertyString,
      'title' => 'Name of the person',
      'key' => 'name-of-the-person', // can be empty, will take title and slugify it then
      'priority' => 'default',
      'show_ui' => true,
      'require' => false
    ));

  }

}