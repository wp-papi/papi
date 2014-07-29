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
      <div class="pr-inner">
        <div class="pr-actions">
          <?php $this->label(); ?>
          <a class="pr-list-add-new-item" href="#">Add new</a>
        </div>
        <ul class="pr-list-template hidden">
          <li>
            <a class="pr-list-remove-item" href="#">Remove</a>
            <table class="ptb-table">
              <tbody>
                <tr class="num">
                  <td colspan="2">
                    #2
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
          <ul class="pr-list-items">
            <?php foreach ($values as $value): ?>
            <li>
              <a class="pr-list-remove-item" href="#">Remove</a>
              <table class="ptb-table">
                <tbody>
<tr class="num">
  <td colspan="2">
    #1
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
   * Output custom JavaScript for the property.
   *
   * @since 1.0.0
   */

  public function js () {
    ?>
    <script type="text/javascript">
      (function ($) {

        // Replace all template name attributes with data-name attribute.
        $('ul.pr-list-template > li').find('[name*=ptb_]').each(function () {
          var $this = $(this);

          $this.attr('data-name', $this.attr('name'));
          $this.removeAttr('name');
        });

        // Add new item and update the array index in html name.
        $('.ptb-property-list').on('click', '.pr-list-add-new-item', function (e) {
          e.preventDefault();

          var $template = $('ul.pr-list-template > li').clone()
            , counter = $('ul.pr-list-items').children().length
            , html = $template.html()
            , dataNameRegex = /data\-name\=/g
            , attrNameRegex = /name\=\"\ptb_\w+(\[\d+\])\[(\w+)\]\"/g
            , attrNameValue = '[' + counter + ']';

          html = html.replace(dataNameRegex, 'name=');

          // Update array number in html name and name if ends with a number.
          html = html.replace(attrNameRegex, function (match, num, name) {
            return match.replace(num, attrNameValue);
          });

          $template.html(html).appendTo('ul.pr-list-items');

        <?php if ($this->get_settings(array('scroll_to_last' => true))->scroll_to_last): ?>
          // Scroll to the last item in list.
          $('html, body').animate({
            scrollTop: $('ul.pr-list-items > li:last').offset().top
          });
        <?php endif; ?>
        });

        // Remove item
        $('.ptb-property-list').on('click', '.pr-list-remove-item', function (e) {
          e.preventDefault();

          $(this).closest('li').remove();
        });

      })(window.jQuery);
    </script>
    <?php
  }

  /**
   * Convert the value of the property before we output it to the application.
   *
   * @param mixed $value
   * @since 1.0.0
   *
   * @return array
   */

  public function convert ($value) {
    return array_values($value);
  }

}