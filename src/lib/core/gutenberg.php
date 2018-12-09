<?php

/**
 * Determine if gutenberg is active or not.
 *
 * @return bool
 */
function papi_is_gutenberg_page() {
	return function_exists( 'the_block_editor_meta_box_post_form_hidden_fields' );
}
