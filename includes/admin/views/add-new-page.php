<?php
$post_type = _papi_get_wp_post_type();

// Check if we should show standard page or not.
$settings           = _papi_get_settings();
$show_standard_page = true;
if ( isset( $settings[ $post_type ] ) && isset( $settings[ $post_type ]['show_standard_page'] ) ) {
	$show_standard_page = $settings[ $post_type ]['show_standard_page'];
}

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
		$page_types = _papi_get_all_page_types();

		foreach ( $page_types as $key => $page_type ) {
			_papi_include_template( 'includes/admin/views/partials/add-new-item.php', array(
				'title'       => $page_type->name,
				'description' => $page_type->description,
				'image'       => $page_type->get_thumbnail(),
				'url'         => _papi_get_page_new_url( $page_type->file_name )
			) );
		}

		if ( $show_standard_page ) {
			_papi_include_template( 'includes/admin/views/partials/add-new-item.php', array(
				'title'       => __( 'Standard page', 'papi' ),
				'description' => __( 'Just the normal WordPress page', 'papi' ),
				'image'       => _papi_page_type_default_thumbnail(),
				'url'         => 'post-new.php' . _papi_get_page_query_strings('?')
			) );

		}
		?>
	</div>
</div>

