<?php

/**
 * Page Type Builder Properties class.
 */

class PTB_Properties extends PTB_Properties_Base {

  /**
   * Property string.
   *
   * @since 1.0
   */

  const PropertyString = '<input type="text" {{attributes}} />';

  /**
   * Property boolean
   *
   * @since 1.0
   */

  const PropertyBoolean = '<input type="checkbox" {{attributes}} />';

}