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
    
    $this->box('Bilder', array(
      $this->tab('Innehåll', array(
        $this->property(array(
          'type' => 'PropertyString',
          'title' => 'Rubrik 1'
        ))
      )),
      $this->tab('Inställningar', array(
        $this->property(array(
          'type' => 'PropertyDate',
          'title' => 'Rubrik 2'
        ))
      ))
    ));
    
    $this->box('Annat', array(
      $this->property(array(
        'type' => 'PropertyNumber',
        'title' => 'Rubrik annat',
      )),
      
      $this->property(array(
        'type' => 'PropertyDivider'
      )),
      
      $this->property(array(
        'type' => 'PropertyNumber',
        'title' => 'Rubrik annat',
      ))
    ));
  }
  
}