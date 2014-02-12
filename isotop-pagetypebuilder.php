<?php

/**
 * Plugin Name: Isotop PageTypeBuilder
 * Description: PageTypeBuilder for WordPress
 * Author: Fredrik Forsmo
 * Author URI: http://forsmo.me/
 * Version: 1.0
 * Plugin URI:
 */

// Exit if accessed directly.
if (!defined('ABSPATH')) exit;

/*

if (!class_exists('Isotop_PageTypeBuilder')):

class Isotop_PageTypeBuilder {

  public function __construct () {
    add_action('add_meta_boxes', array($this, 'add_meta_boxes'));
    add_action('wp_enqueue_scripts', array($this, 'wp_enqueue_scripts'));
    $this->pagetype = isset($_GET['pagetype']) ? $_GET['pagetype'] : '';
  }

  public function add_meta_boxes () {
    add_meta_box('pagetype', __('Sidmall', 'pagetypebuilder'), array($this, 'page_template_box'), 'page', 'side');

    if ($this->pagetype == 'about-page') {
      add_meta_box('about-page-pagetype', __('Om oss', 'pagetypebuilder'), array($this, 'about_us_box'), 'page');
    }
  }

  public function page_template_box () {
    ?>
    <p>Välj vilken sidmall denna sida ska använda:<p>
    <div id="post-formats-select">
      <input type="radio" name="page_template" class="post-format" id="page_template" value="standard">
      <label for="page_template" class="post-format-icon post-format-aside">Standard sida</label>
      <br />
      <input type="radio" name="page_template" class="post-format" id="page_template" value="standard">
      <label for="page_template" class="post-format-icon post-format-aside">Om sida</label>
    </div>
    <?php
  }

  public function wp_enqueue_scripts () {
    wp_enqueue_script('pagetypebuilder', plugins_url('/js/pagetypebuilder.js' , __FILE__), array('jquery'), '1.0.0', true);
  }

  public function about_us_box () {
    ?>
    <label>Om oss rubrik</label>
    <br />
    <input type="text" name="about-us-heading" />
    <?php
  }

}

new Isotop_PageTypeBuilder;

endif;
*/

// Useful global constants.
define('ISOTOP_PAGETYPEBUILDER_VERSION', '0.1.0');
define('ISOTOP_PAGETYPEBUILDER_URL', plugin_dir_url(__FILE__));
define('ISOTOP_PAGETYPEBUILDER_PATH', dirname(__FILE__) . '/');

// Require files.
require_once(ISOTOP_PAGETYPEBUILDER_PATH . 'includes/class-ptb-core.php');