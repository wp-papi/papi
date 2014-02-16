<?php

/**
 * Page Type Builder Properties class.
 *
 * @todo Move properties to own class that extends from a base class.
 */

class PTB_Properties extends PTB_Properties_Base {

  /**
   * Property string.
   *
   * @since 1.0
   */

  const PropertyString = 'PropertyString';
  const PropertyStringHtml = '<input type="text" {{attributes}} />';

  /**
   * Property boolean
   *
   * @since 1.0
   */

  const PropertyBoolean = 'PropertyBoolean';
  const PropertyBooleanHtml = '<input type="checkbox" {{attributes}} />';

  /**
   * Property email.
   *
   * @since 1.0
   */

  const PropertyEmail = 'PropertyEmail';
  const PropertyEmailHtml = '<input type="email" {{attributes}} />';

  /**
   * Property url.
   *
   * @since 1.0
   */

  const PropertyUrl = 'PropertyUrl';
  const PropertyUrlHtml = '<input type="url" {{attributes}} />';

  /**
   * Propert number.
   *
   * @since 1.0
   */

  const PropertyNumber = 'PropertyNumber';
  const PropertyNumberHtml = '<input type="url" {{attributes}} />';

  /**
   * Property date.
   *
   * @since 1.0
   */

  const PropertyDate = 'PropertyDate';
  const PropertyDateHtml = '<input type="date" {{attributes}} />';

  /**
   * Property datetime.
   *
   * @since 1.0
   */

  const PropertyDateTime = 'PropertyDateTime';
  const PropertyDateTimeHtml = '<input type="datetime" {{attributes}} />';

  /**
   * Property time.
   *
   * @since 1.0
   */

  const PropertyTime = 'PropertyTime';
  const PropertyTimeHtml = '<input type="time" {{attributes}} />';

  /**
   * Property color.
   *
   * @since 1.0
   */

  const PropertyColor = 'PropertyColor';
  const PropertyColorHtml = '<input type="color" {{attributes}} />';

  /**
   * Property divider.
   *
   * @since 1.0
   */

  const PropertyDivider = 'PropertyDivider';
  const PropertyDividerHtml = '<h3 class="hndle"><span>{{title}}</span></h3>';

  /**
   * Get special properties html output.
   *
   * @param object $property
   * @since 1.0
   *
   * @return string
   */

  public function special_properties ($property) {
    switch ($property->type) {
      case self::PropertyDivider:
        $html = self::PropertyDividerHtml;
        $html = str_replace('{{title}}', $property->title, $html);
        return '</tbody></table>' . $html . '<table><tbody>';
      default:
        return null;
    }
  }

}