<?php

class PTB_Tab_Page extends PTB_Base {

  public static $page_type = array(
    'name' => 'Tab sida',
    'description' => 'En tab sida',
    'template' => 'page-tab-page.php',
  );

  public function __construct () {
    parent::__construct();

    $this->remove(array(
      'editor',
      'comments',
      'revisions'
    ));
    
    $this->tab('Text', array($this, 'tab_text'));  
  }
  
  public function tab_text () {
    $this->property(array(
      'type' => PropertyText,
      'title' => 'Rubrik'
    ));
  }

}