<?php

/**
 * Add custom taxonomy `FAQ taxonomy`.
 */
function ctx_faq_taxonomy() {
	$labels = [
		'name'              => 'FAQ taxonomy',
		'singular_name'     => 'FAQ',
		'search_items'      => 'Search Terms',
		'all_items'         => 'All Terms',
		'parent_item'       => 'Parent Term',
		'parent_item_colon' => 'Parent Term:',
		'edit_item'         => 'Edit Term',
		'update_item'       => 'Update Term',
		'add_new_item'      => 'Add New Term',
		'new_item_name'     => 'New Term Name',
		'menu_name'         => 'FAQ taxonomy',
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

add_action( 'init', 'ctx_faq_taxonomy' );
