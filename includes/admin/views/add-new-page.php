<?php
  $post_type = _ptb_get_wp_post_type();

  // Check if we should show standard page or not.
  $settings = _ptb_get_settings();
  $show_standard_page = true;
  if (isset($settings[$post_type]) && isset($settings[$post_type]['show_standard_page'])) {
    $show_standard_page = $settings[$post_type]['show_standard_page'];
  }

?>

<div class="wrap">

  <h2>
    <?php echo __('Add new page type', 'ptb'); ?>

    <label class="screen-reader-text" for="add-new-page-search">
      <?php _e('Search page types', 'ptb'); ?>
    </label>

    <input placeholder="<?php _e('Search page types', 'ptb'); ?>..." type="search" name="add-new-page-search" id="add-new-page-search" class="ptb-search">
  </h2>

  <div class="ptb-box-list">
    <?php
      $page_types = _ptb_get_all_page_types();

      foreach ($page_types as $key => $page_type) {
        echo _ptb_apply_template('includes/admin/views/partials/add-new-item.php', array(
              'title'       => $page_type->name,
              'description' => $page_type->description,
              'image'       => $page_type->get_thumbnail(),
              'url'         => _ptb_get_page_new_url ($page_type->file_name, $post_type)
        ));
      }

      if ($show_standard_page) {
        echo _ptb_apply_template('includes/admin/views/partials/add-new-item.php', array(
              'title'       => __('Standard page', 'ptb'),
              'description' => __('Just the normal WordPress page', 'ptb'),
              'image'       => _ptb_page_type_default_thumbnail(),
              'url'         => 'post-new.php?post_type=page'
        ));
      }
    ?>
  </div>
</div>

