<?php

// Exit if accessed directly
if (!defined('ABSPATH')) exit;

/**
 * Page Type Builder - Property PageReferenceList
 *
 * @package PageTypeBuilder
 * @version 1.0.0
 */

class PropertyPageReferenceList extends PTB_Property {

  /**
   * Generate the HTML for the property.
   *
   * @since 1.0.0
   */

  public function html () {
    // Property options.
    $options = $this->get_options();

    // Property settings.
    $settings = $this->get_settings(array(
      'post_types' => array('page'),
      'show_max'   => -1
    ));

    // Database value.
    $references = $this->get_value();

    // Fetch posts with the post types.
    $posts = query_posts(array(
      'post_type' => $settings->post_types
    ));

    // Take as many posts we should show.
    $posts = array_slice($posts, 0, (
      $settings->show_max === -1 ?
      count($posts) :
      $settings->show_max));

    if (!is_array($references)) {
      $references = array();
    }

    $references = array_filter($references, function ($post) {
      return is_object($post);
    });

    $html = <<< EOF
      <div class="ptb-page-reference">
        <div class="pr-inner">
          <div class="pr-left">
            <div class="pr-search">
              <input type="search" placeholder="Sök" />
            </div>
            <ul>
EOF;

              foreach ($posts as $post) {
                $html .= <<< EOF
                <li>
                  <input type="hidden" data-name="{$options->name}[]" value="{$post->ID}" />
                  <a href="#">{$post->post_title}</a>
                  <span class="icon plus"></span>
                </li>
EOF;
              }
      $html .= <<< EOF
            </ul>
          </div>
          <div class="pr-right">
            <ul>
EOF;
            foreach ($references as $post) {
              $html .= <<< EOF
              <li>
                <input type="hidden" name="{$options->name}[]" value="{$post->ID}" />
                <a href="#">{$post->post_title}</a>
                <span class="icon minus"></span>
              </li>
EOF;
            }
      $html .= <<< EOF
            </ul>
          </div>
          <div class="ptb-clear"></div>
        </div>
      </div>
EOF;

    echo $html;
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

        // Add page reference to list
        $('.ptb-page-reference .pr-left').on('click', 'li', function (e) {
          e.preventDefault();
          var $li = $(this).clone();
          $li.find('span.icon').removeClass('plus').addClass('minus');
          $li.find('input').attr('name', $li.find('input').data('name'));
          $li.appendTo('.ptb-page-reference .pr-right ul');
        });

        // Remove page reference from list
        $('.ptb-page-reference .pr-right').on('click', 'li', function (e) {
          e.preventDefault();
          $(this).remove();
        });

        // Search field
        $('.ptb-page-reference .pr-left .pr-search input[type=search]').on('keyup', function () {

          var $this = $(this)
            , $list = $('.ptb-page-reference .pr-left ul')
            , val = $this.val();

          $list.find('li').each(function () {
            var $li = $(this);
            $li[$li.text().toLowerCase().indexOf(val) === -1 ? 'hide' : 'show']();
          });

        });

      })(window.jQuery);
    </script>
    <?php
  }

  /**
   * Render the final html that is displayed in the table or without table.
   *
   * @since 1.0.0
   */

  public function render () {
    $options = $this->get_options();
    if ($options->table): ?>
      <tr>
        <td colspan="2">
          <?php $this->label(); ?>
        </td>
      </tr>
      <tr>
        <td colspan="2">
          <?php $this->html(); ?>
        </td>
      </tr>
    <?php
      $this->helptext(false);
    else:
      $this->label();
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
    if (is_array($value)) {
      return array_map(function ($id) {
        return ptb_get_page($id);
      }, $value);
    } else {
      return array();
    }
  }
}