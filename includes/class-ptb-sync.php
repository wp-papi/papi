<?php

// Exit if accessed directly
if (!defined('ABSPATH')) exit;

/**
 * Page Type Builder Sync.
 */

class PTB_Sync {

  /**
   * Page Type Builder Sync Constructor.
   */

  public function __construct ($post_id = null, $page_type = null) {
    // Can't proceed without a post id or a page type.
    if (is_null($post_id) || is_null($page_type)) {
      return;
    }

    // Setup page type.
    $this->setup_page_type($page_type);
  }

  private function setup_page_type ($page_type) {
    $page_type = new PTB_Page_Type($page_type);
    $this->page_type = $page_type->new_class();
  }

  private function get_all_meta_data ($post_id = null) {
    global $wpdb;
    $data = array();

    // Run SQL query against the WordPress database.
    $wpdb->query("
      SELECT `meta_key`, `meta_value`
      FROM $wpdb->postmeta
      WHERE `post_id` = $post_id
      AND `meta_key` LIKE '" . like_escape('_ptb') . "%'
      AND `meta_key` NOT LIKE '%property%';
    ");

    foreach ($wpdb->last_result as $key => $value) {
      $data[$value->meta_key] = $value->meta_value;
    }

    return $data;
  }

  private function diff_properties ($a, $b) {
    foreach ($a as $key => $value) {
      if ($value->in_list) {
        unset($a[$key]);
      }
    }

    $res = array_diff(array_keys($a), array_keys($b));

    // Property List differ.
    /*
    foreach ($b as $key => $value) {
      $value = unserialize($value);

      // If we have a array, we need to check it.
      if (is_array($value)) {
        foreach ($value as $k => $v) {

          // PropertyList has one more level of arrays with all properties keys.
          // So we have to check so ---
          if (is_array($v)) {
            foreach ($v as $j => $l) {
              $j = _ptb_property_key($j);
              if (in_array($j, array_keys($res))) {
                unset($res[$j]);
              }
            }
          }
        }
      }
    }*/

    return $res;
  }

}