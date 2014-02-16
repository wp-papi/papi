<?php

class PTB_Video_Page extends PTB_Base {

  public static $page_type = array(
    'name' => 'Video sida',
    'description' => 'En video sida',
    'filename' => 'page-video-page.php',
  );

  public function __construct () {
    parent::__construct();

    $this->property(array(
      'type' => self::PropertyString,
      'title' => 'Youtube video',
      // 'key' => 'youtube-vide',
      'box' => 'Filmer',
      'priority' => 'default',
      'disable' => true,
      'require' => false
    ));

  }

}