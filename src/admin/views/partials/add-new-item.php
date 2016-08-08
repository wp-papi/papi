<a href="<?php echo esc_attr( $vars['url'] ); ?>" class="papi-box-item">
	<?php if ( $vars['use_thumbnail'] ): ?>
	<div class="papi-page-type hide">
		<?php if ( ! empty( $vars['thumbnail'] ) ): ?>
			<div class="papi-page-type-screenshot" style="background-image:url(<?php echo esc_html( $vars['thumbnail'] ); ?>)"></div>
		<?php else: ?>
			<div class="papi-page-type-screenshot">
				<div>
					<div><?php esc_html_e( 'Thumbnail missing', 'papi' ); ?></div>
				</div>
			</div>
		<?php endif; ?>
	</div>
	<?php endif; ?>

	<div class="papi-page-type-info">
		<h3><?php echo esc_html( $vars['title'] ); ?></h3>
		<p><?php echo esc_html( $vars['description'] ); ?></p>
	</div>

	<div class="papi-page-type-actions">
		<span class="button button-primary"><?php esc_html_e( 'Select', 'papi' ); ?></span>
	</div>
</a>
