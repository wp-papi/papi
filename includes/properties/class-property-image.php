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

    // Get the value.
    $value = $this->convert($options->value);

    ?>

    <script type="text/template" id="tmpl-ptb-image">
      <a href="#" data-ptb-property="image" data-ptb-options='{"id":"<%= id %>"}'>x</a>
      <img src="<%= image %>" />
      <input type="hidden" value="<%= id %>" name="<%= slug %>" />
    </script>

    <div class="wrap ptb-property-image">
      <p class="ptb-image-select <?php echo empty($value) ? '' : 'hidden'; ?>">
        No image selected
        <button class="button" data-ptb-options='{"slug":"<?php echo $options->slug; ?>"}'><?php _e('Add image', 'ptb'); ?></button>
      </p>
      <div class="ptb-image-area">
        <?php if (!empty($value)): ?>
          <?php $url = isset($value->sizes['thumbnail']) ? $value->sizes['thumbnail'] : $value->url; ?>
          <a href="#" class="ptb-image-remove" data-ptb-options='{"id":"<?php echo $value->id; ?>"}'>x</a>
          <img src="<?php echo $url; ?>" />
          <input type="hidden" value="0" name="<?php echo $options->slug; ?>" />
        <?php endif; ?>
      </div>
    </div>

    <?php
  }

  /**
   * Convert the value of the property before we output it to the application.
   *
   * @param mixed $value
   * @since 1.0.0
   *
   * @return object|string
   */

  public function convert ($value) {
    if (is_numeric($value)) {
      $meta = wp_get_attachment_metadata($value);

      if (!empty($meta)) {
        $mine = array(
          'is_image' => true,
          'url'      => wp_get_attachment_url($value),
          'id'       => intval($value)
        );
        return (object)array_merge($meta, $mine);
      }

      return $value;
    }

    return $value;
  }
}