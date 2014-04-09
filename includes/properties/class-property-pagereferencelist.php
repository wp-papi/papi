<?php

// Exit if accessed directly
if (!defined('ABSPATH')) exit;

/**
 * Page Type Builder - Property PageReferenceList
 */

class PropertyPageReferenceList extends PTB_Property {

  /**
   * Get the html for output.
   *
   * @since 1.0
   *
   * @return string
   */

  public function html () {
    $name = $this->get_options()->name;

    // Default post type is page.
    if (isset($this->get_options()->custom->post_types)) {
      $post_types = $this->get_options()->custom->post_types;
    } else {
      $post_types = 'page';
    }

    // Fetch posts with the post types.
    $posts = query_posts(array(
      'post_type' => $post_types
    ));

    // Default show max value.

    if (isset($this->get_options()->custom->show_max)) {
      $show_max = $this->get_options()->custom->show_max;
    } else {
      $show_max = count($posts);
    }

    $posts = array_slice($posts, 0, $show_max);

    // Get current references.
    $references = $this->get_options()->value;

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
                  <input type="hidden" data-name="{$name}[]" value="{$post->ID}" />
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
                <input type="hidden" name="{$name}[]" value="{$post->ID}" />
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

  return $html;
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
   * Render the final html that is displayed in the table.
   *
   * @since 1.0
   *
   * @return string
   */

  public function render () {
    $label = PTB_Html::td($this->label(), array('colspan' => 2));
    $label = PTB_Html::tr($label);
    $html = PTB_Html::td($this->html(), array('colspan' => 2));
    $html = PTB_Html::tr($html);
    $html .= $this->helptext(false);
    return $label . $html;
  }

  /**
   * Convert the value of the property before we output it to the application.
   *
   * @param mixed $value
   * @since 1.0
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