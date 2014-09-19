<div class="act-box-item">
  <div class="act-post-type">
    <div class="act-post-type-screenshot">
      <?php if (!empty($vars['image'])): ?>
        <img src="<?php echo $vars['image']; ?>">
      <?php endif; ?>
    </div>
  </div>
  <div class="act-post-type-info">
    <h3><?php echo $vars['title']; ?></h3>
    <p><?php echo $vars['description']; ?></p>
  </div>
  <div class="act-post-type-actions">
    <a class="button button-primary customize load-customize hide-if-no-customize" href="<?php echo $vars['url']; ?>"><?php _e('Select', 'act'); ?></a>
  </div>
</div>
