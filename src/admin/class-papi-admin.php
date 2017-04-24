<?php

/**
 * Admin class that handle loading of Papi types,
 * columns and loading of posts.
 */
final class Papi_Admin {

	/**
	 * The entry type.
	 *
	 * @var Papi_Entry_Type
	 */
	protected $entry_type;

	/**
	 * The post type.
	 *
	 * @var string
	 */
	protected $meta_type;

	/**
	 * The construct.
	 */
	public function __construct() {
		$this->load_files();
		$this->setup_actions();
		$this->setup_filters();
	}

	/**
	 * Cloning is forbidden.
	 *
	 * @codeCoverageIgnore
	 */
	public function __clone() {
		_doing_it_wrong( __FUNCTION__, esc_html__( 'Cheatin&#8217; huh?', 'papi' ), '3.0' );
	}

	/**
	 * Unserializing instances of this class is forbidden.
	 *
	 * @codeCoverageIgnore
	 */
	public function __wakeup() {
		_doing_it_wrong( __FUNCTION__, esc_html__( 'Cheatin&#8217; huh?', 'papi' ), '3.0' );
	}

	/**
	 * Preboot all types and setup the current type if any.
	 */
	public function admin_init() {
		$meta_type  = papi_get_meta_type();
		$meta_type  = ucfirst( $meta_type );
		$class_name = 'Papi_Admin_Entry_' . $meta_type;

		// A custom class is not required, e.g
		// options don't have one.
		if ( class_exists( $class_name ) ) {
			$class = call_user_func( [$class_name, 'instance'] );

			if ( ! $class->setup() ) {
				return;
			}
		}

		// Setup entry type.
		if ( $entry_type = $this->get_entry_type() ) {
			$entry_type->setup();
		}
	}

	/**
	 * Add custom body class when it's a page type.
	 *
	 * @param  string $classes
	 *
	 * @return string
	 */
	public function admin_body_class( $classes ) {
		if ( $entry_type = $this->get_entry_type() ) {
			$classes .= sprintf( ' papi-body papi-meta-type-%s', papi_get_meta_type() );

			// Add custom css classes from entry type.
			$arr = $entry_type->get_body_classes();
			$arr = is_string( $arr ) ? [ $arr ] : $arr;
			$arr = is_array( $arr ) ? $arr : [];

			$classes .= ' ' . implode( ' ', $arr );


			// Add custom css classes from query string.
			if ( $css = papi_get_qs( 'papi_css' ) ) {
				$css = is_array( $css ) ? $css : [];
				$css = array_map( 'sanitize_text_field', $css );
				$classes .= ' ' . implode( ' ', $css );
			}
		}

		return $classes;
	}

	/**
	 * Output Papi page type hidden field.
	 */
	public function edit_form_after_title() {
		wp_nonce_field( 'papi_save_data', 'papi_meta_nonce' );

		if ( $value = esc_attr( papi_get_entry_type_id() ) ) {
			papi_render_html_tag( 'input', [
				'data-papi-page-type-key' => true,
				'name'                    => esc_attr( papi_get_page_type_key() ),
				'type'                    => 'hidden',
				'value'                   => $value
			] );
		}
	}

	/**
	 * Get Entry Type instance.
	 *
	 * @return Papi_Entry_Type|false
	 */
	protected function get_entry_type() {
		if ( $this->entry_type instanceof Papi_Entry_Type ) {
			return $this->entry_type;
		}

		$entry_type_id = papi_get_entry_type_id();

		// If the entry type id is empty try to load
		// the entry type id from `page` query string.
		//
		// Example:
		//   /wp-admin/options-general.php?page=papi/option/site-option-type
		if ( empty( $entry_type_id ) ) {
			$entry_type_id = preg_replace( '/^papi\/\w+\//', '', papi_get_qs( 'page' ) );
		}

		// Use the default entry type id if empty.
		if ( empty( $entry_type_id ) ) {
			$entry_type_id = papi_get_entry_type_id();
		}

		// If no entry type id exists Papi can't setup a entry type.
		if ( empty( $entry_type_id ) ) {
			return false;
		}

		$entry_type = papi_get_entry_type_by_id( $entry_type_id );

		if ( $entry_type instanceof Papi_Entry_Type === false ) {
			return false;
		}

		$this->entry_type = $entry_type;

		return $entry_type;
	}

	/**
	 * Load admin files that are not loaded by the autoload.
	 */
	protected function load_files() {
		require_once __DIR__ . '/class-papi-admin-meta-handler.php';
		require_once __DIR__ . '/class-papi-admin-option-handler.php';
		require_once __DIR__ . '/class-papi-admin-entry-post.php';
		require_once __DIR__ . '/class-papi-admin-entry-taxonomy.php';
		require_once __DIR__ . '/class-papi-admin-columns.php';
		require_once __DIR__ . '/class-papi-admin-page-type-switcher.php';
	}

	/**
	 * Add docs links to plugin row meta.
	 *
	 * @param  array  $links
	 * @param  string $file
	 *
	 * @return array
	 */
	public function plugin_row_meta( array $links, $file ) {
		if ( $file === PAPI_PLUGIN_BASENAME ) {
			return array_merge( $links, [
				'docs' => '<a href="' . esc_url( 'https://wp-papi.github.io/docs/' ) . '" title="' . esc_attr( __( 'View Papi Documentation', 'papi' ) ) . '">' . __( 'Docs', 'papi' ) . '</a>',
			] );
		}

		return $links;
	}

	/**
	 * Setup actions.
	 */
	protected function setup_actions() {
		add_action( 'admin_init', [$this, 'admin_init'] );
		add_action( 'edit_form_after_title', [$this, 'edit_form_after_title'] );

		if ( $taxonomy = papi_get_taxonomy() ) {
			add_action( $taxonomy . '_add_form', [$this, 'edit_form_after_title'] );
			add_action( $taxonomy . '_edit_form', [$this, 'edit_form_after_title'] );
		}
	}

	/**
	 * Setup filters.
	 */
	protected function setup_filters() {
		add_filter( 'admin_body_class', [$this, 'admin_body_class'] );
		add_filter( 'plugin_row_meta', [$this, 'plugin_row_meta'], 10, 2 );
		add_filter( 'wp_link_query', [$this, 'wp_link_query'] );
		add_filter( 'wp_refresh_nonces', [$this, 'wp_refresh_nonces'], 11 );
	}

	/**
	 * Filter the link query results.
	 *
	 * @param  array $results
	 *
	 * @return array
	 */
	public function wp_link_query( array $results ) {
		foreach ( $results as $index => $value ) {
			$post_type = papi_get_post_type( $value['ID'] );
			$name      = papi_get_page_type_name( $value['ID'] );

			if ( empty( $name ) ) {
				$name = papi_filter_settings_standard_page_type_name( $post_type );
			}

			$results[$index]['info'] = esc_html( $name );
		}

		return $results;
	}

	/**
	 * Check nonce expiration on the New/Edit Post screen and refresh if needed.
	 *
	 * @param  array $response
	 *
	 * @return array
	 */
	public function wp_refresh_nonces( array $response ) {
		if ( ! array_key_exists( 'wp-refresh-post-nonces', $response ) ) {
			return $response;
		}

		$response['wp-refresh-post-nonces']['replace']['papi_meta_nonce'] = wp_create_nonce( 'papi_save_data' );

		return $response;
	}
}

if ( papi_is_admin() ) {
	new Papi_Admin;
}
