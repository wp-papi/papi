<div class="papi-box-item">
	<div class="papi-post-type">
		<div class="papi-post-type-screenshot">
			<?php if ( ! empty( $vars['thumbnail'] ) ): ?>
				<img src="<?php echo $vars['thumbnail']; ?>">
			<?php endif; ?>
		</div>
	</div>
	<div class="papi-post-type-info">
		<h3><?php echo $vars['title']; ?></h3>

		<p><?php echo $vars['description']; ?></p>
	</div>
	<div class="papi-post-type-actions">
		<a class="button button-primary customize load-customize hide-if-no-customize"
		   href="<?php echo $vars['url']; ?>"><?php _e( 'Select', 'papi' ); ?></a>
	</div>
</div>
