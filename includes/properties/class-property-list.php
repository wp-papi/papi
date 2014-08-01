<?php

// Exit if accessed directly
if (!defined('ABSPATH')) exit;

/**
 * Page Type Builder - Property List
 *
 * @package PageTypeBuilder
 * @version 1.0.0
 */

class PropertyList extends PTB_Property {

  /**
   * Generate the HTML for the property.
   *
   * @since 1.0.0
   */

  public function html () {
    $this->counter = 0;

    // Property options.
    $this->options = $this->get_options();

    // Database value.
    $values = $this->get_value(array());

    $properties = array();

    if (isset($this->options->properties) && is_array($this->options->properties)) {
      $properties = $this->options->properties;
    }

    ?>
    <div class="ptb-property-list">
      <div class="ptb-property-list-inner">
        <div class="ptb-property-list-actions">
          <?php $this->label(); ?>
          <a class="ptb-property-list-add-new-item" href="#">Add new</a>
        </div>
        <ul class="ptb-property-list-template hidden">
          <li>
            <a class="ptb-property-list-remove-item" href="#">Remove</a>
            <table class="ptb-table">
              <tbody>
                <tr class="num">
                  <td colspan="2">
                    #<span></span>
                  </td>
                </tr>
                <?php
                  foreach ($properties as $property):
                    $template_property = clone $property;
                    $template_property->slug = $this->generate_slug($template_property);
                    _ptb_render_property($template_property);
                  endforeach;
                ?>
                </tbody>
              </table>
            </li>
          </ul>
          <ul class="ptb-property-list-items">
            <?php
              foreach ($values as $value):
            ?>
            <li>
              <a class="ptb-property-list-remove-item" href="#">Remove</a>
              <table class="ptb-table">
                <tbody>
                  <tr class="num">
                    <td colspan="2">
                      #<span><?php echo $this->counter + 1; ?></span>
                    </td>
                  </tr>
                  <?php
                    foreach ($properties as $property):
                      $render_property = clone $property;
                      $value_slug = _ptb_remove_ptb($render_property->slug);

                      // Get property value.
                      if (isset($value[$value_slug])) {
                        $render_property->value = $value[$value_slug];
                      }

                      $render_property->slug = $this->generate_slug($render_property);
                      _ptb_render_property($render_property);
                    endforeach;
                  ?>
                </tbody>
              </table>
            </li>
          <?php
            $this->counter++;
          endforeach; ?>
        </ul>
      </div>
    </div>
  <?php
  }

  /**
   * Generate property slug.
   *
   * @param object $property
   * @since 1.0.0
   *
   * @return string
   */

  public function generate_slug ($property) {
    if (!isset($property->slug) || empty($property->slug)) {
      $slug = _ptbify(strtolower($property->type));
    } else {
      $slug = $property->slug;
    }

    return $this->options->slug . '[' . $this->counter . ']' . '[' . str_replace('ptb_ptb', 'ptb', $property->slug) . ']';
  }

  /**
   * Format the value of the property before we output it to the application.
   *
   * @param mixed $value
   * @since 1.0.0
   *
   * @return array
   */

  public function format_value ($value) {
    return array_values($value);
  }

}