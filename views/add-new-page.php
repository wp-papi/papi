<?php
  // Get the right name for the normal page.
  $post_type = _ptb_get_wp_post_type();
  $post_type_obj = get_post_type_object($post_type);
  $name = $post_type_obj->labels->singular_name;
  $normal_title = apply_filters('ptb_normal_title', 'Normal ' . $name);
  $normal_desc = apply_filters('ptb_normal_description', 'Just the Normal ' . $name . ' page');
?>

<div id="wrap">
  <h2>Add new <?php echo $name; ?></h2>
  <p>Select the type of page to create from the list or search: <input type="text" name="add-new-page-search" class="ptb-search" /></p>
  <?php $page_types = _ptb_get_all_page_types(); ?>
  <ul class="ptb-box-list">
    <?php foreach ($page_types as $key => $value): ?>
      <li>
        <a href="<?php echo _ptb_get_page_new_url ($value->file_name, $post_type); ?>"><?php echo $value->name; ?></a>
        <p><?php echo $value->description; ?></p>
      </li>
    <?php endforeach; ?>

    <li>
      <a href="post-new.php?post_type=page"><?php echo __($normal_title, 'ptb'); ?></a>
      <p><?php echo __($normal_desc, 'ptb'); ?></p>
    </li>
  </ul>
</div>

