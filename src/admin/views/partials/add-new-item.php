<a href="<?php echo $vars['url']; ?>" class="papi-box-item">
	<?php if ( ! empty( $vars['thumbnail'] ) ): ?>
	<div class="papi-page-type">
		<div class="papi-page-type-screenshot" style="background-image:url(<?php echo $vars['thumbnail']; ?>)">
		</div>
	</div>
	<?php endif; ?>
	<div class="papi-page-type-info">
		<h3><?php echo $vars['title']; ?></h3>

		<p><?php echo $vars['description']; ?></p>
	</div>
	<div class="papi-page-type-actions">
		<span class="button button-primary"><?php _e( 'Select', 'papi' ); ?></span>
	</div>
</a>
