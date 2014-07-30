<?php

// Exit if accessed directly
if (!defined('ABSPATH')) exit;

/**
 * Page Type Builder - Property Image
 *
 * @package PageTypeBuilder
 * @version 1.0.0
 */

class PropertyImage extends PTB_Property {

  public function html () {
    // Property options.
    $options = $this->get_options();

    // CSS classes.
    $css_classes = $this->css_classes();

    // Property settings.
    $settings = $this->get_settings(array(
      'gallery' => false
    ));

    // Get the value.
    $value = $this->convert($options->value);

    if (!is_array($value)) {
      $value = array_filter(array($value));
    }

    $slug = $options->slug;
    $show_button = empty($value);

    if ($settings->gallery) {
      $css_classes .= ' gallery ';
      $slug .= '[]';
      $show_button = true;
    }

    ?>

    <script type="text/template" id="tmpl-ptb-image">
      <a href="#" data-ptb-property="image" data-ptb-options='{"id":"<%= id %>"}'>x</a>
      <img src="<%= image %>" />
      <input type="hidden" value="<%= id %>" name="<%= slug %>" />
    </script>

    <div class="wrap ptb-property-image <?php echo $css_classes; ?>">
      <p class="ptb-image-select <?php echo $show_button ? '' : 'hidden'; ?>">
        <?php
          if (!$settings->gallery) {
            _e('No image selected', 'ptb');
          }
        ?>
        <button class="button" data-ptb-options='{"slug":"<?php echo $slug; ?>"}'><?php _e('Add image', 'ptb'); ?></button>
      </p>
      <ul>
        <?php
          if (!empty($value)):
            foreach ($value as $key => $image):
              $url = wp_get_attachment_thumb_url($image->id);
        ?>
              <li>
                <a href="#" class="ptb-image-remove" data-ptb-options='{"id":"<?php echo $image->id; ?>"}'>x</a>
                <img src="<?php echo $url; ?>" />
                <input type="hidden" value="<?php echo $image->id; ?>" name="<?php echo $slug; ?>" />
              </li>
        <?php
            endforeach;
          endif;
        ?>
      </ul>
    </div>

    <?php
  }

  /**
   * Convert the value of the property before we output it to the application.
   *
   * @param mixed $value
   * @since 1.0.0
   *
   * @return array|object|string
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
}