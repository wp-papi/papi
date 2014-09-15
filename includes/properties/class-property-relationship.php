<?php

// Exit if accessed directly
if (!defined('ABSPATH')) exit;

/**
 * Page Type Builder - Property Relationship
 *
 * @package PageTypeBuilder
 * @version 1.0.0
 */

class PropertyRelationship extends PTB_Property {

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
                if (!empty($post->post_title)) {
                  $html .= <<< EOF
                    <li>
                      <input type="hidden" data-name="{$options->slug}[]" value="{$post->ID}" />
                      <a href="#">{$post->post_title}</a>
                      <span class="icon plus"></span>
                    </li>
EOF;
                }
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
                <input type="hidden" name="{$options->slug}[]" value="{$post->ID}" />
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
   * Format the value of the property before we output it to the application.
   *
   * @param mixed $value
   * @param int $post_id
   * @since 1.0.0
   *
   * @return array
   */

  public function format_value ($value, $post_id) {
    if (is_array($value)) {
      return array_map(function ($id) {
        return ptb_get_page($id);
      }, $value);
    } else {
      return array();
    }
  }
}