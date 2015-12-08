<?php

/**
 * Admin class that handle loading of Papi types,
 * columns and loading of posts.
 */
final class Papi_Admin {

	/**
	 * The page type.
	 *
	 * @var Papi_Page_Type
	 */
	private $page_type;

	/**
	 * The page type id.
	 *
	 * @var string
	 */
	private $page_type_id;

	/**
	 * The post type.
	 *
	 * @var string
	 */
	private $post_type;

	/**
	 * The constructor.
	 */
	public function __construct() {
		$this->load_files();
		$this->setup_globals();
		$this->setup_actions();
		$this->setup_filters();
	}

	/**
	 * Cloning is forbidden.
	 *
	 * @codeCoverageIgnore
	 */
	public function __clone() {
		_doing_it_wrong( __FUNCTION__, __( 'Cheatin&#8217; huh?', 'papi' ), '2.4.10' );
	}

	/**
	 * Unserializing instances of this class is forbidden.
	 *
	 * @codeCoverageIgnore
	 */
	public function __wakeup() {
		_doing_it_wrong( __FUNCTION__, __( 'Cheatin&#8217; huh?', 'papi' ), '2.4.10' );
	}

	/**
	 * Admin init.
	 *
	 * Setup the page type.
	 */
	public function admin_init() {
		// Preload all page types.
		foreach ( papi_get_post_types() as $post_type ) {
			papi_get_all_page_types( false, $post_type );
		}

		if ( ! $this->setup_papi() ) {
			return;
		}

		$this->page_type->setup();
	}

	/**
	 * Add custom body class when it's a page type.
	 *
	 * @param  string $classes
	 *
	 * @return string
	 */
	public function admin_body_class( $classes ) {
		if ( ! in_array( $this->post_type, papi_get_post_types() ) ) {
			return $classes;
		}

		if ( count( get_page_templates() ) ) {
			$classes .= 'papi-hide-cpt';
		}

		return $classes;
	}

	/**
	 * Output Papi page type hidden field.
	 *
	 * This will only output on a post type page.
	 */
	public function edit_form_after_title() {
		wp_nonce_field( 'papi_save_data', 'papi_meta_nonce' );

		papi_render_html_tag( 'input', [
			'data-papi-page-type-key' => true,
			'name'                    => esc_attr( papi_get_page_type_key() ),
			'type'                    => 'hidden',
			'value'                   => esc_attr( papi_get_page_type_id() )
		] );
	}

	/**
	 * Output hidden meta boxes.
	 */
	public function hidden_meta_boxes() {
		global $_wp_post_type_features;
		if ( ! isset( $_wp_post_type_features[$this->post_type]['editor'] ) ) {
			add_meta_box(
				'papi-hidden-editor',
				'Papi hidden editor',
				[$this, 'hidden_meta_box_editor'],
				$this->post_type
			);
		}
	}

	/**
	 * Output hidden WordPress editor.
	 */
	public function hidden_meta_box_editor() {
		wp_editor( '', 'papiHiddenEditor' );
	}

	/**
	 * Load admin files that are not loaded by the autoload.
	 */
	private function load_files() {
		require_once __DIR__ . '/class-papi-admin-ajax.php';
		require_once __DIR__ . '/class-papi-admin-management-pages.php';
		require_once __DIR__ . '/class-papi-admin-post-handler.php';
		require_once __DIR__ . '/class-papi-admin-option-handler.php';
	}

	/**
	 * Load post new action
	 * Redirect to right url if no page type is set.
	 */
	public function load_post_new() {
		$request_uri = $_SERVER['REQUEST_URI'];
		$post_types = papi_get_post_types();

		if ( in_array( $this->post_type, $post_types ) && strpos( $request_uri, 'page_type=' ) === false ) {
			$parsed_url = parse_url( $request_uri );

			$only_page_type = papi_filter_settings_only_page_type( $this->post_type );
			$page_types     = papi_get_all_page_types( false, $this->post_type );
			$show_standard  = false;

			if ( count( $page_types ) === 1 && empty( $only_page_type ) ) {
				$show_standard  = $page_types[0]->standard_type;
				$show_standard  = $show_standard ?
					papi_filter_settings_show_standard_page_type( $this->post_type ) :
					$show_standard;

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
			is_admin() && exit;
		}
	}

	/**
	 * Add custom table header to page type.
	 *
	 * @param  array $defaults
	 *
	 * @return array
	 */
	public function manage_page_type_posts_columns( $defaults ) {
		if ( ! in_array( $this->post_type, papi_get_post_types() ) ) {
			return $defaults;
		}

		$defaults['page_type'] = papi_filter_settings_page_type_column_title(
			$this->post_type
		);

		return $defaults;
	}

	/**
	 * Add custom table column to page type.
	 *
	 * @param string $column_name
	 * @param int    $post_id
	 */
	public function manage_page_type_posts_custom_column( $column_name, $post_id ) {
		if ( ! in_array( $this->post_type, papi_get_post_types() ) ) {
			return;
		}

		if ( $column_name === 'page_type' ) {
			$page_type = papi_get_page_type_by_post_id( $post_id );

			if ( ! is_null( $page_type ) ) {
				echo esc_html( $page_type->name );
			} else {
				echo esc_html( papi_filter_settings_standard_page_name(
					papi_get_post_type()
				) );
			}
		}
	}

	/**
	 * Filter page types in post type list.
	 */
	public function restrict_page_types() {
		$post_types = papi_get_post_types();

		if ( in_array( $this->post_type, $post_types ) ) {
			$page_types = papi_get_all_page_types( false, $this->post_type );

			$page_types = array_map( function ( $page_type ) {
				return [
					'name' => $page_type->name,
					'value' => $page_type->get_id()
				];
			}, $page_types );

			// Add the standard page that isn't a real page type.
			if ( papi_filter_settings_show_standard_page_type_in_filter( $this->post_type ) ) {
				$page_types[] = [
					'name'  => papi_filter_settings_standard_page_name( $this->post_type ),
					'value' => 'papi-standard-page'
				];
			}

			usort( $page_types, function ( $a, $b ) {
				return strcmp(
					strtolower( $a['name'] ),
					strtolower( $b['name'] )
				);
			} );
			?>
			<select name="page_type" class="postform">
				<option value="0" selected><?php _e( 'All types', 'papi' ); ?></option>
				<?php
				foreach ( $page_types as $page_type ) {
					printf(
						'<option value="%s" %s>%s</option>',
						$page_type['value'],
						papi_get_qs( 'page_type' ) === $page_type['value'] ? ' selected' : '',
						$page_type['name']
					);
				}
				?>
			</select>
			<?php
		}
	}

	/**
	 * Filter posts on load if `page_type` query string is set.
	 *
	 * @param  WP_Query $query
	 *
	 * @return WP_Query
	 */
	public function pre_get_posts( $query ) {
		global $pagenow;

		if ( $pagenow === 'edit.php' && ! is_null( papi_get_qs( 'page_type' ) ) ) {
			if ( papi_get_qs( 'page_type' ) === 'papi-standard-page' ) {
				$query->set( 'meta_query', [
					[
						'key'     => papi_get_page_type_key(),
						'compare' => 'NOT EXISTS'
					]
				] );
			} else {
				$query->set( 'meta_key', papi_get_page_type_key() );
				$query->set( 'meta_value', papi_get_qs( 'page_type' ) );
			}
		}

		return $query;
	}

	/**
	 * Setup actions.
	 */
	private function setup_actions() {
		if ( is_admin() ) {
			add_action( 'admin_init', [$this, 'admin_init'] );
			add_action( 'edit_form_after_title', [$this, 'edit_form_after_title'] );
			add_action( 'load-post-new.php', [$this, 'load_post_new'] );
			add_action( 'restrict_manage_posts', [ $this, 'restrict_page_types'] );
			add_action( 'add_meta_boxes', [$this, 'hidden_meta_boxes'], 10 );
		}
	}

	/**
	 * Setup filters.
	 */
	private function setup_filters() {
		if ( is_admin() ) {
			add_filter( 'admin_body_class', [$this, 'admin_body_class'] );
			add_filter( 'pre_get_posts', [$this, 'pre_get_posts'] );
			add_filter( 'wp_link_query', [$this, 'wp_link_query'] );
			add_filter( 'manage_' . $this->post_type . '_posts_columns', [
				$this,
				'manage_page_type_posts_columns'
			] );
			add_action( 'manage_' . $this->post_type . '_posts_custom_column', [
				$this,
				'manage_page_type_posts_custom_column'
			], 10, 2 );
		}
	}

	/**
	 * Setup globals.
	 */
	private function setup_globals() {
		$this->post_type = papi_get_post_type();

		if ( is_admin() ) {
			$this->page_type_id  = papi_get_page_type_id();
		}
	}

	/**
	 * Load right Papi file if it exists.
	 *
	 * @return bool
	 */
	public function setup_papi() {
		// If the post type isn't in the post types array we can't proceed.
		if ( in_array( $this->post_type, ['revision', 'nav_menu_item'] ) ) {
			return false;
		}

		if ( empty( $this->page_type_id ) ) {
			// If only page type is used, override the page type value.
			$this->page_type_id = papi_filter_settings_only_page_type(
				$this->post_type
			);

			if ( empty( $this->page_type_id ) ) {
				// Load page types that don't have any real post type.
				$this->page_type_id = str_replace(
					'papi/',
					'',
					papi_get_qs( 'page' )
				);
			}

			if ( empty( $this->page_type_id ) ) {
				$this->page_type_id = papi_get_page_type_id();
			}
		}

		if ( empty( $this->page_type_id ) ) {
			return false;
		}

		$this->page_type = papi_get_page_type_by_id( $this->page_type_id );

		// Do a last check so we can be sure that we have a page type instance.
		return $this->page_type instanceof Papi_Page_Type;
	}

	/**
	 * Filter the link query results.
	 *
	 * @param  array $results
	 *
	 * @return array
	 */
	public function wp_link_query( $results ) {
		$post_type = papi_get_post_type();

		foreach ( $results as $index => $value ) {
			$name = papi_get_page_type_name( $value['ID'] );

			if ( empty( $name ) ) {
				$name = papi_filter_settings_standard_page_name( $post_type );
			}

			$results[$index]['info'] = esc_html( $name );
		}

		return $results;
	}
}

new Papi_Admin;
