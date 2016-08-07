<?php

/**
 * Setup tabs.
 *
 * @param  array $tabs
 *
 * @return array
 */
function papi_tabs_setup( array $tabs ) {
	$_tabs = [];

	foreach ( $tabs as $tab ) {
		if ( $tab instanceof Papi_Core_Tab === false ) {
			continue;
		}

		if ( papi_current_user_is_allowed( $tab->capabilities ) ) {
			$_tabs[] = $tab;
		}
	}

	$tabs = papi_sort_order( $_tabs );

	// Generate unique names for all tabs.
	$len = count( $tabs );
	for ( $i = 0; $i < $len; $i ++ ) {
		$tabs[$i]->id = papi_html_name( $tabs[$i]->title ) . '_' . $i;
	}

	return $tabs;
}

/**
 * Create a new tab array or rendering a template tab file.
 *
 * @param  string|array $file_or_options
 * @param  array $properties
 *
 * @return Papi_Core_Tab
 */
function papi_tab( $file_or_options, $properties = [] ) {
	list( $options, $properties ) = papi_get_options_and_properties( $file_or_options, $properties, false );

	return new Papi_Core_Tab( $options, $properties );
}
