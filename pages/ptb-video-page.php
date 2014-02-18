<?php

class PTB_Video_Page extends PTB_Base {

  public static $page_type = array(
    'name' => 'Video sida',
    'description' => 'En video sida',
    'template' => 'page-video-page.php',
  );

  public function __construct () {
    parent::__construct();

    $this->remove(array(
      'editor',
      'comments',
      'revisions'
    ));

    $this->property(array(
      'type' => PropertyString,
      'title' => 'Youtube video',
      // 'key' => 'youtube-vide',
      'box' => 'Filmer',
      'box_sort_order' => 0,
      'priority' => 'default',
      'disable' => false,
      'require' => false
    ));


    $this->property(array(
      'type' => PropertyString,
      'title' => 'Vimeo video',
      // 'key' => 'youtube-vide',
      'box' => 'Filmer',
      'priority' => 'default',
      'disable' => false,
      'sort_order' => 1
    ));


    $this->property(array(
      'type' => PropertyString,
      'title' => 'Youtube video',
      // 'key' => 'youtube-vide',
      'box' => 'Filmer2',
      'box_sort_order' => 1,
      'priority' => 'default',
      'disable' => false,
      'require' => false,
      'sort_order' => 0
    ));


    $this->property(array(
      'type' => PropertyString,
      'title' => 'Vimeo video',
      // 'key' => 'youtube-vide',
      'box' => 'Filmer2',
      'priority' => 'default',
      'disable' => false,
      'sort_order' => 1
    ));

  }

}