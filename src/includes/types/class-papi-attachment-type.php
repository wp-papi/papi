<?php

// Exit if accessed directly
defined( 'ABSPATH' ) || exit;

/**
 * Papi Attachment Type class.
 *
 * @package Papi
 */
class Papi_Attachment_Type extends Papi_Page_Type {

	/**
	 * The post type to register the type with.
	 *
	 * @var string
	 */
	public $post_type = 'attachment';

	/**
	 * The constructor.
	 *
	 * @param string $file_path
	 */
	public function __construct( $file_path = '' ) {
		parent::__construct( $file_path );
		$this->setup_filters();
	}

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
	private function setup_filters() {
		if ( isset( $_GET['post'] ) || $this->papi->exists( 'core.type.attachment' ) ) {
			return;
		}

		add_filter( 'attachment_fields_to_edit', [$this, 'edit_attachment'], 10, 2 );
		add_filter( 'attachment_fields_to_save', [$this, 'save_attachment'], 10, 2 );

		$this->papi->singleton( 'core.type.attachment', $this );

		echo '<pre>';
		var_dump($this->papi);
		exit;
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
			$form_fields['papi-media-title-' . uniqid()] = [
				'label' => '',
				'input' => 'html',
				'html'  => '<h4 class="papi-media-title">' . $box[0]['title'] . '</h4>'
			];

			$properties = isset( $box[1][0]->properties ) ?
				$box[1][0]->properties : $box[1];

			foreach ( $properties as $prop ) {
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
	 * @param  array $attachment
	 *
	 * @return array
	 */
	public function save_attachment( $post, $attachment ) {
		update_post_meta( $post['ID'], papi_get_page_type_key(), $this->get_id() );
		$handler = new Papi_Admin_Post_Handler();
		$handler->save_meta_boxes( $post['ID'], $post );
		return $post;
	}
}
