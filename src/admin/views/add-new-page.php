<?php
$post_type_name = papi_get_post_type();
$post_type      = get_post_type_object( $post_type_name );
$post_type      = empty( $post_type ) ? get_post_type_object( 'page' ) : $post_type;

$parent_page_type = papi_get_entry_type_by_id( papi_get_entry_type_id() );
$page_types       = papi_get_all_page_types( $post_type_name );
$show_standard    = papi_filter_settings_show_standard_page_type( $post_type_name );

if ( $parent_page_type instanceof Papi_Page_Type ) {
	$child_types = $parent_page_type->get_child_types();
	$page_types  = empty( $child_types ) ? $page_types : $child_types;

	if ( ! $show_standard ) {
		$show_standard = $parent_page_type->standard_type;
	}
}

if ( $show_standard ) {
	$page_types[] = papi_get_standard_page_type( $post_type_name );
}

usort( $page_types, function ( $a, $b ) {
	return strcmp( $a->name, $b->name );
} );

$page_types = papi_sort_order( array_reverse( $page_types ) );
?>

<div class="wrap">
	<h1>
		<?php echo esc_html( sprintf( __( 'Add New %s', 'papi' ), $post_type->labels->singular_name ) ); ?>

		<label class="screen-reader-text" for="add-new-page-search">
			<?php echo esc_html( $post_type->labels->search_items ); ?>
		</label>

		<input placeholder="<?php echo esc_html( $post_type->labels->search_items ); ?>..." type="search" name="add-new-page-search" id="add-new-page-search" class="papi-search">
	</h1>

	<ul class="papi-box-tags">
		<?php
			$tags = [];

			foreach ( $page_types as $key => $page_type ) {
				if ( ! papi_display_page_type( $page_type ) ) {
					continue;
				}

				foreach ( $page_type->tags as $tag ) {
					$tags[] = $tag;
				}
			}

			$tags = array_unique( $tags );

			foreach ( $tags as $tag ) {
				echo sprintf( '<li><a href="#" data-tag="%s">%s</a></li>', esc_attr( $tag ), esc_html( $tag ) );
			}
		?>
	</ul>

	<div class="papi-box-list">
		<?php
		$use_thumbnail = false;

		foreach ( $page_types as $key => $page_type ) {
			if ( ! empty( $page_type->get_thumbnail() ) ) {
				$use_thumbnail = true;
			}
		}

		foreach ( $page_types as $key => $page_type ) {
			if ( ! papi_display_page_type( $page_type ) ) {
				continue;
			}

			papi_include_template( 'admin/views/partials/add-new-item.php', [
				'tags'          => $page_type->tags,
				'title'         => $page_type->name,
				'description'   => $page_type->description,
				'thumbnail'     => $page_type->get_thumbnail(),
				'url'           => papi_get_page_new_url( $page_type->get_id(), true, null ),
				'use_thumbnail' => $use_thumbnail
			] );
		}
		?>
	</div>
</div>
