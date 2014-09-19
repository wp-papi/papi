<?php
  $post_type = _act_get_wp_post_type();

  // Check if we should show standard page or not.
  $settings = _act_get_settings();
  $show_standard_page = true;
  if (isset($settings[$post_type]) && isset($settings[$post_type]['show_standard_page'])) {
    $show_standard_page = $settings[$post_type]['show_standard_page'];
  }

?>

<div class="wrap">

  <h2>
    <?php echo __('Add new page type', 'act'); ?>

    <label class="screen-reader-text" for="add-new-page-search">
      <?php _e('Search page types', 'act'); ?>
    </label>

    <input placeholder="<?php _e('Search page types', 'act'); ?>..." type="search" name="add-new-page-search" id="add-new-page-search" class="act-search">
  </h2>

  <div class="act-box-list">
    <?php
      $page_types = _act_get_all_page_types();

      foreach ($page_types as $key => $page_type) {
        _act_include_template('includes/admin/views/partials/add-new-item.php', array(
              'title'       => $page_type->name,
              'description' => $page_type->description,
              'image'       => $page_type->get_thumbnail(),
              'url'         => _act_get_page_new_url ($page_type->file_name, $post_type)
        ));
      }

      if ($show_standard_page) {
        _act_include_template('includes/admin/views/partials/add-new-item.php', array(
          'title'       => __('Standard page', 'act'),
          'description' => __('Just the normal WordPress page', 'act'),
          'image'       => _act_page_type_default_thumbnail(),
          'url'         => 'post-new.php?post_type=page'
        ));

      }
    ?>
  </div>
</div>

