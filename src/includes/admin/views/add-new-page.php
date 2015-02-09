<?php
$post_type = papi_get_wp_post_type();

// Check if we should show standard page or not.
$show_standard_page = papi_filter_settings_standard_page_type( $post_type );
?>

<div class="wrap">

	<h2>
		<?php echo __( 'Add new page type', 'papi' ); ?>

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

			if ( ! papi_filter_show_page_type( $post_type, $page_type ) ) {
				continue;
			}

			papi_include_template( 'includes/admin/views/partials/add-new-item.php', array(
				'title'       => $page_type->name,
				'description' => $page_type->description,
				'thumbnail'   => $page_type->get_thumbnail(),
				'url'         => papi_get_page_new_url( $page_type->get_filename() )
			) );
		}

		if ( $show_standard_page ) {
			papi_include_template( 'includes/admin/views/partials/add-new-item.php', array(
				'title'       => papi_filter_standard_page_name( $post_type ),
				'description' => papi_filter_standard_page_description( $post_type ),
				'thumbnail'   => papi_filter_standard_page_thumbnail( $post_type ),
				'url'         => 'post-new.php' . papi_get_page_query_strings( '?' ) . '&papi-bypass=true'
			) );

		}
		?>
	</div>
</div>
