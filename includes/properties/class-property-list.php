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
    $options = $this->get_options();

    // Database value.
    $values = $this->get_value(array());

    $this->name = $options->name;

    $properties = $options->properties;

    ?>
    <tr>
      <td colspan="2">
        <div class="ptb-property-list">
          <div class="pr-inner">
            <div class="pr-actions">
              <?php $this->label(); ?>
              <a class="pr-list-add-new-item" href="#">Add new</a>
            </div>
            <ul class="pr-list-template hidden">
              <li>
                <a class="pr-list-remove-item" href="#">Remove</a>
                <?php foreach ($properties as $property): ?>
                  <table class="ptb-table">
                    <tbody>
                      <?php
                        $template_property = clone $property;
                        $template_property->name = $this->generate_name($template_property);
                        _ptb_render_property($template_property);
                      ?>
                    </tbody>
                  </table>
                <?php endforeach; ?>
              </li>
            </ul>
            <ul class="pr-list-items">
              <?php foreach ($values as $value): ?>
              <li>
                <a class="pr-list-remove-item" href="#">Remove</a>
                <table class="ptb-table">
                  <tbody>
                    <?php
                      foreach ($properties as $property):
                        $render_property = clone $property;
                        $value_name = _ptb_remove_ptb($render_property->name);

                        // Get property value.
                        if (isset($value[$value_name])) {
                          $render_property->value = $value[$value_name];
                        }

                        $render_property->name = $this->generate_name($render_property);
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
      </td>
    </tr>
  <?php
  }

  /**
   * Generate property name.
   *
   * @param object $property
   * @since 1.0.0
   *
   * @return string
   */

  public function generate_name ($property) {
    if (!isset($property->name) || empty($property->name)) {
      $name = _ptbify(strtolower($property->type));
    } else {
      $name = $property->name;
    }

    return $this->name . '[' . $this->counter . ']' . '[' . str_replace('ptb_ptb', 'ptb', $property->name) . ']';
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
   * Render the final html that is displayed in the table or without a table.
   *
   * @since 1.0.0
   */

  public function render () {
    if ($this->get_options()->table): ?>
      <tr>
        <td colspan="2">
          <?php $this->html(); ?>
        </td>
      </tr>
    <?php
      $this->helptext(false);
    else:
      echo '&nbsp;';
      $this->html();
      $this->helptext(false);
    endif;
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