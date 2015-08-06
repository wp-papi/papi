<?php

/**
 * Papi Porter Driver Core.
 *
 * @package Papi
 */
class Papi_Porter_Driver_Core extends Papi_Porter_Driver {

    /**
     * The driver name.
     *
     * @var string
     */
    protected $name = 'core';

    /**
     * Get value that should be saved.
     *
     * @param array $options
     *
     * @return mixed
     */
    public function get_value( array $options = [] ) {
        $post_id  = $options['post_id'];
        $property = $options['property'];
        $slug     = $options['slug'];


        $value = $this->call_value( $options['value'] );
        $value = $property->import_value( $value, $slug, $post_id );

        // todo make so flexible + repeater works
        if ( $this->should_update_array( $slug ) ) {
            $value = $this->update_array_value( $property, $value, $slug, $post_id );
        }

        return $value;
    }

    /**
     * Update array value.
     *
     * @param Papi_Core_Property $property
     * @param mixed $value
     * @param string $slug
     * @param int $post_id
     *
     * @return array
     */
    protected function update_array_value( $property, $value, $slug, $post_id ) {
        $old   = papi_get_field( $post_id, $slug, [] );
        $value = array_merge( $old, $value );

        return $property->update_value( $value, $slug, $post_id );
    }

}
