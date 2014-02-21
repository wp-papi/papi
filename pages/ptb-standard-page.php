<?php

class PTB_Standard_Page extends PTB_Base {

  public static $page_type = array(
    'name' => 'Standard sida',
    'description' => 'En helt vanlig sida',
    'template' => 'page-standard-page.php',
  );

  public function __construct () {
    parent::__construct();
    
    $this->box('Innehåll', array(
      $this->property(array(
        'type' => 'PropertyString',
        'title' => 'Rubrik',
      )),
      $this->property(array(
        'type' => 'PropertyText',
        'title' => 'Text',
      ))
    ));
    
  }

}