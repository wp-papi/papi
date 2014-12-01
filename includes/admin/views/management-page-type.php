<?php

function _papi_management_page_type_render_box( $properties, $tab = false ) {
	?>
	<table class="papi-table">
		<thead>
			<tr>
				<?php if ( $tab ): ?>
					<th><?php _e( 'Tab title', 'papi' ); ?></th>
					<th><?php _e( 'Sort order', 'papi' ); ?></th>
					<th><?php _e( 'Properties', 'papi' ); ?></th>
				<?php else: ?>
					<th><?php _e( 'Title', 'papi' ); ?></th>
					<th><?php _e( 'Type', 'papi' ); ?></th>
					<th><?php _e( 'Slug', 'papi' ); ?></th>
					<th><?php _e( 'Sort order', 'papi' ); ?></th>
				<?php endif; ?>
			</tr>
		</thead>
		<tbody>
				<?php

					$properties = _papi_populate_properties( $properties );

					if ( $tab ) {
						$properties = _papi_setup_tabs( $properties );
					}

					foreach ( $properties as $property ): ?>
					<tr>
						<?php if ( isset( $property->options ) && isset( $property->options->title ) ): ?>
							<td>
								<?php echo $property->options->title; ?>
								<br />
								<br />
								<?php echo __( 'Properties', 'papi' ) . ': ' . strval( count( $property->properties ) ); ?>
							</td>
							<td>
								<?php echo $property->options->sort_order; ?>
							</td>
							<td>
								<?php _papi_management_page_type_render_box( $property->properties ); ?>
							</td>
						<?php else: ?>
							<td><?php echo $property->title; ?></td>
							<td><?php echo $property->type; ?></td>
							<td><?php echo _papi_remove_papi( $property->slug ); ?></td>
							<td><?php echo $property->sort_order; ?></td>
						<?php endif; ?>
					</tr>
				<?php endforeach; ?>
			</tr>
		</tbody>
	</table>
	<?php
}
?>

<div class="wrap">
	<div class="papi-options-logo"></div>
	<h2><?php echo papi()->name; ?></h2>

	<br/>

	<?php
		$page_type = $_GET['page-type'];
		$page_type = _papi_get_file_path( $page_type );
		$page_type = _papi_get_page_type( $page_type );
	?>

	<h3><?php _e( 'Overview of page type', 'papi' ); ?>: <?php echo $page_type->name; ?></h3>

	<p>
		<a href="tools.php?page=papi"><?php _e( 'Back to list' , 'papi' ); ?></a>
	</p>

	<?php

	$boxes = $page_type->get_boxes();

	if ( empty( $boxes ) ) {
		echo '<p>' . __( 'No meta boxes exists.', 'papi' ) . '</p>';
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

			$counter = count( _papi_get_box_property( $box[1] ) );
		?>
		<div class="postbox papi-box papi-management-box">
			<div class="handlediv" title="Click to toggle">
				<br>
			</div>
			<h3 class="hndle">
				<span><?php echo $box['title']; ?></span>
				<span class="papi-pull-right"><?php echo $top_right_text . ': ' . strval( $counter ); ?></span>
			</h3>
			<div class="inside">
				<?php
					_papi_management_page_type_render_box( $box[1], $tab );
				?>
			</div>
		</div>

	<?php endforeach; ?>

</div>
