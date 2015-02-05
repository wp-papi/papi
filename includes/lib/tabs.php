<?php

/**
 * Papi tabs functions.
 *
 * @package Papi
 * @since 1.0.0
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Get tabs options.
 *
 * @param array|object $options
 *
 * @return object
 */

function papi_get_tab_options( $options ) {

	if ( ! is_array( $options ) ) {
		if ( is_object( $options ) ) {
			$options = (array)$options;
		} else {
			return null;
		}
	}

	$defaults = array(
		'capabilities' => array(),
		'icon'         => '',
		'sort_order'   => papi_filter_settings_sort_order(),
		// Private options
		'_name'        => ''
	);

	$options = array_merge( $defaults, $options );

	return (object) $options;
}

/**
 * Setup tabs.
 *
 * @param array $tabs
 *
 * @return array
 */

function papi_setup_tabs( $tabs ) {
	$_tabs = array();

	foreach( $tabs as $tab ) {
		$tab = (object)$tab;

		if ( ! isset( $tab->options ) ) {
			continue;
		}

		$tab->options = papi_get_tab_options( $tab->options );

		if ( papi_current_user_is_allowed( $tab->options->capabilities ) ) {
			$_tabs[] = $tab;
		}
	}

	$tabs = papi_sort_order( $_tabs );

	// Generate unique names for all tabs.
	for ( $i = 0; $i < count( $tabs ); $i ++ ) {

		if ( empty( $tabs[$i] ) ) {
			continue;
		}

		$tabs[ $i ]->options->_name = papi_html_name( $tabs[$i]->options->title ) . '_' . $i;
	}

	return $tabs;
}
