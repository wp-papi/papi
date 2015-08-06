<?php

/**
 * Import data to Papi.
 *
 * @param int $post_id
 * @param object|string $page_type
 * @param array $fields
 *
 * @return bool
 */
function papi_import( $post_id, $page_type, array $fields ) {
    $result = true;

    foreach ( $fields as $slug => $value ) {
        $property = $page_type->get_property( $slug );

        if ( ! papi_is_property( $property ) ) {
            continue;
        }

        $value = $property->import_value( $value, $slug, $post_id );
        // $value = papi_filter_update_value( $property->type, $value, $slug, $post_id );

        $out = papi_update_property_meta_value( [
            'post_id' => $post_id,
            'slug'    => $slug,
            'value'   => $value
        ] );

        $result = $out ? $result : $out;
    }

    return $result;
}

/**
 * Export data from Papi.
 *
 * @param int $post_id
 * @param string $type
 *
 * @return array
 */
function papi_export( $post_id, $type = 'post' ) {
    $slugs = papi_get_slugs( $post_id );

    foreach ( $slugs as $key => $box ) {
        foreach ( $box as $index => $slug ) {
            unset( $slugs[$key][$index] );
            $slugs[$key][$slug] = papi_get_field( $post_id, $slug, null, $type );
        }
    }

    return $slugs;
}
