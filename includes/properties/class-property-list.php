<?php

// Exit if accessed directly
if (!defined('ABSPATH')) exit;

/**
 * Act - Property List
 *
 * @package Act
 * @version 1.0.0
 */

class PropertyList extends Act_Property {

  /**
   * List counter number.
   *
   * @var int
   * @since 1.0.0
   */

  private $counter = 0;

  /**
   * Get a list of properties that aren't allowed to use in a list.
   *
   * @since 1.0.0
   *
   * @return array
   */

  private function get_not_allowed_properties () {
    $list = apply_filters('act/property/list/not_allowed_properties', array());

    if (is_string($list)) {
      $list = array($list);
    } else if (!is_array($list)) {
      $list = array();
    }

    return array_merge(array('PropertyMap'), $list);
  }

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

    $not_allowed_properties = $this->get_not_allowed_properties();

    // Check so we don't try to register a property map in a list since it won't work.
    $properties = array_filter($properties, function ($property) use($not_allowed_properties) {
      return !in_array($property->type, $not_allowed_properties);
    });

    ?>
    <div class="act-property-list">
      <div class="act-property-list-inner">
        <div class="act-property-list-actions">
          <?php $this->label(); ?>
          <a class="act-property-list-add-new-item" href="#">Add new</a>
        </div>
        <ul class="act-property-list-template hidden">
          <li>
            <a class="act-property-list-remove-item" href="#">Remove</a>
            <table class="act-table">
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
                    _act_render_property($template_property);
                  endforeach;
                ?>
                </tbody>
              </table>
            </li>
          </ul>
          <ul class="act-property-list-items">
            <?php
              foreach ($values as $value):
            ?>
            <li>
              <a class="act-property-list-remove-item" href="#">Remove</a>
              <table class="act-table">
                <tbody>
                  <tr class="num">
                    <td colspan="2">
                      #<span><?php echo $this->counter + 1; ?></span>
                    </td>
                  </tr>
                  <?php
                    foreach ($properties as $property):
                      $render_property = clone $property;
                      $value_slug = _act_remove_act($render_property->slug);

                      // Get property value.
                      if (isset($value[$value_slug])) {
                        $render_property->value = $value[$value_slug];
                      }

                      $render_property->slug = $this->generate_slug($render_property);
                      _act_render_property($render_property);
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
    return $this->options->slug . '[' . $this->counter . ']' . '[' . str_replace('act_act', 'act', $property->slug) . ']';
  }

  /**
   * Format the value of the property before we output it to the application.
   *
   * @param mixed $value
   * @param int $post_id
   * @since 1.0.0
   *
   * @return array
   */

  public function format_value ($value, $post_id) {
    return array_values($value);
  }

}
