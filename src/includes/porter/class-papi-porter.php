<?php

use Tank\Container;

/**
 * Papi Porter class.
 *
 * @package Papi
 */
class Papi_Porter extends Container {

    /**
     * The constructor.
     */
    public function __construct() {
    }

    /**
     * Export data from Papi.
     *
     * @param mixed $post_id
     *
     * @return array
     */
    public function export( $post_id = null, $type = 'post' ) {
        $post_id = papi_get_post_id( $post_id );

        if ( empty( $post_id ) ) {
            return [];
        }

        $slugs = papi_get_slugs( $post_id );

        foreach ( $slugs as $key => $box ) {
            foreach ( $box as $index => $slug ) {
                unset( $slugs[$key][$index] );
                $slugs[$key][$slug] = papi_get_field( $post_id, $slug, null, $type );
            }
        }

        return $slugs;
    }

    /**
     * Get value that should be saved.
     *
     * @param Papi_Core_Property $property
     * @param mixed $value
     * @param string $slug
     * @param int $post_id
     *
     * @return mixed
     */
    protected function get_value( Papi_Core_Property $property, $value, $slug, $post_id ) {
        $value = $property->import_value( $value, $slug, $post_id );

        if ( $this->exists( $slug ) ) {
            $value = $this->make( $slug, [$value] );
        }

        return $value;
    }

    /**
     * Import data to Papi.
     *
     * @param int $post_id
     * @param object|string $page_type
     * @param array $fields
     *
     * @return bool
     */
    public function import( $post_id = null, $page_type, array $fields = [] ) {
        $post_id = papi_get_post_id( $post_id );

        if ( empty( $post_id ) || empty( $page_type ) || empty( $fields ) ) {
            return false;
        }

        $result = true;

        foreach ( $fields as $slug => $value ) {
            $property = $page_type->get_property( $slug );

            if ( ! papi_is_property( $property ) ) {
                $result = false;
                continue;
            }

            $value = $this->get_value( $property, $value, $slug, $post_id );

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
     * Add special cases for field.
     *
     * @param string $slug
     * @param Closure $closure
     */
    public function with( $slug, $closure ) {
        return $this->bind( $slug, $closure );
    }

}
