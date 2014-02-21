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

    $this->property(array(
      'type' => PropertyText,
      'title' => 'Rubrik och text',
      // 'key' => 'youtube-vide',
      'box' => 'Filmer',
      'box_sort_order' => 0,
      'priority' => 'default',
      'disable' => false,
      'require' => false
    ));
    
  }

}