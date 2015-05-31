<div class="wrap">
	<h2>
		<?php _e( 'Add new page type', 'papi' ); ?>

		<label class="screen-reader-text" for="add-new-page-search">
			<?php _e( 'Search page types', 'papi' ); ?>
		</label>

		<input placeholder="<?php _e( 'Search page types', 'papi' ); ?>..." type="search" name="add-new-page-search"
		       id="add-new-page-search" class="papi-search">
	</h2>

	<div class="papi-box-list">
		<?php
		$page_types = papi_get_all_page_types();

		foreach ( $page_types as $key => $page_type ) {
			if ( ! papi_display_page_type( $page_type ) ) {
				continue;
			}

			papi_include_template( 'includes/admin/views/partials/add-new-item.php', [
				'title'       => $page_type->name,
				'description' => $page_type->description,
				'thumbnail'   => $page_type->get_thumbnail(),
				'url'         => papi_get_page_new_url( $page_type->get_id() )
			] );
		}

		$post_type = papi_get_post_type();

		if ( papi_filter_settings_standard_page_type( $post_type ) ) {
			papi_include_template( 'includes/admin/views/partials/add-new-item.php', [
				'title'       => papi_filter_standard_page_name( $post_type ),
				'description' => papi_filter_standard_page_description( $post_type ),
				'thumbnail'   => papi_filter_standard_page_thumbnail( $post_type ),
				'url'         => 'post-new.php' . papi_get_page_query_strings( '?' ) . '&papi-bypass=true'
			] );

		}
		?>
	</div>
</div>
