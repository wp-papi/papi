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
    $css_classes = $this->css_classes();

    $options = $this->get_options();
    $custom = $this->get_custom_options(array(
      'gallery' => false
    ));
    $is_gallery = $custom->gallery;

    // If it's a gallery, we need to load all images.
    if ($is_gallery) {
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
          $image->css_class = ' pr-image-item ';
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
        $image->css_class = ' pr-image-item ';
      }

      $images[] = $image;
    }

    if ($is_gallery) {
      $images = array_filter($images, function ($image) {
        return !!$image->id;
      });
    }

    $html = <<< EOF
      <div class="ptb-property-image">
        <div class="pr-images">
          <ul class="pr-template hidden">
            <li class="{$css_classes}">
              <img />
              <input type="hidden" name="{$options->name}[]" id="{$options->name}[]" />
              <p class="pr-remove-image">
                <a href="#">&times;</a>
              </p>
            </li>
          </ul>
          <ul class="pr-image-items">
EOF;

      foreach ($images as $image) {
        $css = $css_classes . $image->css_class;
        $html .= "<li class=\"$css\">";
        if ($is_gallery) {
          $html .= '<p class="pr-remove-image">
            <a href="#">&times;</a>
          </p>';
        }
        $html .= "
            <img src=\"$image->value\" />
            <input type=\"hidden\" value=\"$image->id\" name=\"$image->name\" id=\"$image->name\" />
          </li>
          ";
        }

    if ($is_gallery) {
      $html .= <<< EOF
        <li class="pr-add-new {$css_classes}">
          <p>
            <a href="#">Set image</a>
          </p>
        </li>
EOF;
    } else {
      $html .= '<li style="visibility:hidden"></li>';
    }

    echo $html .= <<< EOF
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

        $('.ptb-property-image .pr-images').on('click', 'li', function (e) {
          e.preventDefault();

          var $this = $(this)
            , $target = $this
            , $li = $this.closest('li')
            , $img = $li.find('img')
            , remove = $img.attr('src') !== undefined && $li.find('p.pr-remove-image').length && e.target.tagName.toLowerCase() === 'a';

          if ($li.hasClass('pr-add-new')) {
            $target = $('.ptb-property-image .pr-template > li:first').clone();
            $target.insertBefore($li);
            $target = $target.find('img');
          } else if (!remove) {
            $target = $img;
          }

          if (remove) {
            $target.closest('li').remove();
          } else {
            Ptb.Utils.wp_media_editor($target, function (attachment) {
              if (Ptb.Utils.is_image(attachment.url)) {
                $target.attr('style', 'height:auto');
                $target.attr('src', attachment.url);
                $target.next().val(attachment.id);
                $target.closest('li').addClass('pr-image-item');
              }
            });
          }
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
    } else if (is_array($value)) {
      foreach ($value as $k => $v) {
         $value[$k] = $this->convert($v);
      }
      return $value;
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