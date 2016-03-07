<?php

/**
 * Output html for properties tests.
 */
function properties_output_html() {
	?>
	<p>Hello, callable!</p>
	<?php
}

/**
 * Say Hello!
 */
function say_hello_stub() {
	echo 'Hello';
}

/**
 * Say Hello!
 *
 * @param string $name
 */
function say_hello_name_stub( $name ) {
	echo "Hello $name";
}

/**
 * Register a book post type.
 */
function papi_test_register_book_post_type() {
	global $wp_post_types;

	$labels = [
		'name'               => _x( 'Books', 'post type general name', 'papi-tests' ),
		'singular_name'      => _x( 'Book', 'post type singular name', 'papi-tests' ),
		'menu_name'          => _x( 'Books', 'admin menu', 'papi-tests' ),
		'name_admin_bar'     => _x( 'Book', 'add new on admin bar', 'papi-tests' ),
		'add_new'            => _x( 'Add New', 'book', 'papi-tests' ),
		'add_new_item'       => __( 'Add New Book', 'papi-tests' ),
		'new_item'           => __( 'New Book', 'papi-tests' ),
		'edit_item'          => __( 'Edit Book', 'papi-tests' ),
		'view_item'          => __( 'View Book', 'papi-tests' ),
		'all_items'          => __( 'All Books', 'papi-tests' ),
		'search_items'       => __( 'Search Books', 'papi-tests' ),
		'parent_item_colon'  => __( 'Parent Books:', 'papi-tests' ),
		'not_found'          => __( 'No books found.', 'papi-tests' ),
		'not_found_in_trash' => __( 'No books found in Trash.', 'papi-tests' )
	];

	$args = [
		'labels'             => $labels,
		'public'             => true,
		'publicly_queryable' => true,
		'show_ui'            => true,
		'show_in_menu'       => true,
		'query_var'          => true,
		'rewrite'            => ['slug' => 'book'],
		'capability_type'    => 'post',
		'has_archive'        => true,
		'hierarchical'       => false,
		'menu_position'      => null,
		'supports'           => ['title', 'editor', 'author', 'thumbnail', 'excerpt', 'comments']
	];

	$out = register_post_type( 'book', $args );

	if ( ! isset( $wp_post_types['book'] ) ) {
		$wp_post_types['book'] = $out;
	}
}

/**
 * Add custom taxonomy `FAQ taxonomy`.
 */
function papi_test_register_faq_taxonomy() {
	$labels = [
		'name'              => 'FAQ taxonomy',
		'singular_name'     => 'FAQ',
		'search_items'      => 'Search FAQs',
		'all_items'         => 'All FAQs',
		'parent_item'       => 'Parent FAQ',
		'parent_item_colon' => 'Parent FAQ:',
		'edit_item'         => 'Edit FAQ',
		'update_item'       => 'Update FAQ',
		'add_new_item'      => 'Add New FAQ',
		'new_item_name'     => 'New FAQ Name',
		'menu_name'         => 'FAQ taxonomy',
		'view_item'         => 'View FAQ'
	];
	$args = [
		'hierarchical'      => true,
		'labels'            => $labels,
		'show_ui'           => true,
		'show_admin_column' => true,
		'query_var'         => true,
	];

	register_taxonomy( 'faq', ['post'], $args );
}

add_action( 'init', 'papi_test_register_faq_taxonomy' );

/**
 * Register a hidden post type.
 */
function papi_test_register_hidden_post_type() {
	global $wp_post_types;

	$labels = [
		'name'               => _x( 'Hiddens', 'post type general name', 'papi-tests' ),
		'singular_name'      => _x( 'Hidden', 'post type singular name', 'papi-tests' ),
		'menu_name'          => _x( 'Hidden', 'admin menu', 'papi-tests' ),
		'name_admin_bar'     => _x( 'Hidden', 'add new on admin bar', 'papi-tests' ),
		'add_new'            => _x( 'Add New', 'hidden', 'papi-tests' ),
		'add_new_item'       => __( 'Add New Hidden', 'papi-tests' ),
		'new_item'           => __( 'New Hidden', 'papi-tests' ),
		'edit_item'          => __( 'Edit Hidden', 'papi-tests' ),
		'view_item'          => __( 'View Hidden', 'papi-tests' ),
		'all_items'          => __( 'All Hiddens', 'papi-tests' ),
		'search_items'       => __( 'Search Hidden', 'papi-tests' ),
		'parent_item_colon'  => __( 'Parent Hiddens:', 'papi-tests' ),
		'not_found'          => __( 'No hiddens found.', 'papi-tests' ),
		'not_found_in_trash' => __( 'No hiddens found in Trash.', 'papi-tests' )
	];

	$args = [
		'labels'             => $labels,
		'public'             => true,
		'publicly_queryable' => true,
		'show_ui'            => true,
		'show_in_menu'       => false,
		'query_var'          => true,
		'rewrite'            => ['slug' => 'hidden'],
		'capability_type'    => 'post',
		'has_archive'        => true,
		'hierarchical'       => false,
		'menu_position'      => null,
		'supports'           => ['title', 'editor', 'author', 'thumbnail', 'excerpt', 'comments']
	];

	$out = register_post_type( 'hidden', $args );

	if ( ! isset( $wp_post_types['hidden'] ) ) {
		$wp_post_types['hidden'] = $out;
	}
}
