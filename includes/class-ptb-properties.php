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

  /**
   * Property email.
   *
   * @since 1.0
   */

  const PropertyEmail = '<input type="email" {{attributes}} />';

  /**
   * Property url.
   *
   * @since 1.0
   */

  const PropertyUrl = '<input type="url" {{attributes}} />';

  /**
   * Propert number.
   *
   * @since 1.0
   */

  const PropertyNumber = '<input type="url" {{attributes}} />';

  /**
   * Property date.
   *
   * @since 1.0
   */

  const PropertyDate = '<input type="date" {{attributes}} />';

  /**
   * Property datetime.
   *
   * @since 1.0
   */

  const PropertyDateTime = '<input type="datetime" {{attributes}} />';

  /**
   * Property time.
   *
   * @since 1.0
   */

  const PropertyTime = '<input type="time" {{attributes}} />';

  /**
   * Property color.
   *
   * @since 1.0
   */

  const PropertyColor = '<input type="color" {{attributes}} />';

}