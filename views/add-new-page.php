<div id="wrap">
  <h2>Add new page</h2>
  <p>Select the type of page to create from the list or search: <input type="text" name="add-new-page-search" class="ptb-search" /></p>
  <?php $page_types = get_ptb_all_page_types(); ?>
  <ul class="ptb-box-list">
    <?php foreach ($page_types as $key => $value): ?>
      <li>
        <a href="<?php echo get_ptb_page_new_url($value->file_name); ?>"><?php echo $value->page_type->name; ?></a>
        <p><?php echo $value->page_type->description; ?></p>
      </li>
    <?php endforeach; ?>
    <li>
      <a href="post-new.php?post_type=page"><?php echo __('Normal page', 'ptb'); ?></a>
      <p><?php echo __('Just a normal WordPress Page', 'ptb'); ?></p>
    </li>
  </ul>
</div>

