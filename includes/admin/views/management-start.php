<div class="wrap">
	<div class="papi-options-logo"></div>
	<h2><?php echo papi()->name; ?></h2>

	<br/>

	<h3>Page types</h3>
	<table class="wp-list-table widefat papi-options-table">
		<thead>
		<tr>
			<th>
				<strong>Name</strong>
			</th>
			<th>
				<strong>Page type</strong>
			</th>
			<th>
				<strong>Template</strong>
			</th>
			<th>
				<strong>Number of pages</strong>
			</th>
		</tr>
		</thead>
		<tbody>
		<?php
		$page_types = _papi_get_all_page_types( true );
		foreach ( $page_types as $key => $page_type ) {
			?>
			<tr>
				<td><?php echo $page_type->name; ?></td>
				<td><?php echo $page_type->file_name; ?></td>
				<td><?php
					if ( ! current_user_can( 'edit_themes' ) || defined( 'DISALLOW_FILE_EDIT' ) && DISALLOW_FILE_EDIT ) {
						echo $page_type->template;
					} else {
						$theme_dir  = get_template_directory();
						$theme_name = basename( $theme_dir );
						$url        = site_url() . '/wp-admin/theme-editor.php?file=' . $page_type->template . '&theme=' . $theme_name;
						if ( file_exists( $theme_dir . '/' . $page_type->template ) ):
							?>
							<a href="<?php echo $url; ?>"><?php echo $page_type->template; ?></a>
						<?php
						else:
							echo 'Missing';
						endif;
					}
					?></td>
				<td><?php echo _papi_get_number_of_pages( $page_type->file_name ); ?></td>
			</tr>
		<?php
		}
		?>
		</tbody>
	</table>
</div>
