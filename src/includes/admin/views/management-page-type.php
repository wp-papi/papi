<?php

if ( ! function_exists( 'papi_management_page_type_render_box' ) ) {

	/**
	 * Render management box table.
	 *
	 * @param array $properties
	 * @param bool $tab
	 *
	 */
	function papi_management_page_type_render_box( $properties, $tab = false ) {
		?>
		<table class="papi-table">
			<thead>
				<tr>
					<?php if ( $tab ): ?>
						<th><?php _e( 'Tab Title', 'papi' ); ?></th>
						<th><?php _e( 'Sort Order', 'papi' ); ?></th>
						<th><?php _e( 'Properties', 'papi' ); ?></th>
					<?php else: ?>
						<th><?php _e( 'Title', 'papi' ); ?></th>
						<th><?php _e( 'Type', 'papi' ); ?></th>
						<th><?php _e( 'Slug', 'papi' ); ?></th>
						<th><?php _e( 'Sort Order', 'papi' ); ?></th>
					<?php endif; ?>
				</tr>
			</thead>
			<tbody>
				<?php
				$properties = papi_populate_properties( $properties );

				if ( $tab ) {
					$properties = papi_setup_tabs( $properties );
				}

				foreach ( $properties as $property ): ?>
					<tr>
						<?php if ( isset( $property->options ) && isset( $property->options->title ) ): ?>
							<td>
								<?php echo esc_html( $property->options->title ); ?>
								<br />
								<br />
								<?php echo __( 'Properties', 'papi' ) . ': ' . strval( count( $property->properties ) ); ?>
							</td>
							<td>
								<?php echo esc_html( $property->options->sort_order ); ?>
							</td>
							<td>
								<?php papi_management_page_type_render_box( $property->properties ); ?>
							</td>
						<?php else: ?>
							<td><?php echo esc_html( $property->title ); ?></td>
							<td><?php echo esc_html( $property->type ); ?></td>
							<td><?php echo esc_html( $property->get_slug( true ) ); ?></td>
							<td><?php echo esc_html( $property->sort_order ); ?></td>
						<?php endif; ?>
					</tr>
				<?php endforeach; ?>
				</tr>
			</tbody>
		</table>
		<?php
	}
}

?>
<div class="wrap">
	<div class="papi-options-logo"></div>
	<h1><?php echo papi()->name; ?></h1>

	<br/>

	<?php
	$page_type = papi_get_qs( 'page_type' );
	$page_type = papi_get_page_type_by_id( $page_type );

	if ( empty( $page_type ) ):
	?>
		<h3><?php _e( 'Cannot find the page type', 'papi' );?></h3>
		<p>
			<a href="tools.php?page=papi"><?php echo esc_html( 'Back to list' , 'papi' ); ?></a>
		</p>
	<?php
		else:
	?>
		<h3><?php _e( 'Overview of page type', 'papi' ); ?>: <?php echo esc_html( $page_type->name ); ?></h3>

		<p>
			<a href="tools.php?page=papi"><?php echo esc_html( 'Back to list' , 'papi' ); ?></a>
		</p>

		<?php

		$boxes = $page_type->get_boxes();

		if ( empty( $boxes ) ) {
			echo sprintf( '<p>%s</p>', esc_html__( 'No meta boxes exists.', 'papi' ) );
			return;
		}

		foreach ( $boxes as $box ):
			$tab 			= isset( $box[1] ) && isset( $box[1][0] ) && isset( $box[1][0]->tab ) && $box[1][0]->tab;
			$top_right_text = __( 'Properties', 'papi' );

			if ( $tab ) {
				$top_right_text = __( 'Tabs', 'papi' );
			}

			if ( ! isset( $box['title'] ) || empty( $box['title'] ) ) {
				continue;
			}

			$counter = count( papi_get_box_property( $box[1] ) );
			?>
			<div class="postbox papi-box papi-management-box">
				<div class="handlediv" title="Click to toggle">
					<br>
				</div>
				<h3 class="hndle">
					<span><?php echo esc_html( $box['title'] ); ?></span>
					<span class="papi-pull-right"><?php echo esc_html( $top_right_text . ': ' ) . strval( $counter ); ?></span>
				</h3>
				<div class="inside">
					<?php papi_management_page_type_render_box( $box[1], $tab ); ?>
				</div>
			</div>

		<?php endforeach; ?>

	<?php endif; ?>
</div>
