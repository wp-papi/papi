<?php

// Exit if accessed directly
if (!defined('ABSPATH')) exit;

/**
 * Page Type Builder Tab class.
 */

class PTB_Tab {

  /**
   * The box to render as tabs with properties.
   *
   * @var object
   * @since 1.0
   */

  private $box;

  /**
   * First tab.
   *
   * @var object
   * @since 1.0
   */

  private $first_tab;

  /**
   * The gernated html.
   *
   * @var string
   * @since 1.0
   */

  public $html;

  /**
   * Construct. Setup the tabs.
   *
   * @param object $box
   * @since 1.0
   * @throws Exception
   */

  public function __construct ($box) {
    if (!isset($box) || empty($box->properties)) {
      throw new Exception('PTB Error: No tabs in the box');
    }

    // Generate unique names for all tabs.
    for ($i = 0; $i < count($box->properties); $i++) {
      $box->properties[$i]->name = $box->properties[$i]->name = _ptb_name($box->properties[$i]->title) . '_' . $i;
    }

    $this->first_tab = $box->properties[0];
    $this->box = $box;
    $this->html = $this->html();
  }

  /**
   * Generate html for tabs and properties.
   *
   * @since 1.0
   *
   * @return string
   */

  public function html () {
    $ul = PTB_Html::tag('div', array(
      'class' => 'ptb-tabs-back'
    ));

    $ul .= PTB_Html::tag('ul', array(
      'class' => 'ptb-tabs'
    ), false);

    foreach ($this->box->properties as $tab) {
      // Add tab title to ul list.
      $ul .= PTB_Html::tag('li', array(
        'class' => ($this->first_tab == $tab ? 'active' : '')
      ), false);

      $ul .= PTB_Html::tag('a', $tab->title, array(
        'href' => '#',
        'data-ptb-tab' => $tab->name
      ), false);

      if (isset($tab->options->icon)) {
        $ul .= PTB_Html::tag('img', array(
          'src' => $tab->options->icon
        ));
      }

      $ul .= PTB_Html::stop('a');
    }

    $ul .= PTB_Html::stop('ul');

    $div = PTB_Html::tag('div', array(
      'class' => 'ptb-tabs-content'
    ), false);

    // Content for each tabs.
    foreach ($this->box->properties as $tab) {

      // Start div and table for tab.
      $div .= PTB_Html::tag('div', array(
        'data-ptb-tab' => $tab->name,
        'class' => ($this->first_tab == $tab ? 'active' : '')
      ), false);
      $div .= PTB_Html::tag('table', array(
        'class' => 'ptb-table tab-table'
      ), false);
      $div .= PTB_Html::start('tbody');

      // Append all html from the properties.
     // foreach ($tab->properties as $property) {
        var_dump($tab);
     // }

      // End div and table for tab.
      $div .= PTB_Html::stop('tbody')
        . PTB_Html::stop('table')
        . PTB_Html::stop('div');
    }

    $div .= PTB_Html::stop('div');

    return PTB_Html::tag('div', array(
      'class' => 'ptb-tabs-wrapper'
    ), false) . $ul
       . $div
       . PTB_Html::stop('div')
       . PTB_Html::tag('div', array(
        'class' => 'ptb-clear'
       ));
  }

}