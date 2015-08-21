<?php
$post_type_name = papi_get_post_type();
$post_type      = get_post_type_object( $post_type_name );
$post_type      = empty( $post_type ) ? get_post_type_object( 'page' ) : $post_type;
?>
<div class="wrap">
	<h2>
		<?php echo sprintf( __( 'Add New %s', 'papi' ), $post_type->labels->singular_name ); ?>

		<label class="screen-reader-text" for="add-new-page-search">
			<?php echo $post_type->labels->search_items; ?>
		</label>

		<input placeholder="<?php echo $post_type->labels->search_items; ?>..." type="search" name="add-new-page-search"
		       id="add-new-page-search" class="papi-search">
	</h2>

	<div class="papi-box-list">
		<?php
		$page_types = papi_get_all_page_types();

		if ( $parent_post_id = papi_get_post_parent_id() ) {
			$page_type  = papi_get_page_type_by_post_id();

			if ( papi_is_page_type( $page_type ) ) {
				$page_types = $page_type->get_page_types();
			}
		}

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

		if ( papi_filter_settings_show_standard_page_type( $post_type_name ) ) {
			papi_include_template( 'includes/admin/views/partials/add-new-item.php', [
				'title'       => papi_filter_settings_standard_page_name( $post_type_name ),
				'description' => papi_filter_settings_standard_page_description( $post_type_name ),
				'thumbnail'   => papi_filter_settings_standard_page_thumbnail( $post_type_name ),
				'url'         => 'post-new.php' . papi_get_page_query_strings( '?' ) . '&papi-bypass=true'
			] );
		}
		?>
	</div>
</div>
