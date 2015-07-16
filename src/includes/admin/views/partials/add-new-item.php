<div class="papi-box-item">
	<?php if ( ! empty( $vars['thumbnail'] ) ): ?>
	<div class="papi-post-type">
		<div class="papi-post-type-screenshot" style="background-image:url(<?php echo $vars['thumbnail']; ?>)">
		</div>
	</div>
	<?php endif; ?>
	<div class="papi-post-type-info">
		<h3><?php echo $vars['title']; ?></h3>

		<p><?php echo $vars['description']; ?></p>
	</div>
	<div class="papi-post-type-actions">
		<a class="button button-primary" href="<?php echo $vars['url']; ?>"><?php _e( 'Select', 'papi' ); ?></a>
	</div>
</div>
