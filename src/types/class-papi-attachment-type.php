<?php

/**
 * Papi type that handle attachment post type, post data
 * and rendering. All attachment types should extend this
 * class.
 */
class Papi_Attachment_Type extends Papi_Page_Type {

	/**
	 * The post type to register the type with.
	 *
	 * @var string
	 */
	public $post_type = 'attachment';

	/**
	 * The type name.
	 *
	 * @var string
	 */
	public $type = 'attachment';

	/**
	 * Get post type.
	 *
	 * @return string
	 */
	public function get_post_type() {
		return $this->post_type[0];
	}

	/**
	 * Setup filters.
	 */
	protected function setup_filters() {
		// Don't add any filters on post.php page.
		if ( ! isset( $_GET['post'] ) ) {
			add_filter( 'attachment_fields_to_edit', [$this, 'edit_attachment'], 10, 2 );
			add_filter( 'attachment_fields_to_save', [$this, 'save_attachment'], 10 );
		}
	}

	/**
	 * Add attachment fields.
	 *
	 * @param  array   $form_fields
	 * @param  WP_Post $post
	 *
	 * @return array
	 */
	public function edit_attachment( $form_fields, $post ) {
		foreach ( $this->get_boxes() as $box ) {
			if ( ! empty( $box->title ) ) {
				$form_fields['papi-media-title-' . uniqid()] = [
					'label' => '',
					'input' => 'html',
					'html'  => '<h4 class="papi-media-title">' . $box->title . '</h4>'
				];
			}

			foreach ( $box->properties as $prop ) {
				// Raw output is required.
				$prop->raw = true;

				// Set post id to the property.
				$prop->set_post_id( $post->ID );

				// Add property to form fields.
				$form_fields[$prop->get_slug()] = [
					'label' => $prop->title,
					'input' => 'html',
					'helps' => $prop->description,
					'html'  => papi_maybe_get_callable_value(
						'papi_render_property',
						$prop
					)
				];
			}
		}

		// Add nonce field.
		$form_fields['papi_meta_nonce'] = [
			'label' => '',
			'input' => 'html',
			'html'  => sprintf(
				'<input name="papi_meta_nonce" type="hidden" value="%s" />',
				wp_create_nonce( 'papi_save_data' )
			)
		];

		return $form_fields;
	}

	/**
	 * Save attachment post data.
	 *
	 * @param  array $post
	 *
	 * @return array
	 */
	public function save_attachment( $post ) {
		update_post_meta( $post['ID'], papi_get_page_type_key(), $this->get_id() );

		$handler = new Papi_Admin_Meta_Handler();
		$handler->save_meta_boxes( $post['ID'], $post );

		return $post;
	}

	/**
	 * Check if the entry type is a singleton.
	 *
	 * @return bool
	 */
	public function singleton() {
		$key = sprintf( 'entry_type_id.post_type.%s', $this->get_post_type() );

		if ( papi()->exists( $key ) ) {
			return true;
		}

		papi()->singleton( $key, $this->get_id() );

		return false;
	}
}
