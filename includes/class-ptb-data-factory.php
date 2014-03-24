<?php

/**
 * Page Type Builder Data Factory class
 *
 * NOTE: This file is just for playground right now. Not sure if will be in wp-ptb.
 */
 
class PTB_Data_Factory {

  /**
   * The instance of Page Type Builder.
   *
   * @var object
   * @since 1.0
   */

  private static $instance;

  /**
   * Page Type Bulider instance.
   *
   * @since 1.0
   *
   * @return object
   */

  public static function instance () {
    if (!isset(self::$instance)) {
      self::$instance = new PTB_Data_Factory;
    }
    return self::$instance;
  }
  
  public function get_page ($post_id = 0) {
    return new PTB_Page($post_id);
  }

 }