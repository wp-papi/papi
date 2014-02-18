<?php

/**
 * Page Type Builder - Property String
 */

class PropertyString extends PTB_Property {

  /**
   * Get the html for output.
   *
   * @since 1.0
   *
   * @return string
   */

  public function html () {
    return PTB_Html::input('text', array(
      'name' => $this->property->name,
      'value' => $this->property->value
    ));
  }

}