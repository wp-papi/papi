<?php

/**
 * Admin class that handles admin meta boxes.
 */
final class Papi_Admin_Block {

	/**
	 * The core block.
	 *
	 * @var Papi_Core_Block
	 */
	protected $block;

	/**
	 * The constructor.
	 *
	 * @param Papi_Core_Block $block
	 */
	public function __construct( Papi_Core_Block $block ) {
		// Check if the current user is allowed to view this block.
		if ( ! papi_current_user_is_allowed( $block->capabilities ) ) {
			return;
		}

		if ( $block->display ) {
			$this->block = $block;
			$this->setup_actions();
			$this->setup_block();
		}
	}

	/**
	 * Get post type.
	 *
	 * @return string
	 */
	protected function get_post_type() {
		if ( papi_get_meta_type() === 'post' ) {
			if ( $post_id = papi_get_post_id() ) {
				return get_post_type( $post_id );
			}

			if ( $post_type = papi_get_post_type() ) {
				return $post_type;
			}
		}

		return $this->block->id;
	}

	/**
	 * Setup actions.
	 */
	protected function setup_actions() {

	}

	/**
	 * Setup block.
	 */
	public function setup_block() {
		register_block_type( $this->block->name, [
			'attributes' => $this->block->attributes,
			'render_callback' => [$this, 'render_block'],
		] );
	}

	/**
	 * Render block.
	 *
	 * @param  array  $attributes
	 * @param  string $content
	 * @param  bool   $is_preview
	 * @param  int    $post_id
	 */
	public function render_block( $attributes, $content = '', $is_preview = false, $post_id = 0 ) {
		if ( ! $post_id ) {
			$post_id = get_the_ID();
		}

		ob_start();
		papi_render_properties( $this->block->properties );
		return ob_get_clean();
	}
}
