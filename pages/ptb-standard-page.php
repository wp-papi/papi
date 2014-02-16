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

    $this->property(array(
      'type' => self::PropertyString,
      'title' => 'Name of the person',
      'key' => 'name-of-the-person',
      'priority' => 'default',
      'show_ui' => true,
      'require' => false,
      'box' => 'kalle'
    ));

    $this->property(array(
      'type' => self::PropertyUrl,
      'title' => 'Twitter',
      'key' => 'twitter',
      'priority' => 'default',
      'show_ui' => true,
      'require' => false,
      'box' => 'kalle'
    ));

  }

}