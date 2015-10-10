<?php

/**
 * Get tabs options.
 *
 * @param  array|object $options
 *
 * @return stdClass
 */
function papi_get_tab_options( $options ) {
	if ( ! is_array( $options ) && ! is_object( $options ) || empty( $options ) ) {
		return;
	}

	if ( is_object( $options ) ) {
		$options = (array) $options;
	}

	$defaults = [
		'capabilities' => [],
		'icon'         => '',
		'sort_order'   => papi_filter_settings_sort_order(),
		// Private options
		'_name'        => ''
	];

	$options = array_merge( $defaults, $options );

	return (object) $options;
}

/**
 * Setup tabs.
 *
 * @param  array $tabs
 *
 * @return array
 */
function papi_setup_tabs( array $tabs ) {
	$_tabs = [];

	foreach ( $tabs as $tab ) {
		$tab = (object) $tab;

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
	$len = count( $tabs );
	for ( $i = 0; $i < $len; $i ++ ) {
		$tabs[ $i ]->options->_name = papi_html_name( $tabs[$i]->options->title ) . '_' . $i;
	}

	return $tabs;
}

/**
 * Create a new tab array or rendering a template tab file.
 *
 * @param  string|array $file_or_options
 * @param  array $properties
 *
 * @return array
 */
function papi_tab( $file_or_options, $properties = [] ) {
	list( $options, $properties ) = papi_get_options_and_properties( $file_or_options, $properties, false );

	// The tab key is important, it's says that we should render a tab meta box.
	// This may change in later version of Papi.
	return (object) [
		'options'    => $options,
		'properties' => $properties,
		'tab'        => true
	];
}
