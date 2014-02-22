<?php

class PTB_Map_Page extends PTB_Base {

  public static $page_type = array(
    'name' => 'Kartsida',
    'description' => 'En kartsida',
    'template' => 'page-map-page.php',
  );

  public function __construct () {
    parent::__construct();

    $this->remove(array(
      'editor',
      'comments',
      'revisions'
    ));
    
    $this->box('Plats', array(
      $this->property(array(
        'type' => 'PropertyMap',
        'title' => 'Plats',
        'custom' => array(
          'api_key' => 'AIzaSyCYn-cYmKSOx290fSSvNDugi-U6qpJZe60'
        )
      ))
    ));
  }

}