<?php

class Papi_Admin_Entry_Post extends Papi_Admin_Entry {

	/**
	 * The post type.
	 *
	 * @var string
	 */
	protected $post_type;

	/**
	 * The construct.
	 */
	public function __construct() {
		$this->post_type = papi_get_post_type();

		$this->setup_actions();
		$this->setup_filters();
	}

	/**
	 * Filters the fields displayed in the post revision diff UI.
	 *
	 * @param  array   $return
	 * @param  WP_Post $compare_from
	 * @param  WP_Post $compare_to
	 *
	 * @return array
	 */
	public function get_revision_ui_diff( $return, $compare_from, $compare_to ) {
		$meta = get_post_meta( $compare_from->ID );
		$meta = is_array( $meta ) ? $meta : [];

		foreach ( $meta as $key => $value ) {
			if ( $key[0] === '_' && $key !== papi_get_page_type_key() ) {
				continue;
			}

			$content_from = papi_data_get( $compare_from->ID, $key );
			$content_to = papi_data_get( $compare_to->ID, $key );

			$diff = wp_text_diff( $content_from, $content_to, [
				'show_split_view' => true
			] );

			if ( $diff ) {
				$return[] = [
					'id'   => $key,
					'name' => $key,
					'diff' => $diff,
				];
			}
		}

		return $return;
	}

	/**
	 * Output hidden meta boxes.
	 */
	public function hidden_meta_boxes() {
		global $_wp_post_type_features;

		if ( isset( $_wp_post_type_features[$this->post_type]['editor'] ) ) {
			return;
		}

		add_meta_box( 'papi-hidden-editor', 'Papi hidden editor', [$this, 'hidden_meta_box_editor'], $this->post_type );
	}

	/**
	 * Output hidden WordPress editor.
	 */
	public function hidden_meta_box_editor() {
		wp_editor( '', 'papiHiddenEditor' );
	}

	/**
	 * Load post new action
	 * Redirect to right url if no page type is set.
	 */
	public function load_post_new() {
		$request_uri = $_SERVER['REQUEST_URI'];
		$post_types  = papi_get_post_types();

		if ( in_array( $this->post_type, $post_types, true ) && strpos( $request_uri, 'page_type=' ) === false ) {
			$parsed_url = parse_url( $request_uri );

			$only_page_type = papi_filter_settings_only_page_type( $this->post_type );
			$page_types     = papi_get_all_page_types( $this->post_type );
			$show_standard  = false;

			if ( count( $page_types ) === 1 && empty( $only_page_type ) ) {
				$show_standard  = $page_types[0]->standard_type;
				$show_standard  = $show_standard ? papi_filter_settings_show_standard_page_type( $this->post_type ) : $show_standard;
				$only_page_type = $show_standard ? '' : $page_types[0]->get_id();
			}

			// Check if we should show one post type or not and
			// create the right url for that.
			if ( ! empty( $only_page_type ) && ! $show_standard ) {
				$url = papi_get_page_new_url( $only_page_type, false );
			} else {
				$page = 'page=papi-add-new-page,' . $this->post_type;

				if ( $this->post_type !== 'post' ) {
					$page = '&' . $page;
				}

				$url = 'edit.php?' . $parsed_url['query'] . $page;
			}

			wp_safe_redirect( $url );
			papi_is_admin() && exit;
		}
	}

	/**
	 * Redirect post location when post is in iframe mode.
	 *
	 * @param  string $location
	 *
	 * @return string
	 */
	public function redirect_post_location( $location ) {
		if ( ! isset( $_SERVER['HTTP_REFERER'] ) ) {
			return $location;
		}

		$referer = $_SERVER['HTTP_REFERER'];
		$referer = strtolower( $referer );

		if ( strpos( $referer, 'papi-iframe-mode' ) === false ) {
			return $location;
		}

		return sprintf( '%s&papi_css[]=papi-iframe-mode', $location );
	}

	/**
	 * Setup admin entry.
	 */
	public function setup() {
		// Preload all page types.
		foreach ( papi_get_post_types() as $post_type ) {
			papi_get_all_entry_types( [
				'args' => $post_type
			] );
		}

		return ! in_array( $this->post_type, ['revision', 'nav_menu_item'], true );
	}

	/**
	 * Setup blocks.
	 *
	 * @return void
	 */
	public function setup_blocks() {
		$entry_type = papi_get_current_entry_type();

		if ( empty( $entry_type ) ) {
			return;
		}

		?>
		<script type="text/javascript">
			window.papi = window.papi || {};
			window.papi.blocks = <?php echo json_encode( $entry_type->get_blocks() ); ?>;
		</script>
		<?php

		$entry_type->setup_blocks();
	}

	/**
	 * Setup actions.
	 */
	protected function setup_actions() {
		add_action( 'load-post-new.php', [$this, 'load_post_new'] );
		add_action( 'add_meta_boxes', [$this, 'hidden_meta_boxes'], 10 );
		add_action( 'redirect_post_location', [$this, 'redirect_post_location'] );
		add_action( 'admin_head', [$this, 'setup_blocks'] );
	}

	/**
	 * Setup filters.
	 */
	protected function setup_filters() {
		add_filter( 'wp_get_revision_ui_diff', [$this, 'get_revision_ui_diff'], 10, 3 );
	}
}
