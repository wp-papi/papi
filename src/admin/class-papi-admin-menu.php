<?php

/**
 * Admin class that handles admin menus.
 */
final class Papi_Admin_Menu {

	/**
	 * The construct.
	 */
	public function __construct() {
		$this->setup_actions();
	}

	/**
	 * Fill labels on admin bar.
	 */
	public function admin_bar_menu() {
		if ( $entry_type = $this->get_entry_type() ) {
			$this->override_labels( $entry_type );
		}
	}

	/**
	 * Get current type class.
	 *
	 * @return Papi_Entry_Type
	 */
	protected function get_entry_type() {
		if ( $entry_type = papi_get_entry_type_by_id( papi_get_entry_type_id() ) ) {
			return $entry_type;
		}
	}

	/**
	 * Override labels with labels from the entry type.
	 *
	 * @param Papi_Entry_Type $entry_type
	 */
	protected function override_labels( Papi_Entry_Type $entry_type ) {
		global $wp_post_types, $wp_taxonomies;

		if ( $entry_type->type === 'taxonomy' ) {
			$meta_type_value = papi_get_taxonomy();
		} else {
			$meta_type_value = papi_get_post_type();
		}

		if ( empty( $meta_type_value ) || ( ! isset( $wp_post_types[$meta_type_value] ) && ! isset( $wp_taxonomies[$meta_type_value] ) ) ) {
			return;
		}

		foreach ( $entry_type->get_labels() as $key => $value ) {
			if ( empty( $value ) ) {
				continue;
			}

			if ( $entry_type->type === 'taxonomy' && isset( $wp_taxonomies[$meta_type_value]->labels->$key ) ) {
				$wp_taxonomies[$meta_type_value]->labels->$key = $value;
			} else if ( isset( $wp_post_types[$meta_type_value]->labels->$key ) ) {
				$wp_post_types[$meta_type_value]->labels->$key = $value;
			}
		}
	}

	/**
	 * Page items menu.
	 *
	 * This function will register all entry types
	 * that has a fake post type. Like option types.
	 */
	public function page_items_menu() {
		$entry_types = papi_get_all_entry_types( [
			'mode'  => 'exclude',
			'types' => 'page'
		] );

		foreach ( $entry_types as $entry_type ) {
			if ( empty( $entry_type->menu ) || empty( $entry_type->name ) ) {
				continue;
			}

			$slug = sprintf(
				'papi/%s/%s',
				$entry_type->get_type(),
				$entry_type->get_id()
			);

			add_submenu_page(
				$entry_type->menu,
				$entry_type->name,
				$entry_type->name,
				$entry_type->capability,
				$slug,
				[$entry_type, 'render']
			);
		}
	}

	/**
	 * Setup menu items for real post types.
	 */
	public function post_types_menu() {
		global $submenu;

		$post_types = papi_get_post_types();

		foreach ( $post_types as $post_type ) {
			if ( ! post_type_exists( $post_type ) ) {
				continue;
			}

			if ( $post_type === 'post' ) {
				$edit_url = 'edit.php';
			} else {
				$edit_url = 'edit.php?post_type=' . $post_type;
			}

			if ( ! isset( $submenu[$edit_url], $submenu[$edit_url][10], $submenu[$edit_url][10][2] ) ) {
				$post_type_object = get_post_type_object( $post_type );

				if ( $post_type_object->show_in_menu !== true ) {
					$submenu[$edit_url] = [
						10 => [
							__( 'Add New', 'papi' ),
							'edit_posts',
							'post-new.php'
						]
					];
				} else {
					continue;
				}
			}

			$only_page_type = papi_filter_settings_only_page_type( $post_type );
			$page_types     = papi_get_all_page_types( $post_type );
			$show_standard  = false;

			// Don't change menu item when no page types is found.
			if ( empty( $page_types ) ) {
				continue;
			}

			if ( count( $page_types ) === 1 && empty( $only_page_type ) ) {
				$show_standard  = papi_filter_settings_show_standard_page_type( $post_type );
				$only_page_type = $show_standard ? '' : $page_types[0]->get_id();
			}

			if ( ! empty( $only_page_type ) && ! $show_standard ) {
				$submenu[$edit_url][10][2] = papi_get_page_new_url(
					$only_page_type,
					false,
					$post_type,
					[
						'post_parent',
						'lang'
					]
				);
			} else {
				$page  = 'papi-add-new-page,' . $post_type;
				$start = strpos( $edit_url, 'post_type' ) === false ? '?' : '&';

				$submenu[$edit_url][10][2] = sprintf(
					'%s%spage=%s',
					$edit_url,
					$start,
					$page
				);

				// Add menu item.
				add_menu_page(
					__( 'Add New', 'papi' ),
					__( 'Add New', 'papi' ),
					'read',
					$page,
					[$this, 'render_view']
				);

				// Remove the menu item so it's hidden.
				remove_menu_page( $page );
			}
		}
	}

	/**
	 * Menu callback that loads right view depending on what the `page` query string says.
	 */
	public function render_view() {
		if ( strpos( papi_get_qs( 'page' ), 'papi' ) !== false ) {
			$page = str_replace( 'papi-', '', papi_get_qs( 'page' ) );
			$res  = preg_replace( '/\,.*/', '', $page );

			if ( is_string( $res ) ) {
				$page_view = $res;
			}
		}

		if ( ! isset( $page_view ) ) {
			$page_view = null;
		}

		if ( ! is_null( $page_view ) ) {
			$view = new Papi_Admin_View;
			$view->render( $page_view );
		} else {
			echo '<h2>Papi - 404</h2>';
		}
	}

	/**
	 * Setup actions.
	 */
	protected function setup_actions() {
		if ( papi_is_admin() ) {
			add_action( 'admin_init', [$this, 'admin_bar_menu'] );
			add_action( 'admin_menu', [$this, 'page_items_menu'] );
			add_action( 'admin_menu', [$this, 'post_types_menu'] );
		} else {
			add_action( 'admin_bar_menu', [$this, 'admin_bar_menu'] );
		}
	}
}

new Papi_Admin_Menu;
