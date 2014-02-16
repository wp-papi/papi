<div id="wrap">
  <h2>Add new page</h2>
  <p>Select the type of page to create from the list.</p>
  <?php $page_types = ptb_get_all_page_types(); ?>
  <ul class="ptb-box-list">
    <?php foreach ($page_types as $key => $value): ?>
      <li>
        <a href="<?php echo get_ptb_page_new_url($value->file_name); ?>"><?php echo $value->page_type->name; ?></a>
        <p><?php echo $value->page_type->description; ?></p>
      </li>
    <?php endforeach; ?>
  </ul>
</div>

