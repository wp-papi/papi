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
		$parent_page_type = papi_get_page_type_by_id( papi_get_page_type_id() );
		$page_types       = papi_get_all_page_types();
		$show_standard    = papi_filter_settings_show_standard_page_type( $post_type_name );

		if ( papi_is_page_type( $parent_page_type ) ) {
			$child_types = $parent_page_type->get_child_types();
			$page_types = empty( $child_types ) ? $page_types : $child_types;

			if ( ! $show_standard ) {
				$show_standard = $parent_page_type->standard_type;
			}
		}

		if ( $show_standard ) {
			$id                     = sprintf( 'papi-standard-%s-type', $post_type_name );
			$page_type              = new Papi_Page_Type( $id );
			$page_type->id          = $id;
			$page_type->name        = papi_filter_settings_standard_page_name( $post_type_name );
			$page_type->description = papi_filter_settings_standard_page_description( $post_type_name );
			$page_type->thumbnail   = papi_filter_settings_standard_page_thumbnail( $post_type_name );
			$page_type->post_type   = [$post_type_name];
			$page_types[]           = $page_type;
		}

		usort( $page_types, function ( $a, $b ) {
			return strcmp( $a->name, $b->name );
		} );

		$page_types = papi_sort_order( array_reverse( $page_types ) );

		foreach ( $page_types as $key => $page_type ) {
			if ( ! papi_display_page_type( $page_type ) ) {
				continue;
			}

			papi_include_template( 'admin/views/partials/add-new-item.php', [
				'title'       => $page_type->name,
				'description' => $page_type->description,
				'thumbnail'   => $page_type->get_thumbnail(),
				'url'         => papi_get_page_new_url( $page_type->get_id() )
			] );
		}
		?>
	</div>
</div>
