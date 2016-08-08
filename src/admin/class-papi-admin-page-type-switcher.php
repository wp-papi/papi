<?php

class Papi_Admin_Page_Type_Switcher {

	/**
	 * The construct.
	 */
	public function __construct() {
		$this->setup_actions();
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

		// Don't do anything without any page types.
		if ( empty( $page_types ) ) {
			return;
		} ?>

		<div class="misc-pub-section misc-pub-section-last papi-page-type-switcher">
			<label for="<?php echo $page_type_key; ?>"><?php esc_html_e( 'Page Type:', 'papi' ); ?></label>
			<span><?php echo esc_html( $page_type->name ); ?></span>

			<?php if ( papi_current_user_is_allowed( $page_type->capabilities ) && $page_type->switcher ): ?>
				<a href="#" id="papi-page-type-switcher-edit" class="hide-if-no-js"><?php esc_html_e( 'Edit', 'papi' ); ?></a>
				<div>
					<select name="<?php echo $page_type_key; ?>" id="<?php echo $page_type_key; ?>">
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
	 */
	public function save_post( $post_id, $post ) {
		// Check if our nonce is vailed.
		if ( ! wp_verify_nonce( papi_get_sanitized_post( 'papi_meta_nonce' ), 'papi_save_data' ) ) {
			return $data;
		}

		// Check if so both page type keys exists.
		if ( empty( $_POST[papi_get_page_type_key()] ) || empty( $_POST[papi_get_page_type_key( 'switch' )] ) ) {
			return;
		}

		// Page type information.
		$page_type_id        = sanitize_text_field( $_POST[papi_get_page_type_key()] );
		$page_type_switch_id = sanitize_text_field( $_POST[papi_get_page_type_key( 'switch' )] );

		// Don't update if the same ids.
		if ( $page_type_id === $page_type_switch_id ) {
			return;
		}

		$page_type        = papi_get_entry_type_by_id( $page_type_id );
		$page_type_switch = papi_get_entry_type_by_id( $page_type_switch_id );
		$post_type_object = get_post_type_object( papi_get_post_type() );

		// Check if page type and post type is not empty.
		if ( empty( $page_type_switch ) || empty( $post_type_object ) ) {
			return;
		}

		// Check so user can edit posts.
		if ( ! current_user_can( 'edit_post', $post_id ) ) {
			return;
		}

		// Check if autosave.
		if ( wp_is_post_autosave( $post_id ) ) {
			return;
		}

		// Check if revision.
		if ( wp_is_post_revision( $post_id ) ) {
			return;
		}

		// Check if revision post type.
		if ( in_array( $post->post_type, array( $post_type, 'revision' ), true ) ) {
			return;
		}

		// Check so user can publish post.
		if ( ! current_user_can( $post_type_object->cap->publish_posts ) ) {
			return;
		}

		// Check page type capabilities.
		if ( ! papi_current_user_is_allowed( $page_type_switch->capabilities ) ) {
			return;
		}

		// Check if any properties exists.
		if ( ! ( $properties = $page_type->get_properties() ) ) {
			return;
		}

		// Check for properties on the new page type.
		if ( ! ( $properties_switch = $page_type_switch->get_properties() ) ) {
			return;
		}

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

			papi_delete_property_meta_value( $post_id, $property->get_slug() );
		}

		// Update page type id.
		papi_set_page_type_id( $post_id, $page_type_switch_id );
	}

	/**
	 * Setup action hooks.
	 */
	protected function setup_actions() {
		add_action( 'admin_init', [$this, 'admin_init'] );
	}
}

if ( is_admin() ) {
	new Papi_Admin_Page_Type_Switcher;
}
