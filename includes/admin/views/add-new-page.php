<?php
  // Get the right name for the normal page.
  $post_type = _ptb_get_wp_post_type();
  $post_type_obj = get_post_type_object($post_type);
  $name = $post_type_obj->labels->singular_name;

  $settings = _ptb_get_settings();

  // Check if we should show standard page or not.
  $show_standard_page = true;
  if (isset($settings[$post_type]) && isset($settings[$post_type]['show_standard_page'])) {
    $show_standard_page = $settings[$post_type]['show_standard_page'];
  }
?>

<div class="wrap">
  <h2><?php echo __('Add new', 'ptb') . ' ' . $name; ?></h2>
  <div class="ptb-panel">
    <p><?php echo __('Select the page type to create from the list or search', 'ptb'); ?>: <input type="text" name="add-new-page-search" placeholder="Page type name" class="ptb-search" /></p>
  </div>
  <?php $page_types = _ptb_get_all_page_types(); ?>
  <ul class="ptb-box-list">
    <?php foreach ($page_types as $key => $value): ?>
      <li data-ptb-href="<?php echo _ptb_get_page_new_url ($value->file_name, $post_type); ?>">
        <div class="ptb-pull-left">
          <img src="<?php echo $value->get_image(); ?>" alt="<?php echo $value->name; ?>" />
        </div>
        <div class="ptb-pull-right">
          <h4><?php echo $value->name; ?></h4>
          <p><?php echo $value->description; ?></p>
        </div>
      </li>
    <?php endforeach; ?>

    <?php if ($show_standard_page): ?>
    <li data-ptb-href="post-new.php?post_type=page">
      <div class="ptb-pull-left">
        <img src="<?php echo _ptb_page_type_default_image(); ?>" alt="<?php echo $value->name; ?>" />
      </div>
      <div class="ptb-pull-right">
        <h4><?php _e('Standard Page', 'ptb'); ?></h4>
        <p><?php _e('Just the normal WordPress page', 'ptb'); ?></p>
      </div>
    </li>
    <?php endif; ?>
  </ul>
</div>

