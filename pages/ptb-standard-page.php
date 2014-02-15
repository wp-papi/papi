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

  public function hello () {}

}