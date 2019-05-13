<?php

/**
 * Admin class that handle meta data, like post or term.
 */
final class Papi_Admin_Blocks_Handler extends Papi_Core_Data_Handler {

	/**
	 * Setup actions.
	 */
	protected function setup_actions() {
		#add_action('content_save_pre', [$this, 'content_pre_save'], 99);
	}

	public function content_pre_save($content) {
		$reg = '/<!--\s+wp:papi\/core\s+({[\S\s]+?})\s+\/-->/';
		$clean = addslashes(preg_replace($reg, '', stripslashes($content)));

		if (!preg_match($reg, stripslashes( $content ), $matches)) {
			return $clean;
		}

		$data = json_decode($matches[1]);

		if (!isset($data->post_id)) {
			return $clean;
		}

		if (isset($data->entry_type)) {
			update_post_meta($data->post_id, papi_get_page_type_key(), $data->entry_type);
		}

		return $clean;
	}
}
