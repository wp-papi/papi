<?php

// Exit if accessed directly
if (!defined('ABSPATH')) exit;

/**
 * Page Type Builder - Property Image
 */

class PropertyImage extends PTB_Property {

  /**
   * Get the html for output.
   *
   * @since 1.0
   *
   * @return string
   */

  public function html () {
    $images = array();
    $css_classes = $this->css_classes('pr-image-item');

    $options = $this->get_options();
    $custom = $this->custom_options(array(
      'gallery' => false
    ));

    // If it's a gallery, we need to load all images.
    if ($custom->gallery) {
      $values = array();
      $name = '';

      if (isset($options->value)) {
        if (is_array($options->value)) {
          $values = $options->value;
        } else {
          $values = array($options->value);
        }
      }

      // We need a html array!
      if (strpos($name, '[]') === false) {
        $name .= $options->name . '[]';
      }

      foreach ($values as $value) {
        $image = (object)array(
          'id'        => 0,
          'value'     => '',
          'css_class' => '',
          'name'      => $name
        );

        if (is_numeric($value)) {
          $value = $this->convert($value);
        }

        if (is_object($value) && isset($value->url)) {
          $image->value = $value->url;
          $image->id = $value->id;
          $image->css_class = ' height-auto ';
          $images[] = $image;
        }
      }

      // Add one image if no exists.
      if (empty($images)) {
        $images = array(
          (object)array(
            'id'        => 0,
            'value'     => '',
            'css_class' => '',
            'name'      => $options->name
          )
        );
      }
    } else {
      // If it's not a gallery we load the single image.
      $image = (object)array(
        'id'        => 0,
        'value'     => '',
        'css_class' => '',
        'name'      => $options->name
      );

      // If it's not converted convert it.
      if (isset($options->value) && is_numeric($options->value)) {
        $options->value = $this->convert($options->value);
      }

      // Set image if it exists.
      if (isset($options->value) && is_object($options->value)) {
        $image->value = $options->value->url;
        $image->id = $options->value->id;
        $image->css_class = ' height-auto ';
      }

      $images[] = $image;
    }

    $add_new = __('Add New', 'ptb');

    $html = <<< EOF
      <div class="ptb-property-image">
        <div class="pr-images">
          <a href="#" class="pr-add-new">{$add_new}</a>
          <ul class="pr-template hidden">
            <li>
              <img class="{$css_classes}" data-ptb-property="image" />
              <input type="hidden" name="{$options->name}[]" id="{$options->name}[]" />
            </li>
          </ul>
          <ul class="pr-image-items">
EOF;

      foreach ($images as $image) {
        $css = $css_classes . $image->css_class;
        $html .= <<< EOF
          <li>
            <img src="{$image->value}" class="{$css}" data-ptb-property="image" />
            <input type="hidden" value="{$image->id}" name="{$image->name}" id="{$image->name}" />
          </li>
EOF;
      }

    return $html .= <<< EOF
        </ul>
      </div>
    </div>
EOF;
  }


  /**
   * Output custom JavaScript for the property.
   *
   * @since 1.0
   */

  public function js () {
    ?>
    <script type="text/javascript">
      (function ($) {

        $('.pr-images').on('click', '.pr-add-new', function (e) {
          e.preventDefault();
          $('.pr-template > li:first').clone().appendTo('.pr-image-items');
        });

      })(window.jQuery);
    </script>
    <?php
  }

  /**
   * Convert the value of the property before we output it to the application.
   *
   * @param mixed $value
   * @since 1.0
   *
   * @return object|string
   */

  public function convert ($value) {
    if (is_numeric($value)) {
      $meta = wp_get_attachment_metadata($value);
      if (isset($meta) && !empty($meta)) {
        $mine = array(
          'is_image' => true,
          'url'      => wp_get_attachment_url($value),
          'id'       => intval($value)
        );
        return (object)array_merge($meta, $mine);
      } else {
        return $value;
      }
    } else {
      return $value;
    }
  }

  /**
   * Render the final html that is displayed in the table.
   *
   * @since 1.0
   *
   * @return string
   */

  public function render () {
    if ($this->get_options()->table) {
      $label = PTB_Html::td($this->label(), array('colspan' => 2));
      $label = PTB_Html::tr($label);
      $html = PTB_Html::td($this->html(), array('colspan' => 2));
      $html = PTB_Html::tr($html);
      $html .= $this->helptext(false);
      return $label . $html;
    }
    return $this->label() . $this->html() . $this->helptext(false);
  }

}