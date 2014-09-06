<div class="ptb-box-item">
  <div class="ptb-post-type">
    <div class="ptb-post-type-screenshot">
      <?php if (!empty($image)): ?>
        <img src="<?php echo $image; ?>">
      <?php endif; ?>
    </div>
  </div>
  <div class="ptb-post-type-info">
    <h3><?php echo $title; ?></h3>
    <p><?php echo $description; ?></p>
  </div>
  <div class="ptb-post-type-actions">
    <a class="button button-primary customize load-customize hide-if-no-customize" href="<?php echo $url; ?>"><?php _e('Select', 'ptb'); ?></a>
  </div>
</div>