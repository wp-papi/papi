<a href="<?php echo $vars['url']; ?>" class="papi-box-item">
	<?php if ( $vars['use_thumbnail'] ): ?>
	<div class="papi-page-type hide">
		<?php if ( ! empty( $vars['thumbnail'] ) ): ?>
			<div class="papi-page-type-screenshot" style="background-image:url(<?php echo $vars['thumbnail']; ?>)"></div>
		<?php else: ?>
			<div class="papi-page-type-screenshot">
				<div>
					<div><?php _e( 'Thumbnail missing', 'papi' ); ?></div>
				</div>
			</div>
		<?php endif; ?>
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
