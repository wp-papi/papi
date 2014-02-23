<?php

// Exit if accessed directly
if (!defined('ABSPATH')) exit;

/**
 * Page Type Builder Collection class.
 */

class PTB_Collection {
  
  /**
   * The box to render as list with properties.
   *
   * @var object
   * @since 1.0
   */
  
  private $box;
  
  /**
   * First collection.
   *
   * @var object
   * @since 1.0
   */
  
  private $first_collection;
  
  /**
   * The gernated html.
   *
   * @var string
   * @since 1.0
   */
  
  public $html;
  
  /**
   * Counter of items in collection.
   *
   * @var int
   * @since 1.0
   */
  
  private $i = 0;
  
  /**
   * Construct. Setup the collections.
   *
   * @param object $box
   * @since 1.0
   * @throws Exception
   */
 
  public function __construct ($box) {
    if (!isset($box) || empty($box->properties)) {
      throw new Exception('PTB Error: No collections in the box');
    }
    
    $this->first_collection = $box->properties[0];
    $this->box = $box;
    $this->html = $this->html();
  }
  
  /**
   * Generate html for collection and properties.
   *
   * @since 1.0
   *
   * @return string
   */
  
  public function html () {
    $html = PTB_Html::tag('div', array(
      'class' => 'ptb-collection-list'
    ), false);
    
    foreach ($this->box->properties as $collection) {
      // Heading
      $span = PTB_Html::tag('span', $collection->title);
      $html .= PTB_Html::tag('h3', $span, array(
        'class' => 'hndle ptb-divider-text'
      ));
      
      // Paragraph and add new link.
      $html .= PTB_Html::tag('a', __('Add new', 'ptb'), array(
        'href' => '#',
        'class' => 'ptb-pull-right',
        'data-ptb-collection' => $collection->name
      ));
      
      // Generate hidden template tag.
      $html .= PTB_Html::tag('div', array(
        'data-ptb-collection' => $collection->name,
        'class' => 'ptb-hidden'
      ), false);
      
      $html .= PTB_Html::tag('a', __('Delete', 'ptb'), array(
        'href' => '#',
        'class' => 'ptb-pull-right del',
        'data-ptb-collection' => $collection->name
      ));
      
      // Get properties table.
      $html .= $this->properties($collection);
      
      $html .= PTB_Html::stop('div');
      
      // List of properties.
      $html .= PTB_Html::tag('ul', array(
        'data-ptb-collection' => $collection->name
      ), false);
      
      if ($this->first_collection->properties[0]->collection) {
        // Get collection fields.
        $html .= $this->get_collection_fields($collection);
      } else {
        $html .= PTB_Html::tag('li', array(
          'data-ptb-collection-i' => $this->i
        ), false);
        $html .= $this->properties($collection, true);
        $html .= PTB_Html::stop('li');
      }
      
      $html .= PTB_Html::stop('ul');
    }
  
    return $html . PTB_Html::stop('div');
  }
  
  /**
   * Generate properties table.
   *
   * @param object $collection
   * @param bool $prepare
   * @since 1.0
   * @access private
   *
   * @return string
   */
  
  private function properties ($collection, $prepare = false) {
    $html = PTB_Html::tag('table', array(
      'class' => 'ptb-table'
    ), false);
    
    $html .= PTB_Html::start('tbody');
    
    foreach ($collection->properties as $property) {
      if ($prepare) {
        $html .= $this->prepare_properties($property->callback_args->html, $collection->name);
      } else {
        $html .= $property->callback_args->html;
      }
    }
    
    return $html
      . PTB_Html::stop('tbody')
      . PTB_Html::stop('table');
  }
  
  /**
   * Get collection fields as html.
   *
   * @param object $collection
   * @since 1.0
   *
   * @return string
   */
  
  private function get_collection_fields ($collection) {
    $html = '';
    $values = ptb_value(PTB_COLLECTION_KEY);
    $first = key($values);
    foreach ($values as $key => $properties) {
      foreach ($properties as $k => $v) {
        $html .= PTB_Html::tag('li', array(
          'data-ptb-collection-i' => $this->i
        ), false);
        if ($first != $k) {
          $html .= PTB_Html::tag('a', __('Delete', 'ptb'), array(
            'href' => '#',
            'class' => 'ptb-pull-right del',
            'data-ptb-collection' => $collection->name
          ));
        }
        $html .= PTB_Html::tag('table', array(
          'class' => 'ptb-table'
        ), false);
        $html .= PTB_Html::start('tbody');
        $num = 0;
        foreach (ptb_get_only_values($v) as $name => $value) {
          $property = $collection->properties[$num];

          // This is a bit ugly to use PTB_Base again.
          // But all we need to create the property again is in there.
          // TODO: Make a new class that we can reuse here and in PTB_Base.
          $base = new PTB_Base(false);

          $property->value = $value;
          $property = $base->property($property);
          $phtml = $property->callback_args->html;
          $html .= $this->prepare_properties($phtml, $collection->name);
          $num++;
        }
        $html .= PTB_Html::stop('tbody')
          . PTB_Html::stop('table')
          . PTB_Html::stop('li');
        
        $this->i++;
      }
    }
    
    return $html;
  }
  
  /**
   * Prepare properties html name attribute.
   *
   * @param string $html
   * @param string $name
   * @since 1.0
   *
   * @return string
   */
  
  private function prepare_properties ($html, $name) {
    preg_match_all('/name\=\"(\w+)\"/', $html, $matches);
    foreach ($matches[1] as $match) {
      $html = str_replace($match, str_replace('ptb_', PTB_COLLECTION_KEY . '['. $name . ']'  . '[' . $this->i . '][', $match) . ']', $html);
      $html = str_replace(']_property', '_property]', $html);
    }
    return $html;
  }
  
}