<?php

class Papi_Admin_Page_Type_Switcher {

	/**
	 * The construct.
	 */
	public function __construct() {
		add_action( 'admin_init', [$this, 'admin_init'] );
	}

	/**
	 * Admin init hook callback.
	 */
	public function admin_init() {
		add_action( 'post_submitbox_misc_actions', [$this, 'metabox'] );
		add_action( 'save_post', [$this, 'save_post'], 10, 2 );
	}

	/**
	 * Render metabox.
	 */
	public function metabox() {
		$post_type     = papi_get_post_type();
		$page_type     = papi_get_entry_type_by_id( papi_get_page_type_id() );
		$page_type_key = papi_get_page_type_key( 'switch' );
		$page_types    = papi_get_all_page_types( $post_type );
		$show_standard = papi_filter_settings_show_standard_page_type( $post_type );

		if ( $show_standard ) {
			$standard_page_type = papi_get_standard_page_type( $post_type );
			$page_types[]       = $standard_page_type;

			if ( empty( $page_type ) ) {
				$page_type = $standard_page_type;
			}
		}

		usort( $page_types, function ( $a, $b ) {
			return strcmp( $a->name, $b->name );
		} );

		$page_types = papi_sort_order( array_reverse( $page_types ) );

		// Don't do anything without any page types.
		if ( empty( $page_type ) || empty( $page_types ) ) {
			return;
		} ?>

		<div class="misc-pub-section misc-pub-section-last papi-page-type-switcher">
			<label for="<?php echo esc_attr( $page_type_key ); ?>"><?php esc_html_e( 'Page Type:', 'papi' ); ?></label>
			<span><?php echo esc_html( $page_type->name ); ?></span>

			<?php if ( papi_current_user_is_allowed( $page_type->capabilities ) && $page_type->switcher ): ?>
				<a href="#" id="papi-page-type-switcher-edit" class="hide-if-no-js"><?php esc_html_e( 'Edit', 'papi' ); ?></a>
				<div>
					<select name="<?php echo esc_attr( $page_type_key ); ?>" id="<?php echo esc_attr( $page_type_key ); ?>">
						<?php
						foreach ( $page_types as $pt ) {
							if ( ! papi_current_user_is_allowed( $pt->capabilities ) ) {
								continue;
							}

							papi_render_html_tag( 'option', [
								'selected' => $page_type->match_id( $pt->get_id() ),
								'value'    => esc_attr( $pt->get_id() ),

								esc_html( $pt->name )
							] );
						}
						?>
					</select>
					<a href="#" id="papi-page-type-switcher-save" class="hide-if-no-js button"><?php esc_html_e( 'OK', 'papi' ); ?></a>
					<a href="#" id="papi-page-type-switcher-cancel" class="hide-if-no-js"><?php esc_html_e( 'Cancel', 'papi' ); ?></a>
				</div>
			<?php endif; ?>
		</div>
		<?php
	}

	/**
	 * Switch page type if all checks pass.
	 *
	 * @param  int     $post_id
	 * @param  WP_post $post
	 *
	 * @return bool
	 */
	public function save_post( $post_id, $post ) {
		// Check if post id and post object is empty or not.
		if ( empty( $post_id ) || empty( $post ) ) {
			return false;
		}

		// Check if our nonce is vailed.
		if ( ! wp_verify_nonce( papi_get_sanitized_post( 'papi_meta_nonce' ), 'papi_save_data' ) ) {
			return false;
		}

		// Check if so both page type keys exists.
		if ( empty( $_POST[papi_get_page_type_key()] ) || empty( $_POST[papi_get_page_type_key( 'switch' )] ) ) {
			return false;
		}

		// Page type information.
		$page_type_id        = sanitize_text_field( $_POST[papi_get_page_type_key()] );
		$page_type_switch_id = sanitize_text_field( $_POST[papi_get_page_type_key( 'switch' )] );

		// Don't update if the same ids.
		if ( $page_type_id === $page_type_switch_id ) {
			return false;
		}

		// Fetch right page type if standard page type id.
		if ( papi_get_standard_page_type_id( $post->post_type ) === $page_type_id ) {
			$page_type = papi_get_standard_page_type( $post->post_type );
		} else {
			$page_type = papi_get_entry_type_by_id( $page_type_id );
		}

		// Fetch right page type switch if standard page type id.
		if ( papi_get_standard_page_type_id( $post->post_type ) === $page_type_switch_id ) {
			$page_type_switch    = papi_get_standard_page_type( $post->post_type );
			$page_type_switch_id = '';
		} else {
			$page_type_switch = papi_get_entry_type_by_id( $page_type_switch_id );
		}

		$post_type_object = get_post_type_object( $post->post_type );

		// Check if page type and post type is not empty.
		if ( empty( $page_type_switch ) || empty( $post_type_object ) ) {
			return false;
		}

		// Check if autosave.
		if ( wp_is_post_autosave( $post_id ) ) {
			return false;
		}

		// Check if revision.
		if ( wp_is_post_revision( $post_id ) ) {
			return false;
		}

		// Check if revision post type.
		if ( in_array( $post->post_type, ['revision', 'nav_menu_item'], true ) ) {
			return false;
		}

		// Check so page type has the post type.
		if ( ! $page_type->has_post_type( $post->post_type ) || ! $page_type_switch->has_post_type( $post->post_type ) ) {
			return false;
		}

		// Check page type capabilities.
		if ( ! papi_current_user_is_allowed( $page_type_switch->capabilities ) ) {
			return false;
		}

		// Check so user can edit posts and that the user can publish posts on the post type.
		if ( ! current_user_can( 'edit_post', $post_id ) || ! current_user_can( $post_type_object->cap->publish_posts ) ) {
			return false;
		}

		// Get properties.
		$properties        = $page_type->get_properties();
		$properties_switch = $page_type_switch->get_properties();

		// Delete only properties that don't have the same type and slug.
		foreach ( $properties as $property ) {
			$delete = true;

			// Check if the properties are the same or not.
			foreach ( $properties_switch as $property_switch ) {
				if ( $property_switch->type === $property->type && $property_switch->match_slug( $property->get_slug() ) ) {
					$delete = false;
					break;
				}
			}

			if ( ! $delete ) {
				continue;
			}

			// Delete property values.
			$property->delete_value( $property->get_slug( true ), $post_id, papi_get_meta_type() );
		}

		// Delete page type switch id.
		if ( empty( $page_type_switch_id ) ) {
			return delete_post_meta( $post_id, papi_get_page_type_key() );
		}

		// Update page type id.
		return papi_set_page_type_id( $post_id, $page_type_switch_id );
	}
}

if ( papi_is_admin() ) {
	new Papi_Admin_Page_Type_Switcher;
}
